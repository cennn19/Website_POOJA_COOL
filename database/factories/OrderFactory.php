<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = 200000;
        $uniqueCode = fake()->numberBetween(1, 999);

        return [
            'code' => Order::generateCode(),
            'name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'address' => fake()->address(),
            'schedule_date' => now()->addDays(2)->toDateString(),
            'notes' => fake()->sentence(),
            'subtotal' => $subtotal,
            'unique_code' => $uniqueCode,
            'total' => $subtotal + $uniqueCode,
            'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
        ];
    }
}
