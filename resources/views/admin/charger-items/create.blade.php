@extends('layouts.app')

@section('title', 'Tambah Barang Charger')
@section('page-title', 'Tambah Barang Charger')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.charger-items.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke data barang
            </a>
            <h1>Tambah Barang Charger</h1>
            <p>Buat data barang baru untuk pilihan transaksi Charger.</p>
        </div>
    </div>

    @include('admin.charger-items._form')
@endsection
