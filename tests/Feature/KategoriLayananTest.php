<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\KategoriLayanan;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Struktur layanan kategori + jenis pekerjaan dan data usaha (datausaha.md).
 */
class KategoriLayananTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private function payload(array $items): array
    {
        return [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Dusun Grogol, Babat',
            'schedule_date' => now()->addDays(2)->toDateString(),
            'sesi' => 'pagi',
            'items' => $items,
        ];
    }

    // ---------------------------------------------------------------
    // Seeder
    // ---------------------------------------------------------------

    public function test_seeder_creates_five_categories_and_sixteen_jobs(): void
    {
        $this->seed(ServiceSeeder::class);

        $this->assertSame(5, KategoriLayanan::count());
        $this->assertSame(16, Service::count());
        $this->assertSame(0, Service::where('price', '>', 0)->count(), 'Harga masih TODO, harus 0.');

        $jumlahPerKategori = KategoriLayanan::withCount('services')->orderBy('urutan')->pluck('services_count', 'nama')->all();
        $this->assertSame([
            'Cleaning AC' => 3,
            'Pasang AC (Lokasi Standar)' => 5,
            'Instalasi Listrik' => 1,
            'Tambah Freon' => 4,
            'Service dan Perbaikan AC' => 3,
        ], $jumlahPerKategori);

        $this->assertSame('meter', Service::where('slug', 'instalasi-pipa-lebih-dari-3-meter')->value('satuan'));
        $this->assertNull(KategoriLayanan::where('slug', 'cleaning-ac')->value('catatan_harga'));
        $this->assertNotNull(KategoriLayanan::where('slug', 'service-dan-perbaikan-ac')->value('catatan_harga'));
    }

    public function test_reseeding_does_not_duplicate_or_overwrite_admin_prices(): void
    {
        $this->seed(ServiceSeeder::class);
        Service::where('slug', 'cleaning-ac-split-05-1-pk')->update(['price' => 80000]);

        $this->seed(ServiceSeeder::class);

        $this->assertSame(16, Service::count());
        $this->assertSame(80000, Service::where('slug', 'cleaning-ac-split-05-1-pk')->value('price'));
    }

    // ---------------------------------------------------------------
    // Layanan berharga 0
    // ---------------------------------------------------------------

    public function test_zero_price_services_are_hidden_from_landing_page_and_order_form(): void
    {
        $kategori = KategoriLayanan::factory()->create(['nama' => 'Cleaning AC']);
        Service::factory()->for($kategori, 'kategori')->create(['name' => 'Pekerjaan Berharga', 'price' => 70000]);
        Service::factory()->for($kategori, 'kategori')->create(['name' => 'Pekerjaan Belum Berharga', 'price' => 0]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pekerjaan Berharga')
            ->assertDontSee('Pekerjaan Belum Berharga');

        $this->get(route('order.create'))
            ->assertOk()
            ->assertSee('Pekerjaan Berharga')
            ->assertDontSee('Pekerjaan Belum Berharga');
    }

    public function test_landing_page_shows_fallback_when_no_price_is_filled_yet(): void
    {
        $this->seed(ServiceSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Daftar harga sedang diperbarui.')
            ->assertDontSee('Rp 0');
    }

    public function test_order_for_zero_price_service_is_rejected(): void
    {
        $service = Service::factory()->create(['price' => 0]);

        $this->post(route('order.store'), $this->payload([
            ['kategori_id' => $service->kategori_layanan_id, 'service_id' => $service->id, 'qty' => 1],
        ]))->assertSessionHasErrors('items.0.service_id');

        $this->assertDatabaseCount('orders', 0);
    }

    // ---------------------------------------------------------------
    // Kategori ↔ jenis pekerjaan
    // ---------------------------------------------------------------

    public function test_order_form_groups_jobs_by_their_category(): void
    {
        $cleaning = KategoriLayanan::factory()->create(['nama' => 'Cleaning AC', 'urutan' => 1]);
        $freon = KategoriLayanan::factory()->create(['nama' => 'Tambah Freon', 'urutan' => 2]);
        $a = Service::factory()->for($cleaning, 'kategori')->create(['price' => 70000]);
        $b = Service::factory()->for($freon, 'kategori')->create(['price' => 150000]);

        $this->get(route('order.create'))
            ->assertOk()
            ->assertViewHas('katalog', function ($katalog) use ($cleaning, $freon, $a, $b) {
                $byKategori = $katalog->keyBy('id');

                return $byKategori[$cleaning->id]['pekerjaan']->pluck('id')->all() === [$a->id]
                    && $byKategori[$freon->id]['pekerjaan']->pluck('id')->all() === [$b->id];
            });
    }

    public function test_backend_rejects_job_from_another_category(): void
    {
        $cleaning = KategoriLayanan::factory()->create();
        $freon = KategoriLayanan::factory()->create();
        $jobFreon = Service::factory()->for($freon, 'kategori')->create(['price' => 150000]);

        $this->post(route('order.store'), $this->payload([
            ['kategori_id' => $cleaning->id, 'service_id' => $jobFreon->id, 'qty' => 1],
        ]))->assertSessionHasErrors(['items.0.service_id' => 'Jenis pekerjaan tidak sesuai dengan kategori yang dipilih.']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_category_is_required(): void
    {
        $service = Service::factory()->create(['price' => 70000]);

        $this->post(route('order.store'), $this->payload([
            ['service_id' => $service->id, 'qty' => 1],
        ]))->assertSessionHasErrors('items.0.kategori_id');
    }

    // ---------------------------------------------------------------
    // Satuan meter
    // ---------------------------------------------------------------

    public function test_pipe_per_meter_total_is_price_times_length_plus_unique_code(): void
    {
        $kategori = KategoriLayanan::factory()->create(['nama' => 'Pasang AC (Lokasi Standar)']);
        $pipa = Service::factory()->for($kategori, 'kategori')->perMeter()->create([
            'name' => 'Instalasi pipa lebih dari 3 meter',
            'price' => 85000,
        ]);

        $this->post(route('order.store'), $this->payload([
            ['kategori_id' => $kategori->id, 'service_id' => $pipa->id, 'qty' => 4],
        ]))->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame(85000 * 4, $order->subtotal);
        $this->assertSame(85000 * 4 + $order->unique_code, $order->total);
        $this->assertSame('Pasang AC (Lokasi Standar) – Instalasi pipa lebih dari 3 meter', $order->items->first()->service_name);

        $this->assertSame('Rp 85.000 / meter', $pipa->priceLabel());
        $this->assertSame('Panjang pipa (meter)', $pipa->qtyLabel());

        $this->get(route('payment.show', $order->code))->assertOk()->assertSee('4 meter');
    }

    public function test_starting_price_order_shows_extra_cost_note_on_payment_page(): void
    {
        $kategori = KategoriLayanan::factory()->create(['catatan_harga' => 'Tarif menyesuaikan kondisi instalasi di lokasi.']);
        $service = Service::factory()->for($kategori, 'kategori')->create(['price' => 100000, 'harga_mulai' => true]);

        $this->post(route('order.store'), $this->payload([
            ['kategori_id' => $kategori->id, 'service_id' => $service->id, 'qty' => 1],
        ]));

        $this->get(route('payment.show', Order::firstOrFail()->code))
            ->assertOk()
            ->assertSee('biayanya bisa bertambah di lokasi')
            ->assertSee('Tarif menyesuaikan kondisi instalasi di lokasi.');
    }

    // ---------------------------------------------------------------
    // Pesanan lama
    // ---------------------------------------------------------------

    public function test_old_orders_with_legacy_services_still_open_in_admin_and_status_page(): void
    {
        $legacy = Service::factory()->legacy()->create(['name' => 'Cuci AC Split (0.5 - 2 PK)', 'price' => 65000]);
        $order = Order::factory()->create(['status' => OrderStatus::DISETUJUI]);
        $order->items()->create([
            'service_id' => $legacy->id,
            'service_name' => 'Cuci AC Split (0.5 - 2 PK)',
            'qty' => 2,
            'price_at_order' => 65000,
            'subtotal' => 130000,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Cuci AC Split (0.5 - 2 PK)');

        $this->get(route('order.status', $order->code))
            ->assertOk()
            ->assertSee('Cuci AC Split (0.5 - 2 PK)');

        // Layanan lama tidak bisa dipesan lagi.
        $this->assertFalse(Service::bookable()->whereKey($legacy->id)->exists());
    }

    // ---------------------------------------------------------------
    // Admin: Kelola Layanan & Harga
    // ---------------------------------------------------------------

    public function test_admin_page_groups_by_category_and_flags_missing_prices(): void
    {
        $this->seed(ServiceSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSeeInOrder(['Cleaning AC', 'Pasang AC (Lokasi Standar)', 'Instalasi Listrik', 'Tambah Freon', 'Service dan Perbaikan AC'])
            ->assertSee('Harga belum diisi');
    }

    public function test_admin_can_update_job_price_unit_and_order(): void
    {
        $service = Service::factory()->create(['price' => 0, 'satuan' => 'unit', 'sort_order' => 1]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.services.update', $service), [
                'price' => 50000,
                'satuan' => 'meter',
                'sort_order' => 7,
                'is_active' => '1',
            ])
            ->assertSessionHas('success');

        $service->refresh();
        $this->assertSame(50000, $service->price);
        $this->assertSame('meter', $service->satuan);
        $this->assertSame(7, $service->sort_order);
        $this->assertFalse($service->harga_mulai);
        $this->assertTrue(Service::bookable()->whereKey($service->id)->exists());
    }

    public function test_admin_can_save_all_job_prices_at_once(): void
    {
        $a = Service::factory()->create(['price' => 0, 'satuan' => 'unit', 'sort_order' => 1, 'is_active' => true, 'harga_mulai' => false]);
        $b = Service::factory()->create(['price' => 0, 'satuan' => 'unit', 'sort_order' => 2, 'is_active' => true, 'harga_mulai' => true]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.services.bulk-update'), [
                'services' => [
                    $a->id => ['price' => 75000, 'satuan' => 'unit', 'sort_order' => 1, 'harga_mulai' => '0', 'is_active' => '1'],
                    $b->id => ['price' => 25000, 'satuan' => 'meter', 'sort_order' => 5, 'harga_mulai' => '0', 'is_active' => '0'],
                ],
            ])
            ->assertSessionHas('success', '2 jenis pekerjaan berhasil diperbarui.');

        $a->refresh();
        $b->refresh();
        $this->assertSame(75000, $a->price);
        $this->assertTrue($a->is_active);
        $this->assertSame(25000, $b->price);
        $this->assertSame('meter', $b->satuan);
        $this->assertSame(5, $b->sort_order);
        $this->assertFalse($b->harga_mulai);
        $this->assertFalse($b->is_active);
    }

    public function test_bulk_save_rejects_invalid_price_and_changes_nothing(): void
    {
        $a = Service::factory()->create(['price' => 0, 'satuan' => 'unit', 'sort_order' => 1]);
        $b = Service::factory()->create(['price' => 0, 'satuan' => 'unit', 'sort_order' => 2]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.services.bulk-update'), [
                'services' => [
                    $a->id => ['price' => 75000, 'satuan' => 'unit', 'sort_order' => 1],
                    $b->id => ['price' => -5, 'satuan' => 'unit', 'sort_order' => 2],
                ],
            ])
            ->assertSessionHasErrors("services.{$b->id}.price");

        $this->assertSame(0, $a->fresh()->price);
    }

    public function test_admin_can_add_job_to_category_and_edit_category_note(): void
    {
        $kategori = KategoriLayanan::factory()->create(['slug' => 'tambah-freon']);
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.services.store', $kategori), [
                'name' => 'Isi Freon R22',
                'satuan' => 'unit',
                'price' => 200000,
                'harga_mulai' => '1',
            ])
            ->assertSessionHas('success');

        $baru = Service::where('name', 'Isi Freon R22')->firstOrFail();
        $this->assertSame($kategori->id, $baru->kategori_layanan_id);
        $this->assertTrue($baru->harga_mulai);
        $this->assertSame('tambah-freon-isi-freon-r22', $baru->slug);

        $this->actingAs($admin)
            ->put(route('admin.kategori.update', $kategori), ['catatan_harga' => 'Catatan baru.'])
            ->assertSessionHas('success');
        $this->assertSame('Catatan baru.', $kategori->fresh()->catatan_harga);
    }

    public function test_guest_cannot_manage_services(): void
    {
        $kategori = KategoriLayanan::factory()->create();

        $this->post(route('admin.services.store', $kategori), ['name' => 'X', 'satuan' => 'unit', 'price' => 1])
            ->assertRedirect();
        $this->put(route('admin.kategori.update', $kategori), ['catatan_harga' => 'X'])
            ->assertRedirect();
        $this->put(route('admin.services.bulk-update'), ['services' => []])
            ->assertRedirect();

        $this->assertDatabaseCount('services', 0);
        $this->assertNull($kategori->fresh()->catatan_harga);
    }

    // ---------------------------------------------------------------
    // Data usaha
    // ---------------------------------------------------------------

    public function test_contact_page_and_footer_show_business_contact_as_text(): void
    {
        $alamat = 'Dusun Grogol RT 04 RW 02, Desa Tritunggal, Kecamatan Babat, Kabupaten Lamongan';

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('0852-3285-5466')
            ->assertSee('idhwanmuhammad1703@gmail.com')
            ->assertSee($alamat)
            ->assertDontSee('Malang');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('0852-3285-5466')
            ->assertSee('idhwanmuhammad1703@gmail.com')
            ->assertSee($alamat)
            ->assertSee('https://wa.me/6285232855466', false)
            ->assertDontSee('Malang');
    }
}
