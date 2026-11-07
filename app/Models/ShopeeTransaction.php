<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopeeTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'transaction_date',
        'receipt_number',
        'customer_name',
        'item_type',
        'item_price',
        'service_fee',
        'is_picked_up',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'item_price' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'is_picked_up' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->item_price + (float) $this->service_fee;
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
