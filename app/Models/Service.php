<?php

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Jenis pekerjaan di dalam sebuah kategori layanan.
 */
class Service extends Model
{
    use HasFactory;

    public const SATUAN_UNIT = 'unit';

    public const SATUAN_METER = 'meter';

    public const SATUAN = [self::SATUAN_UNIT, self::SATUAN_METER];

    protected $fillable = [
        'kategori_layanan_id',
        'name',
        'slug',
        'type',
        'satuan',
        'price',
        'harga_mulai',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $attributes = [
        'type' => 'unit',
        'satuan' => self::SATUAN_UNIT,
    ];

    protected function casts(): array
    {
        return [
            'type' => ServiceType::class,
            'is_active' => 'boolean',
            'harga_mulai' => 'boolean',
            'price' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class, 'kategori_layanan_id');
    }

    /**
     * Label harga untuk tampilan, misal "Mulai dari Rp 75.000", "Rp 65.000", atau "Rp 50.000 / meter".
     */
    public function priceLabel(): string
    {
        return ($this->harga_mulai ? 'Mulai dari ' : '')
            .format_rupiah($this->price)
            .($this->satuan === self::SATUAN_METER ? ' / meter' : '');
    }

    /**
     * Label isian jumlah di form pemesanan.
     */
    public function qtyLabel(): string
    {
        return $this->satuan === self::SATUAN_METER ? 'Panjang pipa (meter)' : 'Jumlah unit';
    }

    /**
     * Nama lengkap untuk disimpan di pesanan, misal "Tambah Freon – Split 0,5–1 PK".
     */
    public function fullName(): string
    {
        return $this->kategori ? $this->kategori->nama.' – '.$this->name : $this->name;
    }

    public function hasPrice(): bool
    {
        return $this->price > 0;
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Bisa dipesan pelanggan: aktif, harga sudah diisi (> 0), dan kategorinya aktif.
     */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('services.is_active', true)
            ->where('services.price', '>', 0)
            ->whereHas('kategori', fn (Builder $q) => $q->where('aktif', true));
    }
}
