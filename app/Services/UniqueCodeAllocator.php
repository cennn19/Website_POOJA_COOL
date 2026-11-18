<?php

namespace App\Services;

use App\Exceptions\UniqueCodeExhaustedException;
use App\Models\Order;

/**
 * Memilih kode unik (1–999) yang belum dipakai pesanan aktif.
 *
 * Kolom orders.kode_unik_aktif memiliki unique index, jadi jika dua pesanan
 * bersamaan sempat memilih angka yang sama, database menolak salah satunya
 * dan pemanggil mencoba lagi dengan angka lain.
 */
class UniqueCodeAllocator
{
    public const MIN = 1;

    public const MAX = 999;

    /**
     * @throws UniqueCodeExhaustedException
     */
    public function pick(): int
    {
        $used = Order::query()
            ->whereNotNull('kode_unik_aktif')
            ->pluck('kode_unik_aktif')
            ->map(fn ($code) => (int) $code)
            ->all();

        $available = array_values(array_diff(range(self::MIN, self::MAX), $used));

        if ($available === []) {
            throw new UniqueCodeExhaustedException;
        }

        return $available[random_int(0, count($available) - 1)];
    }
}
