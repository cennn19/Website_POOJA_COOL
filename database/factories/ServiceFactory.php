<?php

namespace Database\Factories;

use App\Enums\ServiceType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true).' Service';

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999),
            'type' => fake()->randomElement([ServiceType::PACKAGE, ServiceType::UNIT]),
            'price' => fake()->randomElement([75000, 120000, 150000, 250000, 350000]),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
