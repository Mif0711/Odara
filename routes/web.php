<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChargerItemController;
use App\Http\Controllers\ChargerTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ShopeeTransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:8,1')
        ->name('login.store');

    Route::get('/setup', [AuthController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [AuthController::class, 'setup'])
        ->middleware('throttle:5,1')
        ->name('setup.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('petugas')->group(function () {
        Route::resource('shopee', ShopeeTransactionController::class)
            ->except(['show'])
            ->parameters(['shopee' => 'shopee']);
        Route::patch('shopee/{shopee}/pickup', [ShopeeTransactionController::class, 'togglePickup'])->name('shopee.pickup');

        Route::resource('charger', ChargerTransactionController::class)
            ->except(['show'])
            ->parameters(['charger' => 'charger']);
        Route::patch('charger/{charger}/pickup', [ChargerTransactionController::class, 'togglePickup'])->name('charger.pickup');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('rooms', RoomController::class)
            ->except(['show', 'destroy'])
            ->parameters(['rooms' => 'room']);
        Route::patch('rooms/{room}/toggle', [RoomController::class, 'toggle'])->name('rooms.toggle');

        Route::resource('charger-items', ChargerItemController::class)
            ->except(['show', 'destroy'])
            ->parameters(['charger-items' => 'chargerItem']);
        Route::patch('charger-items/{chargerItem}/toggle', [ChargerItemController::class, 'toggle'])->name('charger-items.toggle');

        Route::resource('users', UserController::class)
            ->except(['show', 'destroy'])
            ->parameters(['users' => 'user']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('reports/shopee', [ReportController::class, 'shopee'])->name('reports.shopee');
        Route::get('reports/shopee/export', [ReportController::class, 'exportShopee'])->name('reports.shopee.export');
        Route::get('reports/charger', [ReportController::class, 'charger'])->name('reports.charger');
        Route::get('reports/charger/export', [ReportController::class, 'exportCharger'])->name('reports.charger.export');
    });
});
