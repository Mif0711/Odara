<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak · ODARA</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/odara.css') }}">
</head>
<body class="error-body">
    <div class="error-card">
        <span class="error-code">403</span>
        <h1>Akses Ditolak</h1>
        <p>Halaman ini hanya dapat diakses oleh Admin. Akun Petugas tidak memiliki izin untuk membuka pengelolaan data internal.</p>
        <div class="error-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
            @endauth
        </div>
    </div>
</body>
</html>
