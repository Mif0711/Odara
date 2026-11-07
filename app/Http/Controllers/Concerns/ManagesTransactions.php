<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ChargerItem;
use App\Models\ChargerTransaction;
use App\Models\Room;
use App\Models\ShopeeTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

trait ManagesTransactions
{
    /**
     * Accept Indonesian formatted currency input such as "5.000" or "Rp 5.000".
     *
     * @param  array<int, string>  $fields
     */
    protected function normalizeRupiahFields(Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            if (! $request->exists($field)) {
                continue;
            }

            $value = $request->input($field);

            if (! is_string($value)) {
                continue;
            }

            $digits = preg_replace('/\D+/', '', $value);

            $request->merge([
                $field => $digits === '' ? null : (int) $digits,
            ]);
        }
    }

    protected function ensureShopeeCanModify(User $user, ShopeeTransaction $transaction): void
    {
        abort_unless(
            $user->isPetugas() && $transaction->user_id === $user->id,
            403,
            'Hanya Petugas pemilik transaksi yang dapat mengubah transaksi Shopee.'
        );
    }

    protected function ensureChargerCanModify(User $user, ChargerTransaction $transaction): void
    {
        abort_unless(
            $user->isPetugas() && $transaction->user_id === $user->id,
            403,
            'Hanya Petugas pemilik transaksi yang dapat mengubah transaksi Charger.'
        );
    }

    /**
     * @return Collection<int, Room>
     */
    protected function roomOptions(?int $currentRoomId = null): Collection
    {
        return Room::query()
            ->where(function ($query) use ($currentRoomId) {
                $query->where('is_active', true);

                if ($currentRoomId !== null) {
                    $query->orWhere('id', $currentRoomId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    protected function customerNameSuggestions(User $user): array
    {
        $shopee = ShopeeTransaction::query()
            ->where('user_id', $user->id)
            ->select('customer_name')
            ->distinct()
            ->limit(250)
            ->pluck('customer_name');

        $charger = ChargerTransaction::query()
            ->where('user_id', $user->id)
            ->select('customer_name')
            ->distinct()
            ->limit(250)
            ->pluck('customer_name');

        return $shopee
            ->merge($charger)
            ->filter(fn ($name) => is_string($name) && trim($name) !== '')
            ->unique(fn ($name) => mb_strtolower($name))
            ->sort()
            ->values()
            ->all();
    }

    protected function assertRoomIsAvailable(int $roomId, ?int $currentRoomId = null): void
    {
        $room = Room::find($roomId);

        if (! $room) {
            throw ValidationException::withMessages([
                'room_id' => 'Kamar yang dipilih tidak ditemukan.',
            ]);
        }

        if (! $room->is_active && $room->id !== $currentRoomId) {
            throw ValidationException::withMessages([
                'room_id' => 'Kamar tersebut sudah nonaktif dan tidak dapat dipilih untuk transaksi baru.',
            ]);
        }
    }

    /**
     * @return Collection<int, ChargerItem>
     */
    protected function chargerItemOptions(?int $currentItemId = null): Collection
    {
        return ChargerItem::query()
            ->where(function ($query) use ($currentItemId) {
                $query->where('is_active', true);

                if ($currentItemId !== null) {
                    $query->orWhere('id', $currentItemId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    protected function assertChargerItemIsAvailable(int $itemId, ?int $currentItemId = null): ChargerItem
    {
        $item = ChargerItem::find($itemId);

        if (! $item) {
            throw ValidationException::withMessages([
                'charger_item_id' => 'Barang Charger yang dipilih tidak ditemukan.',
            ]);
        }

        if (! $item->is_active && $item->id !== $currentItemId) {
            throw ValidationException::withMessages([
                'charger_item_id' => 'Barang Charger tersebut sudah nonaktif dan tidak dapat dipilih untuk transaksi baru.',
            ]);
        }

        return $item;
    }
}
