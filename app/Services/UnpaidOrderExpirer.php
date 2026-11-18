<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

/**
 * Membatalkan pesanan yang melewati batas waktu (config booking.kedaluwarsa_jam):
 * - "menunggu pembayaran" tanpa bukti bayar, dan
 * - "ditolak" yang buktinya tidak diunggah ulang,
 * agar kuota sesi dan kode unik yang ditahannya kembali tersedia.
 *
 * Dipanggil dari dua tempat:
 * - perintah `orders:expire` (cron, bila hosting mendukung), dan
 * - middleware ExpireUnpaidOrders saat ada pengunjung (untuk hosting tanpa cron).
 * Keduanya aman berjalan bersamaan karena memakai lock yang sama.
 */
class UnpaidOrderExpirer
{
    private const LOCK_KEY = 'orders:expire:lock';

    private const LAST_RUN_KEY = 'orders:expire:last-run';

    /**
     * Jalankan pembatalan sekarang. Mengembalikan jumlah pesanan yang dibatalkan.
     */
    public function expire(): int
    {
        $lock = Cache::lock(self::LOCK_KEY, 120);

        if (! $lock->get()) {
            return 0; // Proses lain sedang membatalkan.
        }

        try {
            $hours = (int) config('booking.kedaluwarsa_jam', 24);
            $cutoff = now()->subHours($hours);
            $count = 0;

            Order::query()
                ->where(function ($query) use ($cutoff) {
                    // Belum pernah mengunggah bukti dalam batas waktu.
                    $query->where(fn ($q) => $q
                        ->where('status', OrderStatus::MENUNGGU_PEMBAYARAN)
                        ->where('created_at', '<=', $cutoff)
                        ->whereDoesntHave('payments'))
                    // Bukti ditolak dan tidak diunggah ulang dalam batas waktu.
                        ->orWhere(fn ($q) => $q
                            ->where('status', OrderStatus::DITOLAK)
                            ->whereDoesntHave('payments', fn ($p) => $p
                                ->whereNull('verified_at')
                                ->orWhere('verified_at', '>', $cutoff)));
                })
                ->orderBy('id')
                ->each(function (Order $order) use ($cutoff, $hours, &$count) {
                    // Syarat dicek ulang setelah baris dikunci (pelanggan bisa saja baru mengunggah bukti).
                    if ($order->cancelIfExpired($cutoff, $hours)) {
                        $count++;
                    }
                });

            return $count;
        } finally {
            $lock->release();
        }
    }

    /**
     * Jalankan pembatalan paling sering sekali per interval (config booking.cek_kedaluwarsa_menit),
     * supaya pengecekan saat ada pengunjung tidak memperlambat website.
     */
    public function expireIfDue(): void
    {
        $minutes = max(1, (int) config('booking.cek_kedaluwarsa_menit', 5));

        if (Cache::add(self::LAST_RUN_KEY, true, now()->addMinutes($minutes))) {
            $this->expire();
        }
    }
}
