@extends('layouts.app')

@section('title', 'Edit Transaksi Charger')
@section('page-title', 'Edit Transaksi Charger')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('charger.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke daftar
            </a>
            <h1>Edit Transaksi Charger</h1>
            <p>Perbarui data transaksi Charger milik {{ $transaction->customer_name }}.</p>        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('charger.destroy', $transaction) }}" data-confirm="Hapus transaksi Charger ini? Tindakan ini tidak dapat dibatalkan.">
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
        'type' => 'charger',
        'action' => route('charger.update', $transaction),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'cancelUrl' => route('charger.index'),
    ])
@endsection
