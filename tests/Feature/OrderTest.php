<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_page_is_accessible(): void
    {
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->get(route('order.create'));

        $response->assertStatus(200);
        $response->assertSee($service->name);
    }

    public function test_server_calculates_total_correctly_and_ignores_request_price(): void
    {
        $service1 = Service::factory()->create([
            'name' => 'Cuci AC Split',
            'price' => 75000,
            'is_active' => true,
        ]);

        $service2 = Service::factory()->create([
            'name' => 'Tambah Freon R32',
            'price' => 150000,
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'John Doe',
            'phone' => '081234567890',
            'address' => 'Jl. Mawar No. 12, Jakarta',
            'schedule_date' => now()->addDays(2)->format('Y-m-d'),
            'notes' => 'AC di lantai 2',
            'items' => [
                [
                    'service_id' => $service1->id,
                    'qty' => 2, // 2 * 75.000 = 150.000
                    'price' => 1, // Harga palsu dari request, HARUS diabaikan server
                    'subtotal' => 2, // Subtotal palsu dari request, HARUS diabaikan
                ],
                [
                    'service_id' => $service2->id,
                    'qty' => 1, // 1 * 150.000 = 150.000
                    'price' => 9999999, // Nilai manipulasi
                ],
            ],
            // Manipulasi total request, HARUS diabaikan
            'subtotal' => 100,
            'total' => 100,
        ];

        $response = $this->post(route('order.store'), $payload);

        $order = Order::first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('payment.show', ['code' => $order->code]));

        // Expected subtotal: 150.000 + 150.000 = 300.000
        $this->assertEquals(300000, $order->subtotal);
        $this->assertGreaterThanOrEqual(1, $order->unique_code);
        $this->assertLessThanOrEqual(999, $order->unique_code);
        $this->assertEquals($order->subtotal + $order->unique_code, $order->total);
        $this->assertEquals(OrderStatus::MENUNGGU_PEMBAYARAN, $order->status);

        // Check snapshot values in order_items
        $this->assertCount(2, $order->items);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'service_id' => $service1->id,
            'service_name' => 'Cuci AC Split',
            'qty' => 2,
            'price_at_order' => 75000,
            'subtotal' => 150000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'service_id' => $service2->id,
            'service_name' => 'Tambah Freon R32',
            'qty' => 1,
            'price_at_order' => 150000,
            'subtotal' => 150000,
        ]);
    }

    public function test_order_fails_if_service_is_inactive(): void
    {
        $inactiveService = Service::factory()->create([
            'price' => 100000,
            'is_active' => false,
        ]);

        $payload = [
            'name' => 'Jane Doe',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5',
            'schedule_date' => now()->addDay()->format('Y-m-d'),
            'items' => [
                [
                    'service_id' => $inactiveService->id,
                    'qty' => 1,
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->withoutExceptionHandling();
        $this->post(route('order.store'), $payload);
    }

    public function test_order_fails_if_quantity_is_invalid(): void
    {
        $service = Service::factory()->create([
            'price' => 50000,
            'is_active' => true,
        ]);

        $payloadZero = [
            'name' => 'Jane Doe',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5',
            'schedule_date' => now()->addDay()->format('Y-m-d'),
            'items' => [
                [
                    'service_id' => $service->id,
                    'qty' => 0, // qty must be min 1
                ],
            ],
        ];

        $response = $this->post(route('order.store'), $payloadZero);
        $response->assertSessionHasErrors('items.0.qty');

        $payloadTooLarge = [
            'name' => 'Jane Doe',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 5',
            'schedule_date' => now()->addDay()->format('Y-m-d'),
            'items' => [
                [
                    'service_id' => $service->id,
                    'qty' => 50, // max is 20
                ],
            ],
        ];

        $responseLarge = $this->post(route('order.store'), $payloadTooLarge);
        $responseLarge->assertSessionHasErrors('items.0.qty');
    }

    public function test_customer_can_check_order_status(): void
    {
        $order = Order::factory()->create([
            'code' => 'HVC-TEST1234',
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->get(route('order.status', ['code' => 'HVC-TEST1234']));

        $response->assertStatus(200);
        $response->assertSee('HVC-TEST1234');
        $response->assertSee('Menunggu Pembayaran');
    }
}
