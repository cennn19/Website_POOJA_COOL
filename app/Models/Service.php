<?php

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ServiceType::class,
            'is_active' => 'boolean',
            'price' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePackages(Builder $query): Builder
    {
        return $query->where('type', ServiceType::PACKAGE);
    }

    public function scopeUnits(Builder $query): Builder
    {
        return $query->where('type', ServiceType::UNIT);
    }
}
