<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Services\UnpaidOrderExpirer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Pembatalan pesanan kedaluwarsa saat ada pengunjung (pengganti cron untuk hosting tanpa cron).
 */
class LazyOrderExpiryTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('booking.kuota_per_sesi', 3);
        config()->set('booking.cek_kedaluwarsa_menit', 5);
        $this->service = Service::factory()->create(['price' => 100000, 'is_active' => true]);
    }

    private function expiredOrder(array $attributes = []): Order
    {
        return Order::factory()->create(array_merge(['created_at' => now()->subHours(25)], $attributes));
    }

    public function test_visiting_order_page_cancels_expired_unpaid_orders(): void
    {
        $expired = $this->expiredOrder(['unique_code' => 7, 'kode_unik_aktif' => 7]);
        $recent = Order::factory()->create(['created_at' => now()->subHours(2)]);

        $this->get(route('order.create'))->assertOk();

        $expired->refresh();
        $this->assertEquals(OrderStatus::DIBATALKAN, $expired->status);
        $this->assertNull($expired->kode_unik_aktif);
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $recent->fresh()->status);
    }

    public function test_expired_orders_free_the_session_quota_for_new_orders(): void
    {
        $date = now()->addDays(3)->toDateString();
        $this->expiredOrder(['schedule_date' => $date, 'sesi' => 'pagi']);
        Order::factory()->count(2)->create(['schedule_date' => $date, 'sesi' => 'pagi']);

        $this->post(route('order.store'), [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5, Lowokwaru, Malang',
            'schedule_date' => $date,
            'sesi' => 'pagi',
            'items' => [['kategori_id' => $this->service->kategori_layanan_id, 'service_id' => $this->service->id, 'qty' => 1]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(3, Order::whereDate('schedule_date', $date)
            ->where('sesi', 'pagi')
            ->where('status', '!=', OrderStatus::DIBATALKAN)
            ->count());
    }

    public function test_check_runs_at_most_once_per_interval(): void
    {
        $this->get(route('order.status'))->assertOk();

        $expired = $this->expiredOrder();
        $this->get(route('order.status'))->assertOk();
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $expired->fresh()->status);

        $this->travel(6)->minutes();
        $this->get(route('order.status'))->assertOk();
        $this->assertEquals(OrderStatus::DIBATALKAN, $expired->fresh()->status);
    }

    public function test_admin_panel_also_triggers_the_check(): void
    {
        $expired = $this->expiredOrder();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertOk();

        $this->assertEquals(OrderStatus::DIBATALKAN, $expired->fresh()->status);
    }

    public function test_failure_in_expiry_check_does_not_break_the_page(): void
    {
        $this->mock(UnpaidOrderExpirer::class)
            ->shouldReceive('expireIfDue')
            ->andThrow(new RuntimeException('database sibuk'));

        $this->get(route('order.create'))->assertOk();
    }

    public function test_cron_and_visitor_check_do_not_cancel_twice(): void
    {
        $expired = $this->expiredOrder();

        $this->get(route('order.create'))->assertOk();
        $this->artisan('orders:expire')->expectsOutput('0 pesanan kedaluwarsa dibatalkan.')->assertSuccessful();

        $this->assertSame(1, $expired->logs()->where('action', 'dibatalkan')->count());
    }
}
