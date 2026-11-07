@extends('layouts.app')

@section('title', 'Edit Kamar')
@section('page-title', 'Edit Kamar')

@section('content')
    <div class="page-head">
        <div>
            <a href="{{ route('admin.rooms.index') }}" class="back-link">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
                Kembali ke data kamar
            </a>
            <h1>Edit Kamar</h1>
            <p>Perbarui informasi kamar {{ $room->name }}.</p>
        </div>
        <div class="page-actions">
            <form method="POST" action="{{ route('admin.rooms.toggle', $room) }}" data-confirm="{{ $room->is_active ? 'Nonaktifkan kamar ini?' : 'Aktifkan kembali kamar ini?' }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $room->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                    {{ $room->is_active ? 'Nonaktifkan Kamar' : 'Aktifkan Kamar' }}
                </button>
            </form>
        </div>
    </div>

    @include('admin.rooms._form')
@endsection
