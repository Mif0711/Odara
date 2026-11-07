@extends('layouts.app')

@section('title', 'Data Kamar')
@section('page-title', 'Data Kamar')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Administrasi</span>
            <h1>Data Kamar</h1>
            <p>Kelola kamar yang tersedia untuk pilihan transaksi Shopee dan Charger.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                <svg class="icon"><use href="#i-plus"></use></svg>
                Tambah Kamar
            </a>
        </div>
    </div>

    <div class="info-banner">
        <svg class="icon"><use href="#i-alert-circle"></use></svg>
        <p>Kamar yang dinonaktifkan tidak akan muncul pada formulir transaksi baru, tetapi tetap tercatat pada transaksi lama.</p>
    </div>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Daftar Kamar</h2>
                <p>{{ $rooms->count() }} kamar terdaftar · {{ $rooms->where('is_active', true)->count() }} aktif</p>
            </div>
        </div>

        @if ($rooms->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-door"></use></svg></div>
                <h3>Belum ada data kamar</h3>
                <p>Tambahkan kamar terlebih dahulu agar Petugas dapat mencatat transaksi.</p>
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                    <svg class="icon"><use href="#i-plus"></use></svg>
                    Tambah Kamar
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kamar</th>
                            <th>Keterangan</th>
                            <th>Transaksi</th>
                            <th>Status</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rooms as $room)
                            <tr>
                                <td data-label="Nama Kamar">
                                    <strong>{{ $room->name }}</strong>
                                </td>
                                <td data-label="Keterangan">
                                    {{ $room->description ?: '—' }}
                                </td>
                                <td data-label="Transaksi">
                                    <span class="count-cell">{{ $room->shopee_transactions_count + $room->charger_transactions_count }}</span>
                                    <small class="cell-sub">Shopee {{ $room->shopee_transactions_count }} · Charger {{ $room->charger_transactions_count }}</small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge {{ $room->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $room->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="row-actions">
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="icon-btn" title="Edit kamar" aria-label="Edit kamar">
                                            <svg class="icon"><use href="#i-edit"></use></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.rooms.toggle', $room) }}" data-confirm="{{ $room->is_active ? 'Nonaktifkan kamar ini? Kamar tidak akan muncul pada transaksi baru.' : 'Aktifkan kembali kamar ini?' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $room->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                                                {{ $room->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
