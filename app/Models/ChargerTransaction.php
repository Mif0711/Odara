<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'charger_item_id',
        'transaction_date',
        'pin_number',
        'customer_name',
        'item_type',
        'qty',
        'is_picked_up',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'qty' => 'integer',
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

    public function chargerItem(): BelongsTo
    {
        return $this->belongsTo(ChargerItem::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
