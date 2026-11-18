<?php

namespace Database\Factories;

use App\Models\KategoriLayanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<KategoriLayanan>
 */
class KategoriLayananFactory extends Factory
{
    protected $model = KategoriLayanan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = ucfirst(fake()->unique()->words(2, true));

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama).'-'.fake()->unique()->numberBetween(100, 99999),
            'urutan' => 0,
            'catatan_harga' => null,
            'aktif' => true,
        ];
    }
}
