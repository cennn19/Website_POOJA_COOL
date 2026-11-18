<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\UniqueCodeExhaustedException;
use App\Models\KategoriLayanan;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Services\UniqueCodeAllocator;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderFlowFixesTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('booking.kuota_per_sesi', 3);
        $this->service = Service::factory()->create(['price' => 100000, 'is_active' => true]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5, Lowokwaru, Malang',
            'schedule_date' => now()->addDays(2)->toDateString(),
            'sesi' => 'pagi',
            'items' => [['kategori_id' => $this->service->kategori_layanan_id, 'service_id' => $this->service->id, 'qty' => 1]],
        ], $overrides);
    }

    /**
     * Isi kode_unik_aktif secara massal tanpa lewat factory (cepat).
     *
     * @param  array<int, int>  $codes
     */
    private function occupyCodes(array $codes, string $status = 'menunggu_pembayaran'): void
    {
        $now = now();
        $rows = array_map(fn (int $code) => [
            'code' => 'HVC-T'.str_pad((string) $code, 6, '0', STR_PAD_LEFT),
            'name' => 'Isi',
            'phone' => '0800000000',
            'address' => 'Alamat',
            'schedule_date' => now()->addDays(20)->toDateString(),
            'sesi' => null,
            'subtotal' => 1000,
            'unique_code' => $code,
            'kode_unik_aktif' => $status === 'menunggu_pembayaran' ? $code : null,
            'total' => 1000 + $code,
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ], $codes);

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('orders')->insert($chunk);
        }
    }

    // ---------------------------------------------------------------
    // 1. Kode unik
    // ---------------------------------------------------------------

    public function test_allocator_never_picks_a_code_used_by_an_active_order(): void
    {
        $this->occupyCodes(array_values(array_diff(range(1, 999), [500])));

        $this->assertSame(500, app(UniqueCodeAllocator::class)->pick());

        $this->post(route('order.store'), $this->payload())->assertSessionHasNoErrors();
        $this->assertSame(500, Order::where('name', 'Budi Santoso')->first()->unique_code);
    }

    public function test_codes_from_finished_or_cancelled_orders_can_be_reused(): void
    {
        $this->occupyCodes([123], 'selesai');
        $this->occupyCodes([456], 'dibatalkan');
        $this->occupyCodes(array_values(array_diff(range(1, 999), [123, 456])));

        $picked = [app(UniqueCodeAllocator::class)->pick()];
        $this->assertContains($picked[0], [123, 456]);
    }

    public function test_database_rejects_two_active_orders_with_the_same_code(): void
    {
        $this->occupyCodes([77]);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->occupyCodes([77]);
    }

    public function test_exhausted_codes_return_a_clear_error_instead_of_500(): void
    {
        $this->occupyCodes(range(1, 999));

        $this->expectException(UniqueCodeExhaustedException::class);
        app(UniqueCodeAllocator::class)->pick();
    }

    public function test_order_form_shows_message_when_codes_are_exhausted(): void
    {
        $this->occupyCodes(range(1, 999));

        $response = $this->post(route('order.store'), $this->payload());

        $response->assertRedirect();
        $response->assertSessionHasErrors('items');
        $this->assertDatabaseMissing('orders', ['name' => 'Budi Santoso']);
    }

    public function test_cancelling_or_completing_releases_the_active_code(): void
    {
        $admin = User::factory()->create();
        $a = Order::factory()->create(['unique_code' => 11, 'kode_unik_aktif' => 11]);
        $b = Order::factory()->create(['unique_code' => 12, 'kode_unik_aktif' => 12, 'status' => OrderStatus::DISETUJUI]);

        $a->cancel($admin, 'Tes');
        $b->complete($admin);

        $this->assertNull($a->fresh()->kode_unik_aktif);
        $this->assertNull($b->fresh()->kode_unik_aktif);
    }

    // ---------------------------------------------------------------
    // 2. Dibatalkan & kedaluwarsa
    // ---------------------------------------------------------------

    public function test_expire_command_cancels_only_unpaid_orders_older_than_24_hours(): void
    {
        $old = Order::factory()->create(['created_at' => now()->subHours(25), 'kode_unik_aktif' => 1, 'unique_code' => 1]);
        $recent = Order::factory()->create(['created_at' => now()->subHours(23)]);

        $paid = Order::factory()->create([
            'created_at' => now()->subHours(30),
            'status' => OrderStatus::MENUNGGU_VERIFIKASI,
        ]);
        $paid->payments()->create(['proof_path' => 'p.jpg', 'status' => PaymentStatus::PENDING]);

        // Kasus tepi: status masih menunggu pembayaran tetapi sudah ada bukti bayar.
        $hasProof = Order::factory()->create(['created_at' => now()->subHours(30)]);
        $hasProof->payments()->create(['proof_path' => 'q.jpg', 'status' => PaymentStatus::PENDING]);

        $this->artisan('orders:expire')->assertSuccessful();

        $old->refresh();
        $this->assertEquals(OrderStatus::DIBATALKAN, $old->status);
        $this->assertSame('Kedaluwarsa: tidak dibayar dalam 24 jam', $old->alasan_batal);
        $this->assertNotNull($old->dibatalkan_pada);
        $this->assertNull($old->kode_unik_aktif);

        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $recent->fresh()->status);
        $this->assertEquals(OrderStatus::MENUNGGU_VERIFIKASI, $paid->fresh()->status);
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $hasProof->fresh()->status);
    }

    public function test_expire_command_is_scheduled_hourly(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('orders:expire');
    }

    public function test_admin_can_cancel_with_reason_and_reason_is_required(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.orders.cancel', $order), ['alasan_batal' => ''])
            ->assertSessionHasErrors('alasan_batal');
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $order->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.orders.cancel', $order), ['alasan_batal' => 'Pelanggan minta batal'])
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(OrderStatus::DIBATALKAN, $order->status);
        $this->assertSame('Pelanggan minta batal', $order->alasan_batal);
        $this->assertDatabaseHas('order_logs', ['order_id' => $order->id, 'action' => 'dibatalkan', 'user_id' => $admin->id]);
    }

    public function test_finished_order_cannot_be_cancelled(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::SELESAI]);

        $this->actingAs($admin)
            ->post(route('admin.orders.cancel', $order), ['alasan_batal' => 'Coba batal'])
            ->assertSessionHas('error');

        $this->assertEquals(OrderStatus::SELESAI, $order->fresh()->status);

        $this->expectException(DomainException::class);
        $order->fresh()->cancel($admin, 'Coba lagi');
    }

    public function test_status_page_shows_cancellation_and_reason(): void
    {
        $order = Order::factory()->create();
        $order->cancel(null, 'Kedaluwarsa: tidak dibayar dalam 24 jam');

        $this->get(route('order.status', ['code' => $order->code]))
            ->assertOk()
            ->assertSee('Pesanan Dibatalkan')
            ->assertSee('Kedaluwarsa: tidak dibayar dalam 24 jam');
    }

    public function test_cancelled_order_cannot_upload_payment_proof(): void
    {
        $order = Order::factory()->create();
        $order->cancel(null, 'Batal');

        $this->assertFalse($order->fresh()->canUploadProof());
        $this->get(route('payment.show', ['code' => $order->code]))
            ->assertOk()
            ->assertSee('Pesanan Dibatalkan')
            ->assertDontSee('KIRIM BUKTI PEMBAYARAN');
    }

    public function test_admin_list_hides_cancelled_orders_by_default(): void
    {
        $admin = User::factory()->create();
        $active = Order::factory()->create(['code' => 'HVC-AKTIF001']);
        $cancelled = Order::factory()->create(['code' => 'HVC-BATAL001']);
        $cancelled->cancel($admin, 'Batal');

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertSee('HVC-AKTIF001')
            ->assertDontSee('HVC-BATAL001');

        $this->actingAs($admin)->get(route('admin.orders.index', ['tampilkan_batal' => 1]))
            ->assertSee('HVC-AKTIF001')
            ->assertSee('HVC-BATAL001');

        $this->actingAs($admin)->get(route('admin.orders.index', ['status' => 'dibatalkan']))
            ->assertDontSee('HVC-AKTIF001')
            ->assertSee('HVC-BATAL001');
    }

    // ---------------------------------------------------------------
    // 3. Sesi, kuota, batas waktu, ubah jadwal
    // ---------------------------------------------------------------

    public function test_full_session_rejects_fourth_order(): void
    {
        $date = now()->addDays(3)->toDateString();
        Order::factory()->count(3)->create(['schedule_date' => $date, 'sesi' => 'siang']);

        $this->post(route('order.store'), $this->payload(['schedule_date' => $date, 'sesi' => 'siang']))
            ->assertSessionHasErrors('sesi');

        $this->assertSame(3, Order::whereDate('schedule_date', $date)->where('sesi', 'siang')->count());

        // Sesi lain di tanggal yang sama masih bisa.
        $this->post(route('order.store'), $this->payload(['schedule_date' => $date, 'sesi' => 'pagi']))
            ->assertSessionHasNoErrors();
    }

    public function test_cancelled_orders_do_not_count_toward_quota(): void
    {
        $date = now()->addDays(3)->toDateString();
        $orders = Order::factory()->count(3)->create(['schedule_date' => $date, 'sesi' => 'pagi']);
        $orders->first()->cancel(null, 'Batal');

        $this->post(route('order.store'), $this->payload(['schedule_date' => $date, 'sesi' => 'pagi']))
            ->assertSessionHasNoErrors();
    }

    public function test_afternoon_session_today_is_rejected_after_ten(): void
    {
        $this->travelTo(now()->setTime(10, 30));

        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->toDateString(), 'sesi' => 'siang']))
            ->assertSessionHasErrors('sesi');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_afternoon_session_today_is_allowed_before_ten(): void
    {
        $this->travelTo(now()->setTime(9, 30));

        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->toDateString(), 'sesi' => 'siang']))
            ->assertSessionHasNoErrors();
    }

    public function test_morning_session_today_is_always_rejected(): void
    {
        $this->travelTo(now()->setTime(6, 0));

        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->toDateString(), 'sesi' => 'pagi']))
            ->assertSessionHasErrors('sesi');
    }

    public function test_dates_in_the_past_or_beyond_thirty_days_are_rejected(): void
    {
        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors('schedule_date');

        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->addDays(31)->toDateString()]))
            ->assertSessionHasErrors('schedule_date');

        $this->post(route('order.store'), $this->payload(['schedule_date' => now()->addDays(30)->toDateString()]))
            ->assertSessionHasNoErrors();
    }

    public function test_session_is_required(): void
    {
        $this->post(route('order.store'), $this->payload(['sesi' => null]))->assertSessionHasErrors('sesi');
        $this->post(route('order.store'), $this->payload(['sesi' => 'malam']))->assertSessionHasErrors('sesi');
    }

    public function test_admin_reschedule_checks_quota_unless_forced_and_logs_old_schedule(): void
    {
        $admin = User::factory()->create();
        $target = now()->addDays(5)->toDateString();
        Order::factory()->count(3)->create(['schedule_date' => $target, 'sesi' => 'pagi']);

        $order = Order::factory()->create(['schedule_date' => now()->addDays(2)->toDateString(), 'sesi' => 'siang']);
        $oldDate = $order->schedule_date->toDateString();

        $this->actingAs($admin)
            ->post(route('admin.orders.reschedule', $order), ['jadwal_tanggal' => $target, 'jadwal_sesi' => 'pagi'])
            ->assertSessionHas('error');
        $this->assertSame($oldDate, $order->fresh()->schedule_date->toDateString());

        $this->actingAs($admin)
            ->post(route('admin.orders.reschedule', $order), ['jadwal_tanggal' => $target, 'jadwal_sesi' => 'pagi', 'paksa' => '1'])
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertSame($target, $order->schedule_date->toDateString());
        $this->assertSame('pagi', $order->sesi);

        $log = $order->logs()->where('action', 'ubah_jadwal')->first();
        $this->assertNotNull($log);
        $this->assertSame($oldDate, $log->meta['jadwal_lama']['tanggal']);
        $this->assertSame('siang', $log->meta['jadwal_lama']['sesi']);
        $this->assertTrue($log->meta['paksa']);
    }

    public function test_order_page_marks_full_session_as_unavailable(): void
    {
        $date = now()->addDays(4)->toDateString();
        Order::factory()->count(3)->create(['schedule_date' => $date, 'sesi' => 'pagi']);

        $response = $this->get(route('order.create'));

        $response->assertOk();
        $availability = $response->viewData('availability');
        $this->assertFalse($availability[$date]['pagi']['available']);
        $this->assertSame('Penuh', $availability[$date]['pagi']['reason']);
        $this->assertTrue($availability[$date]['siang']['available']);
    }

    // ---------------------------------------------------------------
    // 4. Harga "Mulai dari"
    // ---------------------------------------------------------------

    public function test_starting_price_services_show_mulai_dari_and_category_note(): void
    {
        $kategori = KategoriLayanan::factory()->create([
            'catatan_harga' => 'Tarif menyesuaikan tingkat kerusakan.',
        ]);
        Service::factory()->for($kategori, 'kategori')->create(['name' => 'Perbaikan Uji', 'price' => 75000, 'harga_mulai' => true, 'is_active' => true]);

        $this->get(route('order.create'))
            ->assertSee('Mulai dari Rp 75.000')
            ->assertSee('Tarif menyesuaikan tingkat kerusakan.');

        $this->get(route('home'))
            ->assertSee('Mulai dari Rp 75.000')
            ->assertSee('Tarif menyesuaikan tingkat kerusakan.');
    }

    public function test_admin_can_toggle_starting_price(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.services.index'))->assertOk()->assertSee($this->service->name);

        $this->actingAs($admin)
            ->put(route('admin.services.update', $this->service), ['price' => 90000, 'harga_mulai' => '1', 'is_active' => '1'])
            ->assertSessionHas('success');

        $this->service->refresh();
        $this->assertTrue($this->service->harga_mulai);
        $this->assertSame(90000, $this->service->price);
        $this->assertSame('Mulai dari Rp 90.000', $this->service->priceLabel());
    }

    // ---------------------------------------------------------------
    // 5. Data contoh
    // ---------------------------------------------------------------

    public function test_home_has_no_fabricated_stats_or_testimonials_and_reads_business_config(): void
    {
        config()->set('usaha.jam_operasional', 'Senin–Sabtu, 08.00–17.00 WIB');

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('250+')
            ->assertDontSee('98%')
            ->assertDontSee('TESTIMONI PELANGGAN')
            ->assertSee('Senin–Sabtu, 08.00–17.00 WIB');
    }
}
