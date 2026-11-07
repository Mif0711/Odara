@extends('layouts.app')

@section('title', 'Tambah Kamar')
@section('page-title', 'Tambah Kamar')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.rooms.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke data kamar
            </a>
            <h1>Tambah Kamar</h1>
            <p>Buat data kamar baru untuk pilihan transaksi.</p>
        </div>
    </div>

    @include('admin.rooms._form')
@endsection
