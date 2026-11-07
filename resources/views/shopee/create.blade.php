@extends('layouts.app')

@section('title', 'Catat Transaksi Shopee')
@section('page-title', 'Catat Transaksi Shopee')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('shopee.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke daftar
            </a>
            <h1>Catat Transaksi Shopee</h1>
            <p>Isi data pelanggan, pilih kamar, lalu lengkapi biaya transaksi.</p>
        </div>
    </div>

    @include('partials.transaction-form', [
        'type' => 'shopee',
        'action' => route('shopee.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Transaksi',
        'cancelUrl' => route('shopee.index'),
    ])
@endsection
