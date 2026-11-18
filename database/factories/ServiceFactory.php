<?php

namespace Database\Factories;

use App\Enums\ServiceType;
use App\Models\KategoriLayanan;
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
            'kategori_layanan_id' => KategoriLayanan::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 99999),
            'type' => ServiceType::UNIT,
            'satuan' => Service::SATUAN_UNIT,
            'price' => fake()->randomElement([75000, 120000, 150000, 250000, 350000]),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Layanan lama sebelum struktur kategori (tanpa kategori, nonaktif).
     */
    public function legacy(): static
    {
        return $this->state(fn () => [
            'kategori_layanan_id' => null,
            'is_active' => false,
        ]);
    }

    public function perMeter(): static
    {
        return $this->state(fn () => ['satuan' => Service::SATUAN_METER]);
    }
}
