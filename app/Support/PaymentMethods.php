<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Membaca daftar metode pembayaran dari config/payment.php.
 */
class PaymentMethods
{
    /**
     * Semua metode yang aktif (enabled = true).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function enabled(): Collection
    {
        return collect(config('payment.methods', []))
            ->filter(fn (array $method) => ($method['enabled'] ?? true) && filled($method['key'] ?? null))
            ->map(fn (array $method) => array_merge([
                'type' => 'bank',
                'name' => $method['key'],
                'account_number' => null,
                'account_name' => null,
                'image' => null,
                'note' => null,
            ], $method))
            // Nama pemilik QRIS selalu dibaca dari config/usaha.php.
            ->map(fn (array $method) => $method['type'] === 'qris'
                ? array_merge($method, ['account_name' => config('usaha.qris_nama_pemilik')])
                : $method)
            ->values();
    }

    /**
     * Metode aktif dikelompokkan per jenis, mengikuti urutan config('payment.types').
     * Jenis tanpa metode aktif tidak ikut ditampilkan.
     *
     * @return Collection<string, array{label: string, methods: Collection<int, array<string, mixed>>}>
     */
    public static function grouped(): Collection
    {
        $enabled = static::enabled();

        return collect(config('payment.types', []))
            ->map(fn (string $label, string $type) => [
                'label' => $label,
                'methods' => $enabled->where('type', $type)->values(),
            ])
            ->filter(fn (array $group) => $group['methods']->isNotEmpty());
    }

    /**
     * Key semua metode aktif, untuk validasi.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return static::enabled()->pluck('key')->all();
    }

    /**
     * Cari metode berdasarkan key (termasuk yang sudah dinonaktifkan,
     * supaya pembayaran lama tetap punya label di admin).
     *
     * @return array<string, mixed>|null
     */
    public static function find(?string $key): ?array
    {
        if (blank($key)) {
            return null;
        }

        return collect(config('payment.methods', []))->firstWhere('key', $key);
    }

    /**
     * Label yang mudah dibaca, misal "Transfer Bank · BCA".
     */
    public static function label(?string $key): ?string
    {
        $method = static::find($key);

        if (! $method) {
            return $key;
        }

        $typeLabel = config('payment.types.'.($method['type'] ?? ''), null);

        return $method['type'] === 'qris' || ! $typeLabel
            ? $method['name']
            : $typeLabel.' · '.$method['name'];
    }
}
