<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAndPrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('usaha.whatsapp', '0811-1111-2222');
    }

    public function test_status_page_masks_personal_data(): void
    {
        $order = Order::factory()->create([
            'name' => 'Hendra Wijaya',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5, Lowokwaru, Malang',
            'notes' => 'Kunci di bawah pot bunga',
        ]);

        $response = $this->get(route('order.status', ['code' => $order->code]));

        $response->assertOk();
        $response->assertSee('Hendra W.');
        $response->assertSee('0812****7890');
        $response->assertSee('•••, Malang');
        $response->assertDontSee('081234567890');
        $response->assertDontSee('Jl. Melati No. 5');
        $response->assertDontSee('Hendra Wijaya');
        $response->assertDontSee('Kunci di bawah pot bunga');
    }

    public function test_payment_page_offers_prefilled_whatsapp_to_admin(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::MENUNGGU_PEMBAYARAN]);

        $response = $this->get(route('payment.show', ['code' => $order->code]));

        $response->assertOk();
        $response->assertSee('https://wa.me/6281111112222?text=', false);
        $response->assertSee(rawurlencode('Kode: '.$order->code), false);
    }

    public function test_admin_order_page_has_status_based_message_to_customer(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'phone' => '0857-1234-5678',
            'status' => OrderStatus::DITOLAK,
        ]);
        $order->payments()->create([
            'proof_path' => 'payments/x.jpg',
            'method' => 'qris',
            'status' => PaymentStatus::REJECTED,
            'note' => 'Nominal kurang',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('https://wa.me/6285712345678?text=', false);
        $response->assertSee(rawurlencode('Alasan: Nominal kurang'), false);
        // Admin tetap melihat data lengkap
        $response->assertSee('0857-1234-5678');
    }

    public function test_footer_uses_configured_whatsapp_number(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('0811-1111-2222');
        $response->assertSee('https://wa.me/6281111112222', false);
    }
}
