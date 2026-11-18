<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Carbon;

/**
 * Aturan jadwal kunjungan: sesi, batas waktu pesan, dan kuota per sesi.
 * Konfigurasi ada di config/booking.php.
 */
class BookingSchedule
{
    /**
     * @return array<string, array{label: string, jam: string, batas_pesan_hari_ini: ?string}>
     */
    public function sessions(): array
    {
        return config('booking.sesi', []);
    }

    public function quota(): int
    {
        return (int) config('booking.kuota_per_sesi', 3);
    }

    public function maxDays(): int
    {
        return (int) config('booking.maks_hari_ke_depan', 30);
    }

    public function sessionLabel(?string $sesi): ?string
    {
        $session = $this->sessions()[$sesi] ?? null;

        return $session ? $session['label'].' ('.$session['jam'].')' : null;
    }

    /**
     * Cek aturan waktu (bukan kuota). Mengembalikan pesan error, atau null jika boleh.
     */
    public function timeRuleError(Carbon|string $date, string $sesi, ?Carbon $now = null): ?string
    {
        $now ??= now();
        $date = Carbon::parse($date)->startOfDay();
        $today = $now->copy()->startOfDay();
        $session = $this->sessions()[$sesi] ?? null;

        if (! $session) {
            return 'Sesi kunjungan tidak valid.';
        }

        if ($date->lt($today)) {
            return 'Tanggal kunjungan sudah lewat.';
        }

        if ($date->gt($today->copy()->addDays($this->maxDays()))) {
            return 'Tanggal kunjungan maksimal '.$this->maxDays().' hari ke depan.';
        }

        if ($date->equalTo($today)) {
            $deadline = $session['batas_pesan_hari_ini'];

            if ($deadline === null) {
                return 'Sesi '.$session['label'].' untuk hari ini sudah tidak bisa dipesan. Silakan pilih sesi atau tanggal lain.';
            }

            if ($now->format('H:i') >= $deadline) {
                return 'Sesi '.$session['label'].' untuk hari ini hanya bisa dipesan sebelum pukul '.str_replace(':', '.', $deadline).'.';
            }
        }

        return null;
    }

    /**
     * Jumlah pesanan yang memakai tanggal + sesi ini (semua status kecuali dibatalkan).
     */
    public function bookedCount(Carbon|string $date, string $sesi, ?int $exceptOrderId = null, bool $lock = false): int
    {
        $query = Order::query()
            ->whereDate('schedule_date', Carbon::parse($date)->toDateString())
            ->where('sesi', $sesi)
            ->where('status', '!=', OrderStatus::DIBATALKAN)
            ->when($exceptOrderId, fn ($q) => $q->where('id', '!=', $exceptOrderId));

        if ($lock) {
            // Kunci baris terkait selama transaksi agar kuota tidak terlampaui saat pesanan bersamaan.
            return $query->lockForUpdate()->get(['id'])->count();
        }

        return $query->count();
    }

    public function isFull(Carbon|string $date, string $sesi, ?int $exceptOrderId = null, bool $lock = false): bool
    {
        return $this->bookedCount($date, $sesi, $exceptOrderId, $lock) >= $this->quota();
    }

    public function fullMessage(Carbon|string $date, string $sesi): string
    {
        $label = $this->sessions()[$sesi]['label'] ?? $sesi;

        return 'Sesi '.$label.' pada '.Carbon::parse($date)->translatedFormat('d F Y').' sudah penuh. Silakan pilih sesi atau tanggal lain.';
    }

    /**
     * Ketersediaan semua sesi dari hari ini sampai batas maksimal, untuk form order.
     *
     * @return array<string, array<string, array{available: bool, reason: ?string}>>
     */
    public function availability(?Carbon $now = null): array
    {
        $now ??= now();
        $start = $now->copy()->startOfDay();
        $end = $start->copy()->addDays($this->maxDays());

        $counts = Order::query()
            ->whereDate('schedule_date', '>=', $start->toDateString())
            ->whereDate('schedule_date', '<=', $end->toDateString())
            ->whereNotNull('sesi')
            ->where('status', '!=', OrderStatus::DIBATALKAN)
            ->get(['schedule_date', 'sesi'])
            ->groupBy(fn (Order $o) => $o->schedule_date->toDateString().'|'.$o->sesi)
            ->map->count();

        $result = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->toDateString();
            foreach (array_keys($this->sessions()) as $sesi) {
                $timeError = $this->timeRuleError($day, $sesi, $now);
                $full = ($counts[$key.'|'.$sesi] ?? 0) >= $this->quota();

                $result[$key][$sesi] = [
                    'available' => ! $timeError && ! $full,
                    'reason' => $timeError ? 'Lewat batas' : ($full ? 'Penuh' : null),
                ];
            }
        }

        return $result;
    }
}
