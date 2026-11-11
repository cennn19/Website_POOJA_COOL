<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_admin_orders(): void
    {
        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_unauthenticated_user_cannot_access_payment_proof(): void
    {
        Storage::fake('private');
        $path = UploadedFile::fake()->image('proof.jpg')->store('payments', 'private');

        $order = Order::factory()->create();
        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => $path,
            'status' => PaymentStatus::PENDING,
        ]);

        $response = $this->get(route('admin.payments.proof', $payment));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_orders_and_stream_proof(): void
    {
        Storage::fake('private');
        $path = UploadedFile::fake()->image('proof.jpg')->store('payments', 'private');

        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_VERIFIKASI,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => $path,
            'status' => PaymentStatus::PENDING,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee($order->code);

        $proofResponse = $this->actingAs($admin)->get(route('admin.payments.proof', $payment));
        $proofResponse->assertStatus(200);
    }

    public function test_admin_can_approve_order_and_customer_cannot(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_VERIFIKASI,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => 'payments/fake.jpg',
            'status' => PaymentStatus::PENDING,
        ]);

        // Customer (unauthenticated) attempt
        $guestResponse = $this->post(route('admin.orders.approve', $order));
        $guestResponse->assertRedirect(route('admin.login'));
        $this->assertEquals(OrderStatus::MENUNGGU_VERIFIKASI, $order->fresh()->status);

        // Admin attempt
        $adminResponse = $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $adminResponse->assertRedirect();

        $order->refresh();
        $this->assertEquals(OrderStatus::DISETUJUI, $order->status);
        $this->assertEquals(PaymentStatus::VERIFIED, $order->latestPayment->status);
        $this->assertEquals($admin->id, $order->latestPayment->verified_by);
        $this->assertNotNull($order->latestPayment->verified_at);
    }

    public function test_admin_can_reject_order_with_reason(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_VERIFIKASI,
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => 'payments/fake.jpg',
            'status' => PaymentStatus::PENDING,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.reject', $order), [
            'reason' => 'Nominal transfer tidak sesuai dengan tagihan.',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals(OrderStatus::DITOLAK, $order->status);
        $this->assertEquals(PaymentStatus::REJECTED, $order->latestPayment->status);
        $this->assertEquals('Nominal transfer tidak sesuai dengan tagihan.', $order->latestPayment->note);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $admin = User::factory()->create();

        // Cannot approve an order that is still 'menunggu_pembayaran'
        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $response->assertSessionHas('error');
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $order->fresh()->status);

        // Directly on model, domain exception is thrown
        $this->expectException(DomainException::class);
        $order->approve($admin);
    }

    public function test_admin_can_complete_approved_order(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::DISETUJUI,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.complete', $order));
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals(OrderStatus::SELESAI, $order->status);
    }
}
