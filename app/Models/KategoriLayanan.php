<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Kategori layanan, misal "Cleaning AC" atau "Tambah Freon".
 * Setiap kategori berisi beberapa jenis pekerjaan (model Service).
 */
class KategoriLayanan extends Model
{
    use HasFactory;

    protected $table = 'kategori_layanan';

    protected $fillable = [
        'nama',
        'slug',
        'urutan',
        'catatan_harga',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Kategori aktif beserta jenis pekerjaan yang bisa dipesan (aktif dan harga sudah diisi).
     * Kategori tanpa pekerjaan yang bisa dipesan tidak ikut.
     *
     * @return Collection<int, self>
     */
    public static function katalog()
    {
        return static::query()
            ->aktif()
            ->orderBy('urutan')
            ->with(['services' => fn ($q) => $q->bookable()])
            ->get()
            ->filter(fn (self $kategori) => $kategori->services->isNotEmpty())
            ->values();
    }
}
