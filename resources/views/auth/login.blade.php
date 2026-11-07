@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="auth-form-head">
        <span class="auth-badge">Area Internal</span>
        <h2>Masuk ke ODARA</h2>
        <p>Gunakan akun Admin atau Petugas yang terdaftar untuk melanjutkan.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <svg class="icon"><use href="#i-alert-circle"></use></svg>
            <div>
                <strong>Tidak dapat masuk.</strong>
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" role="status">
            <svg class="icon"><use href="#i-check-circle"></use></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <div class="input-icon">
                <svg class="icon"><use href="#i-user"></use></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="input @error('email') is-invalid @enderror"
                       placeholder="nama@odara.id" required autofocus autocomplete="username">
            </div>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="input-icon">
                <svg class="icon"><use href="#i-shield"></use></svg>
                <input id="password" type="password" name="password"
                       class="input @error('password') is-invalid @enderror"
                       placeholder="Masukkan password" required autocomplete="current-password">
            </div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form-row">
            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            Masuk
            <svg class="icon"><use href="#i-arrow-right"></use></svg>
        </button>
    </form>

    <p class="auth-note">
        ODARA hanya digunakan untuk akun internal Admin dan Petugas. Pelanggan tidak memerlukan akun.
    </p>
@endsection
