<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SecurityAndBugFixesTest extends TestCase
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
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Melati No. 5, Lowokwaru, Malang',
            'schedule_date' => now()->addDays(2)->toDateString(),
            'sesi' => 'pagi',
            'items' => [$this->item()],
        ], $overrides);
    }

    /**
     * @return array<string, int>
     */
    private function item(int $qty = 1): array
    {
        return ['kategori_id' => $this->service->kategori_layanan_id, 'service_id' => $this->service->id, 'qty' => $qty];
    }

    // ---------------------------------------------------------------
    // 1. Form pesanan tetap utuh setelah validasi gagal
    // ---------------------------------------------------------------

    public function test_order_form_alpine_data_survives_old_input_with_quotes_newlines_and_items(): void
    {
        $this->withSession(['_old_input' => [
            'name' => 'Budi "Bos" O\'Neil',
            'address' => "Jl. Ma'arif No. 5\nRT 02 / RW 03",
            'notes' => '</div><script>alert(1)</script>',
            'items' => [['kategori_id' => (string) $this->service->kategori_layanan_id, 'service_id' => (string) $this->service->id, 'qty' => '2']],
        ]]);

        $html = $this->get(route('order.create'))->assertOk()->getContent();

        // Atribut x-data utama harus utuh sampai akhir (tidak terpotong tanda kutip dari input).
        $this->assertMatchesRegularExpression('/x-data="\{\s*katalog:.*canSubmit\(\).*"/s', $html);
        preg_match('/x-data="(\{\s*katalog:.*?)"\s*>/s', $html, $match);
        $js = html_entity_decode($match[1] ?? '', ENT_QUOTES);

        $this->assertStringContainsString('canSubmit()', $js);
        $this->assertStringNotContainsString("Ma'arif", $js, 'Apostrof harus di-escape untuk JavaScript.');
        $this->assertStringNotContainsString("No. 5\nRT", $js, 'Baris baru mentah membuat string JavaScript error.');
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_order_form_ignores_manipulated_array_old_input(): void
    {
        $this->withSession(['_old_input' => ['name' => ['x' => 'y'], 'items' => 'bukan-array']]);

        $this->get(route('order.create'))->assertOk();
    }

    // ---------------------------------------------------------------
    // 2. Batas jumlah item & duplikat
    // ---------------------------------------------------------------

    public function test_duplicate_service_rows_are_rejected(): void
    {
        $this->post(route('order.store'), $this->payload(['items' => [$this->item(20), $this->item(20)]]))
            ->assertSessionHasErrors('items.1.service_id');

        $this->assertSame(0, Order::count());
    }

    public function test_huge_item_lists_are_rejected_cheaply(): void
    {
        $items = array_fill(0, 3000, $this->item(20));

        DB::enableQueryLog();
        $this->post(route('order.store'), $this->payload(['items' => $items]))
            ->assertSessionHasErrors('items');
        $queries = count(DB::getQueryLog());

        $this->assertSame(0, Order::count());
        $this->assertLessThan(100, $queries, 'Daftar item raksasa tidak boleh memicu ribuan query.');
    }

    // ---------------------------------------------------------------
    // 3. Validasi nomor HP
    // ---------------------------------------------------------------

    public function test_phone_without_enough_digits_is_rejected(): void
    {
        foreach (['---', '()', '+62', '0812'] as $phone) {
            $this->post(route('order.store'), $this->payload(['phone' => $phone]))
                ->assertSessionHasErrors('phone');
        }

        $this->post(route('order.store'), $this->payload(['phone' => '+62 812-3456-7890']))
            ->assertSessionHasNoErrors();
    }

    // ---------------------------------------------------------------
    // 4. Riwayat pesanan lengkap
    // ---------------------------------------------------------------

    public function test_every_status_change_is_logged(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Satu']);
        $order = Order::factory()->create();

        $order->recordPaymentProof('payments/a.jpg', 'qris');
        $order->reject($admin, 'Nominal kurang');
        $order->recordPaymentProof('payments/b.jpg', 'qris');
        $order->approve($admin);
        $order->complete($admin);

        $actions = $order->logs()->reorder('id')->pluck('action')->all();
        $this->assertSame(['bukti_diunggah', 'ditolak', 'bukti_diunggah', 'disetujui', 'selesai'], $actions);

        $logs = $order->logs()->reorder('id')->get();
        $this->assertNull($logs[0]->user_id);
        $this->assertStringContainsString('mengunggah bukti', $logs[0]->description);
        $this->assertStringContainsString('mengunggah ulang', $logs[2]->description);
        $this->assertSame($admin->id, $logs[1]->user_id);
        $this->assertStringContainsString('Nominal kurang', $logs[1]->description);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Pelanggan')
            ->assertSee('Pesanan ditandai selesai.');
    }

    // ---------------------------------------------------------------
    // 5. Pesanan ditolak tanpa unggah ulang ikut kedaluwarsa
    // ---------------------------------------------------------------

    private function rejectedOrder(int $hoursAgo): Order
    {
        $order = Order::factory()->create(['created_at' => now()->subHours($hoursAgo + 1)]);
        $order->recordPaymentProof('payments/x.jpg', 'qris');

        $this->travel(-$hoursAgo)->hours();
        $order->reject(User::factory()->create(), 'Bukti buram');
        $this->travelBack();

        return $order->fresh();
    }

    public function test_rejected_order_without_reupload_expires_after_24_hours(): void
    {
        $old = $this->rejectedOrder(25);
        $recent = $this->rejectedOrder(2);

        $this->artisan('orders:expire')->assertSuccessful();

        $old->refresh();
        $this->assertEquals(OrderStatus::DIBATALKAN, $old->status);
        $this->assertStringContainsString('tidak diunggah ulang', $old->alasan_batal);
        $this->assertNull($old->kode_unik_aktif);

        $this->assertEquals(OrderStatus::DITOLAK, $recent->fresh()->status);
    }

    public function test_rejected_order_that_was_reuploaded_does_not_expire(): void
    {
        $order = $this->rejectedOrder(25);
        $order->recordPaymentProof('payments/y.jpg', 'qris');

        $this->artisan('orders:expire')->assertSuccessful();

        $this->assertEquals(OrderStatus::MENUNGGU_VERIFIKASI, $order->fresh()->status);
    }

    public function test_rejected_order_frees_session_quota_after_expiry(): void
    {
        $date = now()->addDays(4)->toDateString();
        $rejected = $this->rejectedOrder(25);
        $rejected->update(['schedule_date' => $date, 'sesi' => 'siang']);
        Order::factory()->count(2)->create(['schedule_date' => $date, 'sesi' => 'siang']);

        $this->post(route('order.store'), $this->payload(['schedule_date' => $date, 'sesi' => 'siang']))
            ->assertSessionHasNoErrors();
    }

    public function test_status_and_payment_pages_show_reupload_deadline(): void
    {
        $order = $this->rejectedOrder(2);

        $this->get(route('order.status', $order->code))->assertOk()->assertSee('Unggah ulang paling lambat');
        $this->get(route('payment.show', $order->code))->assertOk()->assertSee('Batas unggah ulang');
    }

    // ---------------------------------------------------------------
    // 6. Throttle halaman berbasis kode pesanan
    // ---------------------------------------------------------------

    public function test_status_lookup_is_rate_limited(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->get(route('order.status', 'HVC-TEBAK'.$i))->assertOk();
        }

        $this->get(route('order.status', 'HVC-TEBAK99'))->assertStatus(429);
    }

    public function test_payment_page_lookup_is_rate_limited(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->get(route('payment.show', 'HVC-TEBAK'.$i))->assertNotFound();
        }

        $this->get(route('payment.show', 'HVC-TEBAK99'))->assertStatus(429);
    }

    public function test_browsing_status_pages_does_not_use_up_the_order_and_upload_limits(): void
    {
        // Ditemukan saat uji coba di browser: tanpa prefix, semua throttle berbagi satu
        // penghitung per IP sehingga pelanggan kena 429 saat membuat pesanan / unggah ulang.
        $order = Order::factory()->create();

        for ($i = 0; $i < 25; $i++) {
            $this->get(route('order.status', $order->code))->assertOk();
        }

        $this->post(route('order.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('payment.upload', $order->code), [])->assertStatus(302);
        $this->post(route('admin.login.store'), ['email' => 'x@y.z', 'password' => 'salah'])->assertStatus(302);
    }

    // ---------------------------------------------------------------
    // 10. Aksi bersamaan memakai data terbaru
    // ---------------------------------------------------------------

    public function test_stale_admin_action_cannot_override_a_newer_decision(): void
    {
        $order = Order::factory()->create();
        $order->recordPaymentProof('payments/a.jpg', 'qris');

        // Dua admin membuka halaman yang sama (data sama-sama "menunggu verifikasi").
        $tabAdminA = Order::find($order->id);
        $tabAdminB = Order::find($order->id);

        $tabAdminA->reject(User::factory()->create(), 'Nominal salah');

        $this->expectException(DomainException::class);
        $tabAdminB->approve(User::factory()->create());
    }

    public function test_auto_expiry_skips_order_that_got_paid_in_the_meantime(): void
    {
        $order = Order::factory()->create(['created_at' => now()->subHours(25)]);
        $stale = Order::find($order->id);

        // Pelanggan mengunggah bukti tepat sebelum pembatalan otomatis memproses pesanan ini.
        $order->recordPaymentProof('payments/a.jpg', 'qris');

        $this->assertFalse($stale->cancelIfExpired(now()->subHours(24), 24));
        $this->assertEquals(OrderStatus::MENUNGGU_VERIFIKASI, $order->fresh()->status);
    }
}
