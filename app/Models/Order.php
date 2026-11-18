<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Services\BookingSchedule;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'address',
        'schedule_date',
        'sesi',
        'notes',
        'subtotal',
        'unique_code',
        'kode_unik_aktif',
        'total',
        'status',
        'alasan_batal',
        'dibatalkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'schedule_date' => 'date',
            'dibatalkan_pada' => 'datetime',
            'subtotal' => 'integer',
            'unique_code' => 'integer',
            'total' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->latest();
    }

    /**
     * Label sesi, misal "Pagi (08.00–12.00)". Null untuk pesanan lama tanpa sesi.
     */
    public function sesiLabel(): ?string
    {
        return app(BookingSchedule::class)->sessionLabel($this->sesi);
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this->status, OrderStatus::inactive(), true);
    }

    public function canBeRescheduled(): bool
    {
        return $this->canBeCancelled();
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public static function generateCode(): string
    {
        do {
            $code = 'HVC-'.strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function canUploadProof(): bool
    {
        return in_array($this->status, [
            OrderStatus::MENUNGGU_PEMBAYARAN,
            OrderStatus::DITOLAK,
        ], true);
    }

    /**
     * Jalankan perubahan status secara atomik: kunci baris pesanan, muat ulang data terbaru,
     * baru kemudian periksa aturan dan ubah. Mencegah dua aksi bersamaan (misal Setujui dan
     * Tolak oleh dua admin, atau unggah bukti tepat saat pembatalan otomatis) sama-sama lolos.
     *
     * @template T
     *
     * @param  callable(): T  $change
     * @return T
     */
    private function transition(callable $change): mixed
    {
        return DB::transaction(function () use ($change) {
            static::query()->whereKey($this->getKey())->lockForUpdate()->first();
            $this->refresh();

            return $change();
        });
    }

    /**
     * Catat kejadian di riwayat pesanan. $user null = pelanggan atau sistem otomatis.
     *
     * @param  array<string, mixed>|null  $meta
     */
    private function log(?User $user, string $action, string $description, ?array $meta = null): void
    {
        $this->logs()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    /**
     * Catat bukti bayar baru dan ubah status menjadi menunggu_verifikasi.
     */
    public function recordPaymentProof(string $proofPath, ?string $method = null): Payment
    {
        return $this->transition(function () use ($proofPath, $method) {
            if (! $this->canUploadProof()) {
                throw new DomainException('Status pesanan saat ini tidak mengizinkan pengunggahan bukti bayar.');
            }

            $reupload = $this->status === OrderStatus::DITOLAK;

            $payment = $this->payments()->create([
                'proof_path' => $proofPath,
                'method' => $method,
                'status' => PaymentStatus::PENDING,
            ]);

            $this->update([
                'status' => OrderStatus::MENUNGGU_VERIFIKASI,
            ]);

            $label = $payment->methodLabel();
            $this->log(
                null,
                'bukti_diunggah',
                ($reupload ? 'Pelanggan mengunggah ulang bukti pembayaran' : 'Pelanggan mengunggah bukti pembayaran').($label ? ' ('.$label.')' : '').'.',
            );

            return $payment;
        });
    }

    /**
     * Admin menyetujui pesanan.
     */
    public function approve(User $admin): void
    {
        $this->transition(function () use ($admin) {
            if ($this->status !== OrderStatus::MENUNGGU_VERIFIKASI) {
                throw new DomainException('Hanya pesanan yang menunggu verifikasi yang dapat disetujui.');
            }

            $this->latestPayment?->update([
                'status' => PaymentStatus::VERIFIED,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            $this->update([
                'status' => OrderStatus::DISETUJUI,
            ]);

            $this->log($admin, 'disetujui', 'Pembayaran diverifikasi dan pesanan disetujui.');
        });
    }

    /**
     * Admin menolak bukti bayar dengan catatan/alasan.
     */
    public function reject(User $admin, string $reason): void
    {
        $this->transition(function () use ($admin, $reason) {
            if ($this->status !== OrderStatus::MENUNGGU_VERIFIKASI) {
                throw new DomainException('Hanya pesanan yang menunggu verifikasi yang dapat ditolak.');
            }

            $this->latestPayment?->update([
                'status' => PaymentStatus::REJECTED,
                'note' => $reason,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            $this->update([
                'status' => OrderStatus::DITOLAK,
            ]);

            $this->log($admin, 'ditolak', 'Bukti pembayaran ditolak. Alasan: '.$reason);
        });
    }

    /**
     * Admin menandai pesanan selesai (pekerjaan tuntas).
     */
    public function complete(User $admin): void
    {
        $this->transition(function () use ($admin) {
            if ($this->status !== OrderStatus::DISETUJUI) {
                throw new DomainException('Hanya pesanan yang telah disetujui yang dapat diselesaikan.');
            }

            $this->update([
                'status' => OrderStatus::SELESAI,
                'kode_unik_aktif' => null,
            ]);

            $this->log($admin, 'selesai', 'Pesanan ditandai selesai.');
        });
    }

    /**
     * Batalkan pesanan (manual oleh admin, atau otomatis jika $admin null).
     * Kode unik dilepas agar bisa dipakai pesanan lain.
     */
    public function cancel(?User $admin, string $reason): void
    {
        $this->transition(function () use ($admin, $reason) {
            if (! $this->canBeCancelled()) {
                throw new DomainException('Pesanan yang sudah selesai atau dibatalkan tidak dapat dibatalkan.');
            }

            $this->update([
                'status' => OrderStatus::DIBATALKAN,
                'alasan_batal' => $reason,
                'dibatalkan_pada' => now(),
                'kode_unik_aktif' => null,
            ]);

            $this->log($admin, 'dibatalkan', 'Pesanan dibatalkan. Alasan: '.$reason);
        });
    }

    /**
     * Pembatalan otomatis: batalkan hanya jika, setelah baris dikunci, pesanan masih memenuhi
     * syarat kedaluwarsa. Mengembalikan true bila pesanan dibatalkan.
     *
     * - Menunggu Pembayaran: dibuat sebelum $cutoff dan belum pernah ada bukti bayar.
     * - Ditolak: bukti terakhir ditolak sebelum $cutoff dan tidak diunggah ulang.
     */
    public function cancelIfExpired(Carbon $cutoff, int $hours): bool
    {
        return $this->transition(function () use ($cutoff, $hours) {
            $reason = match (true) {
                $this->status === OrderStatus::MENUNGGU_PEMBAYARAN
                    && $this->created_at->lte($cutoff)
                    && ! $this->payments()->exists() => "Kedaluwarsa: tidak dibayar dalam {$hours} jam",
                $this->status === OrderStatus::DITOLAK
                    && $this->latestPayment?->verified_at?->lte($cutoff) => "Kedaluwarsa: bukti pembayaran tidak diunggah ulang dalam {$hours} jam setelah ditolak",
                default => null,
            };

            if ($reason === null) {
                return false;
            }

            $this->cancel(null, $reason);

            return true;
        });
    }

    /**
     * Ubah jadwal kunjungan. Kuota dicek kecuali $force = true. Jadwal lama dicatat di log.
     */
    public function reschedule(User $admin, string $date, string $sesi, bool $force = false): void
    {
        $schedule = app(BookingSchedule::class);

        $this->transition(function () use ($admin, $date, $sesi, $force, $schedule) {
            if (! $this->canBeRescheduled()) {
                throw new DomainException('Jadwal pesanan yang sudah selesai atau dibatalkan tidak dapat diubah.');
            }

            if (! array_key_exists($sesi, $schedule->sessions())) {
                throw new DomainException('Sesi kunjungan tidak valid.');
            }

            // Kunci baris sesi tujuan agar kuota tidak terlampaui oleh pesanan yang masuk bersamaan.
            if (! $force && $schedule->isFull($date, $sesi, $this->id, lock: true)) {
                throw new DomainException($schedule->fullMessage($date, $sesi).' Centang "Paksa meski penuh" untuk tetap menyimpan.');
            }

            $oldDate = $this->schedule_date->toDateString();
            $oldSesi = $this->sesi;
            $oldLabel = $this->schedule_date->translatedFormat('d F Y').($oldSesi ? ', '.$schedule->sessionLabel($oldSesi) : '');

            $this->update([
                'schedule_date' => $date,
                'sesi' => $sesi,
            ]);
            $this->refresh();

            $newLabel = $this->schedule_date->translatedFormat('d F Y').', '.$schedule->sessionLabel($sesi);

            $this->log(
                $admin,
                'ubah_jadwal',
                'Jadwal diubah dari '.$oldLabel.' menjadi '.$newLabel.($force ? ' (dipaksa meski penuh)' : '').'.',
                [
                    'jadwal_lama' => ['tanggal' => $oldDate, 'sesi' => $oldSesi],
                    'jadwal_baru' => ['tanggal' => $date, 'sesi' => $sesi],
                    'paksa' => $force,
                ],
            );
        });
    }
}
