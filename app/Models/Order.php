<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'notes',
        'subtotal',
        'unique_code',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'schedule_date' => 'date',
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
     * Catat bukti bayar baru dan ubah status menjadi menunggu_verifikasi.
     */
    public function recordPaymentProof(string $proofPath): Payment
    {
        if (! $this->canUploadProof()) {
            throw new DomainException('Status pesanan saat ini tidak mengizinkan pengunggahan bukti bayar.');
        }

        $payment = $this->payments()->create([
            'proof_path' => $proofPath,
            'status' => PaymentStatus::PENDING,
        ]);

        $this->update([
            'status' => OrderStatus::MENUNGGU_VERIFIKASI,
        ]);

        return $payment;
    }

    /**
     * Admin menyetujui pesanan.
     */
    public function approve(User $admin): void
    {
        if ($this->status !== OrderStatus::MENUNGGU_VERIFIKASI) {
            throw new DomainException('Hanya pesanan yang menunggu verifikasi yang dapat disetujui.');
        }

        if ($payment = $this->latestPayment) {
            $payment->update([
                'status' => PaymentStatus::VERIFIED,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
        }

        $this->update([
            'status' => OrderStatus::DISETUJUI,
        ]);
    }

    /**
     * Admin menolak bukti bayar dengan catatan/alasan.
     */
    public function reject(User $admin, string $reason): void
    {
        if ($this->status !== OrderStatus::MENUNGGU_VERIFIKASI) {
            throw new DomainException('Hanya pesanan yang menunggu verifikasi yang dapat ditolak.');
        }

        if ($payment = $this->latestPayment) {
            $payment->update([
                'status' => PaymentStatus::REJECTED,
                'note' => $reason,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
        }

        $this->update([
            'status' => OrderStatus::DITOLAK,
        ]);
    }

    /**
     * Admin menandai pesanan selesai (pekerjaan tuntas).
     */
    public function complete(User $admin): void
    {
        if ($this->status !== OrderStatus::DISETUJUI) {
            throw new DomainException('Hanya pesanan yang telah disetujui yang dapat diselesaikan.');
        }

        $this->update([
            'status' => OrderStatus::SELESAI,
        ]);
    }
}
