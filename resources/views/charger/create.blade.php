@extends('layouts.app')

@section('title', 'Catat Transaksi Charger')
@section('page-title', 'Catat Transaksi Charger')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('charger.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke daftar
            </a>
            <h1>Catat Transaksi Charger</h1>
            <p>Isi data pelanggan, pilih barang dan kamar, lalu lengkapi QTY transaksi.</p>
        </div>
    </div>

    @include('partials.transaction-form', [
        'type' => 'charger',
        'action' => route('charger.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Transaksi',
        'cancelUrl' => route('charger.index'),
    ])
@endsection
