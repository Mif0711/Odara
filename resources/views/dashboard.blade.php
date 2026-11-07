@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $monthRevenue = $shopeeRevenue;
@endphp

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">{{ $isAdmin ? 'Ringkasan Administrasi' : 'Ringkasan Operasional' }}</span>
            <h1>Selamat datang, {{ $firstName }}</h1>
            <p>
                @if ($isAdmin)
                    Pantau data master dan akun internal ODARA.
                @else
                    Pantau aktivitas transaksi dan operasional ODARA hari ini.
                @endif
            </p>
        </div>
        @if (! $isAdmin)
            <div class="page-actions">
                <a href="{{ route('shopee.create') }}" class="btn btn-primary">
                    <svg class="icon"><use href="#i-plus"></use></svg>
                    Catat Shopee
                </a>
                <a href="{{ route('charger.create') }}" class="btn btn-outline">
                    <svg class="icon"><use href="#i-plus"></use></svg>
                    Catat Charger
                </a>
            </div>
        @endif
    </div>

    @if ($isAdmin)
        <section class="stats-grid" aria-label="Ringkasan data master">
            <article class="stat-card">
                <div class="stat-icon stat-icon-teal">
                    <svg class="icon"><use href="#i-door"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Kamar Aktif</span>
                    <strong class="stat-value">{{ number_format($roomsCount, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Tersedia untuk transaksi</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg class="icon"><use href="#i-users"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Petugas Aktif</span>
                    <strong class="stat-value">{{ number_format($officersCount, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Akun Petugas internal</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <svg class="icon"><use href="#i-box"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Barang Charger Aktif</span>
                    <strong class="stat-value">{{ number_format($chargerItemsCount, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Pilihan pada transaksi Charger</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-violet">
                    <svg class="icon"><use href="#i-shield"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Akun Internal Aktif</span>
                    <strong class="stat-value">{{ number_format($usersCount, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Admin dan Petugas</span>
                </div>
            </article>
        </section>

        <div class="info-banner">
            <svg class="icon"><use href="#i-alert-circle"></use></svg>
            <p>Transaksi Shopee dan Charger sepenuhnya dicatat dan dikelola oleh Petugas. Admin berfokus pada data kamar, data barang Charger, dan akun internal.</p>
        </div>

        <section class="mini-stats">
            <a href="{{ route('admin.rooms.index') }}" class="mini-stat">
                <span class="mini-stat-icon"><svg class="icon"><use href="#i-door"></use></svg></span>
                <span>
                    <small>Data Kamar</small>
                    <strong>{{ $roomsCount }} aktif</strong>
                </span>
                <svg class="icon mini-stat-arrow"><use href="#i-arrow-right"></use></svg>
            </a>
            <a href="{{ route('admin.charger-items.index') }}" class="mini-stat">
                <span class="mini-stat-icon"><svg class="icon"><use href="#i-box"></use></svg></span>
                <span>
                    <small>Data Barang Charger</small>
                    <strong>{{ $chargerItemsCount }} aktif</strong>
                </span>
                <svg class="icon mini-stat-arrow"><use href="#i-arrow-right"></use></svg>
            </a>
            <a href="{{ route('admin.users.index') }}" class="mini-stat">
                <span class="mini-stat-icon"><svg class="icon"><use href="#i-users"></use></svg></span>
                <span>
                    <small>Akun Internal</small>
                    <strong>{{ $usersCount }} aktif</strong>
                </span>
                <svg class="icon mini-stat-arrow"><use href="#i-arrow-right"></use></svg>
            </a>
        </section>
    @else
        <section class="stats-grid" aria-label="Ringkasan angka">
            <article class="stat-card">
                <div class="stat-icon stat-icon-teal">
                    <svg class="icon"><use href="#i-bag"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Transaksi Shopee</span>
                    <strong class="stat-value">{{ number_format($shopeeTotal, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Total transaksi tercatat</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <svg class="icon"><use href="#i-zap"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Transaksi Charger</span>
                    <strong class="stat-value">{{ number_format($chargerTotal, 0, ',', '.') }}</strong>
                    <span class="stat-meta">Total transaksi tercatat</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg class="icon"><use href="#i-clock"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Transaksi Hari Ini</span>
                    <strong class="stat-value">{{ number_format($todayTotal, 0, ',', '.') }}</strong>
                    <span class="stat-meta">{{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon stat-icon-violet">
                    <svg class="icon"><use href="#i-wallet"></use></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Pendapatan Bulan Ini</span>
                    <strong class="stat-value">{{ \App\Support\Money::idr($monthRevenue) }}</strong>
                    <span class="stat-meta">{{ now()->translatedFormat('F Y') }} · transaksi Shopee</span>
                </div>
            </article>
        </section>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Transaksi Terbaru</h2>
                    <p>Transaksi terakhir yang dapat Anda akses.</p>
                </div>
                <div class="panel-head-actions">
                    <a href="{{ route('shopee.index') }}" class="text-link">Shopee</a>
                    <a href="{{ route('charger.index') }}" class="text-link">Charger</a>
                </div>
            </div>

            @if ($recent->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><svg class="icon"><use href="#i-box"></use></svg></div>
                    <h3>Belum ada transaksi</h3>
                    <p>Mulai catat transaksi pertama Anda melalui tombol di atas.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Kamar</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent as $item)
                                @php($isShopee = $item instanceof \App\Models\ShopeeTransaction)
                                <tr>
                                    <td data-label="Jenis">
                                        <span class="type-badge {{ $isShopee ? 'type-shopee' : 'type-charger' }}">
                                            <svg class="icon"><use href="{{ $isShopee ? '#i-bag' : '#i-zap' }}"></use></svg>
                                            {{ $isShopee ? 'Shopee' : 'Charger' }}
                                        </span>
                                    </td>
                                    <td data-label="Tanggal">{{ $item->transaction_date->translatedFormat('d M Y') }}</td>
                                    <td data-label="Pelanggan">
                                        <strong>{{ $item->customer_name }}</strong>
                                        <small class="cell-sub">{{ $item->item_type }}</small>
                                    </td>
                                    <td data-label="Kamar">{{ $item->room?->name ?? '—' }}</td>
                                    <td data-label="Status">
                                        <span class="badge {{ $item->is_picked_up ? 'badge-success' : 'badge-warning' }}">
                                            {{ $item->is_picked_up ? 'Sudah diambil' : 'Belum diambil' }}
                                        </span>
                                    </td>
                                    <td data-label="Total">
                                        @if ($isShopee)
                                            <strong>{{ \App\Support\Money::idr($item->total) }}</strong>
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>
                                    <td data-label="Petugas">{{ $item->user?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
