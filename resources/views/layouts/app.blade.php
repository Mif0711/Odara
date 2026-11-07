<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b1f1c">
    <title>@yield('title', 'Dashboard') · ODARA</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/odara.css') }}">
</head>
<body>
    @include('partials.icons')

    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-mark">O</span>
                <span class="brand-copy">
                    <strong>ODARA</strong>
                    <small>Sistem Informasi Data Operasional</small>
                </span>
            </a>

            <nav class="sidebar-nav" aria-label="Navigasi utama">
                <span class="nav-label">Utama</span>

                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <svg class="icon"><use href="#i-grid"></use></svg>
                    <span>Dashboard</span>
                </a>

                @if (auth()->user()->isPetugas())
                    <a href="{{ route('shopee.index') }}" class="nav-item {{ request()->routeIs('shopee.*') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-bag"></use></svg>
                        <span>Transaksi Shopee</span>
                    </a>

                    <a href="{{ route('charger.index') }}" class="nav-item {{ request()->routeIs('charger.*') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-zap"></use></svg>
                        <span>Transaksi Charger</span>
                    </a>
                @endif

                @if (auth()->user()->isAdmin())
                    <span class="nav-label nav-label-spaced">Administrasi</span>

                    <a href="{{ route('admin.rooms.index') }}" class="nav-item {{ request()->routeIs('admin.rooms.*') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-door"></use></svg>
                        <span>Data Kamar</span>
                    </a>

                    <a href="{{ route('admin.charger-items.index') }}" class="nav-item {{ request()->routeIs('admin.charger-items.*') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-box"></use></svg>
                        <span>Data Barang Charger</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-users"></use></svg>
                        <span>Akun Internal</span>
                    </a>

                    <span class="nav-label nav-label-spaced">Laporan</span>

                    <a href="{{ route('admin.reports.shopee') }}" class="nav-item {{ request()->routeIs('admin.reports.shopee') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-bag"></use></svg>
                        <span>Laporan Shopee</span>
                    </a>

                    <a href="{{ route('admin.reports.charger') }}" class="nav-item {{ request()->routeIs('admin.reports.charger') ? 'is-active' : '' }}">
                        <svg class="icon"><use href="#i-zap"></use></svg>
                        <span>Laporan Charger</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <span class="avatar">{{ auth()->user()->initials() }}</span>
                    <span class="sidebar-user-copy">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->roleLabel() }}</small>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-btn icon-btn-light" title="Keluar" aria-label="Keluar">
                            <svg class="icon"><use href="#i-logout"></use></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <button type="button" class="sidebar-overlay" data-sidebar-close aria-label="Tutup menu"></button>

        <div class="app-main">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="icon-btn mobile-menu" data-sidebar-toggle aria-label="Buka menu">
                        <svg class="icon"><use href="#i-menu"></use></svg>
                    </button>
                    <div class="topbar-title">
                        <span class="topbar-eyebrow">ODARA</span>
                        <strong>@yield('page-title', 'Dashboard')</strong>
                    </div>
                </div>

                <div class="topbar-right">
                    <span class="topbar-date">
                        <svg class="icon"><use href="#i-calendar"></use></svg>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="role-pill {{ auth()->user()->isAdmin() ? 'role-admin' : 'role-petugas' }}">
                        <svg class="icon"><use href="#i-shield"></use></svg>
                        {{ auth()->user()->roleLabel() }}
                    </span>
                </div>
            </header>

            <main class="content">
                @if (session('success'))
                    <div class="alert alert-success toast" role="status">
                        <svg class="icon"><use href="#i-check-circle"></use></svg>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger toast" role="alert">
                        <svg class="icon"><use href="#i-alert-circle"></use></svg>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                    </div>
                @endif

                @if ($errors->any() && ! View::hasSection('hide-error-summary'))
                    <div class="alert alert-danger" role="alert">
                        <svg class="icon"><use href="#i-alert-circle"></use></svg>
                        <div>
                            <strong>Periksa kembali data yang diisi.</strong>
                            <ul class="alert-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/odara.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
