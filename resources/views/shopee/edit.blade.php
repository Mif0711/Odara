@extends('layouts.app')

@section('title', 'Edit Transaksi Shopee')
@section('page-title', 'Edit Transaksi Shopee')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('shopee.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke daftar
            </a>
            <h1>Edit Transaksi Shopee</h1>
            <p>Perbarui data transaksi Shopee milik {{ $transaction->customer_name }}.</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('shopee.destroy', $transaction) }}" data-confirm="Hapus transaksi Shopee ini? Tindakan ini tidak dapat dibatalkan.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <svg class="icon"><use href="#i-trash"></use></svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    @include('partials.transaction-form', [
        'type' => 'shopee',
        'action' => route('shopee.update', $transaction),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'cancelUrl' => route('shopee.index'),
    ])
@endsection
