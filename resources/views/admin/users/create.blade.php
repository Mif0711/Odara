@extends('layouts.app')

@section('title', 'Tambah Akun Internal')
@section('page-title', 'Tambah Akun Internal')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.users.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke akun internal
            </a>
            <h1>Tambah Akun Internal</h1>
            <p>Buat akun baru untuk Admin atau Petugas.</p>
        </div>
    </div>

    @include('admin.users._form', ['currentUser' => auth()->user()])
@endsection
