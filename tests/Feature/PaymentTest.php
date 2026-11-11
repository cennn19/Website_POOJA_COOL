<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_is_accessible(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->get(route('payment.show', ['code' => $order->code]));

        $response->assertStatus(200);
        $response->assertSee($order->code);
        $response->assertSee(number_format($order->total, 0, ',', '.'));
    }

    public function test_upload_rejects_non_image_files(): void
    {
        Storage::fake('private');

        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        $fakePdf = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->post(route('payment.upload', ['code' => $order->code]), [
            'proof' => $fakePdf,
        ]);

        $response->assertSessionHasErrors('proof');
        $this->assertEquals(0, $order->payments()->count());
    }

    public function test_upload_rejects_files_over_two_megabytes(): void
    {
        Storage::fake('private');

        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        // 3 MB file
        $largeFile = UploadedFile::fake()->image('large_receipt.jpg')->size(3072);

        $response = $this->post(route('payment.upload', ['code' => $order->code]), [
            'proof' => $largeFile,
        ]);

        $response->assertSessionHasErrors('proof');
        $this->assertEquals(0, $order->payments()->count());
    }

    public function test_valid_proof_upload_changes_status_to_menunggu_verifikasi_and_stores_in_private_disk(): void
    {
        Storage::fake('private');

        $order = Order::factory()->create([
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        $validImage = UploadedFile::fake()->image('transfer_receipt.jpg', 600, 800)->size(500);

        $response = $this->post(route('payment.upload', ['code' => $order->code]), [
            'proof' => $validImage,
        ]);

        $response->assertRedirect(route('payment.show', ['code' => $order->code]));

        $order->refresh();
        $this->assertEquals(OrderStatus::MENUNGGU_VERIFIKASI, $order->status);

        $payment = $order->latestPayment;
        $this->assertNotNull($payment);
        $this->assertEquals(PaymentStatus::PENDING, $payment->status);

        // Verify stored in private disk
        Storage::disk('private')->assertExists($payment->proof_path);
    }

    public function test_customer_cannot_upload_proof_when_order_is_already_approved(): void
    {
        Storage::fake('private');

        $order = Order::factory()->create([
            'status' => OrderStatus::DISETUJUI,
        ]);

        $validImage = UploadedFile::fake()->image('transfer_receipt.jpg', 600, 800)->size(500);

        $response = $this->post(route('payment.upload', ['code' => $order->code]), [
            'proof' => $validImage,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, $order->payments()->count());
    }
}
