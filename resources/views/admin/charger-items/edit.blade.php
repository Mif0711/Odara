@extends('layouts.app')

@section('title', 'Edit Barang Charger')
@section('page-title', 'Edit Barang Charger')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.charger-items.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke data barang
            </a>
            <h1>Edit Barang Charger</h1>
            <p>Perbarui informasi barang {{ $item->name }}.</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('admin.charger-items.toggle', $item) }}" data-confirm="{{ $item->is_active ? 'Nonaktifkan barang ini?' : 'Aktifkan kembali barang ini?' }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                    {{ $item->is_active ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}
                </button>
            </form>
        </div>
    </div>

    @include('admin.charger-items._form')
@endsection
