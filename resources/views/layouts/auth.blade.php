@php
    $hasUsers = \App\Models\User::query()->exists();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b1f1c">
    <title>@yield('title', 'Masuk') · ODARA</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/odara.css') }}">
</head>
<body class="auth-body">
    @include('partials.icons')

    <div class="auth-shell">
        <aside class="auth-brand">
            <a href="{{ route('home') }}" class="brand brand-light">
                <span class="brand-mark">O</span>
                <span class="brand-copy">
                    <strong>ODARA</strong>
                    <small>Sistem Informasi Data Operasional</small>
                </span>
            </a>

            <div class="auth-brand-copy">
                <span class="auth-kicker">Area Internal</span>
                <h1>Operasional toko,<br>lebih rapi dan cepat.</h1>
                <p>Satu tempat untuk mencatat transaksi Shopee dan Charger, mengelola kamar, serta memantau aktivitas harian.</p>

                <ul class="auth-points">
                    <li><svg class="icon"><use href="#i-check"></use></svg> Pencatatan transaksi yang cepat</li>
                    <li><svg class="icon"><use href="#i-check"></use></svg> Filter tanggal modern</li>
                    <li><svg class="icon"><use href="#i-check"></use></svg> Hak akses Admin &amp; Petugas</li>
                </ul>
            </div>

            <span class="auth-brand-foot">&copy; {{ date('Y') }} ODARA. Akses khusus internal.</span>
        </aside>

        <main class="auth-panel">
            <div class="auth-card">
                <div class="auth-mobile-brand">
                    <span class="brand-mark">O</span>
                    <div>
                        <strong>ODARA</strong>
                        <small>Sistem Informasi Data Operasional</small>
                    </div>
                </div>

                @yield('content')
            </div>

            @if ($hasUsers)
                <p class="auth-footer-note">Belum punya akses? Hubungi Admin untuk pembuatan akun internal.</p>
            @endif
        </main>
    </div>

    <script src="{{ asset('js/odara.js') }}" defer></script>
</body>
</html>
