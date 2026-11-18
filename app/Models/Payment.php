<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Support\PaymentMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'proof_path',
        'method',
        'status',
        'note',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Label metode pembayaran yang mudah dibaca, misal "Transfer Bank · BCA".
     */
    public function methodLabel(): ?string
    {
        return PaymentMethods::label($this->method);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
