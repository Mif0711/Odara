@extends('layouts.auth')

@section('title', 'Buat Admin Pertama')

@section('content')
    <div class="auth-form-head">
        <span class="auth-badge">Persiapan Awal</span>
        <h2>Buat Akun Admin</h2>
        <p>Database masih kosong. Buat akun Admin pertama untuk mulai menggunakan ODARA.</p>
    </div>

    @if ($errors->any())
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

    <form method="POST" action="{{ route('setup.store') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="name">Nama Admin</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="input @error('name') is-invalid @enderror"
                   placeholder="Nama lengkap" required autofocus autocomplete="name">
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input @error('email') is-invalid @enderror"
                   placeholder="nama@odara.id" required autocomplete="username">
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-grid">
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password"
                       class="input @error('password') is-invalid @enderror"
                       placeholder="Minimal 8 karakter" required autocomplete="new-password">
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Ulangi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="input" placeholder="Ulangi password" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            Buat Akun Admin
            <svg class="icon"><use href="#i-arrow-right"></use></svg>
        </button>
    </form>

    <p class="auth-note">
        Setelah akun Admin dibuat, Anda dapat menambahkan akun Petugas dan data kamar melalui menu Administrasi.
    </p>
@endsection
