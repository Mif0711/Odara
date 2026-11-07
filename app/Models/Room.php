<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function shopeeTransactions(): HasMany
    {
        return $this->hasMany(ShopeeTransaction::class);
    }

    public function chargerTransactions(): HasMany
    {
        return $this->hasMany(ChargerTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
