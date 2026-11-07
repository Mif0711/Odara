@extends('layouts.app')

@section('title', 'Edit Akun Internal')
@section('page-title', 'Edit Akun Internal')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.users.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke akun internal
            </a>
            <h1>Edit Akun Internal</h1>
            <p>Perbarui informasi akun {{ $account->name }}.</p>
        </div>
        @if (! $account->is($currentUser))
            <div class="page-actions">
                <form method="POST" action="{{ route('admin.users.toggle', $account) }}" data-confirm="{{ $account->is_active ? 'Nonaktifkan akun ini? Pengguna tidak akan bisa masuk.' : 'Aktifkan kembali akun ini?' }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $account->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                        {{ $account->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            </div>
        @endif
    </div>

    @include('admin.users._form')
@endsection
