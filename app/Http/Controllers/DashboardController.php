<?php

namespace App\Http\Controllers;

use App\Models\ChargerItem;
use App\Models\ChargerTransaction;
use App\Models\Room;
use App\Models\ShopeeTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        $roomsCount = null;
        $officersCount = null;
        $chargerItemsCount = null;
        $usersCount = null;
        $shopeeTotal = 0;
        $chargerTotal = 0;
        $todayTotal = 0;
        $shopeeRevenue = 0.0;
        $recent = collect();

        if ($isAdmin) {
            $roomsCount = Room::query()->active()->count();
            $officersCount = User::query()->officers()->active()->count();
            $chargerItemsCount = ChargerItem::query()->active()->count();
            $usersCount = User::query()->active()->count();
        } else {
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();
            $today = now()->toDateString();

            $shopee = ShopeeTransaction::query()->where('user_id', $user->id);
            $charger = ChargerTransaction::query()->where('user_id', $user->id);

            $shopeeTotal = (clone $shopee)->count();
            $chargerTotal = (clone $charger)->count();
            $todayTotal = (clone $shopee)->whereDate('transaction_date', $today)->count()
                + (clone $charger)->whereDate('transaction_date', $today)->count();

            $shopeeRevenue = (float) (clone $shopee)
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->selectRaw('COALESCE(SUM(item_price + service_fee), 0) as revenue')
                ->value('revenue');

            $recent = (clone $shopee)
                ->with(['room', 'user'])
                ->latest('transaction_date')
                ->latest('id')
                ->limit(5)
                ->get()
                ->merge((clone $charger)->with(['room', 'user'])->latest('transaction_date')->latest('id')->limit(5)->get())
                ->sortByDesc(fn ($item) => $item->transaction_date->toDateString().'-'.str_pad((string) $item->id, 8, '0', STR_PAD_LEFT))
                ->take(8)
                ->values();
        }

        return view('dashboard', compact(
            'isAdmin',
            'shopeeTotal',
            'chargerTotal',
            'todayTotal',
            'shopeeRevenue',
            'recent',
            'roomsCount',
            'officersCount',
            'chargerItemsCount',
            'usersCount',
        ));
    }
}
