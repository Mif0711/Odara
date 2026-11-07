@extends('layouts.app')

@section('title', 'Data Barang Charger')
@section('page-title', 'Data Barang Charger')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Administrasi</span>
            <h1>Data Barang Charger</h1>
            <p>Kelola daftar barang yang dapat dipilih Petugas pada transaksi Charger.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.charger-items.create') }}" class="btn btn-primary">
                <svg class="icon"><use href="#i-plus"></use></svg>
                Tambah Barang
            </a>
        </div>
    </div>

    <div class="info-banner">
        <svg class="icon"><use href="#i-alert-circle"></use></svg>
        <p>Barang yang dinonaktifkan tidak akan muncul pada formulir transaksi Charger baru, tetapi tetap tercatat pada transaksi lama.</p>
    </div>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Daftar Barang</h2>
                <p>{{ $items->count() }} barang terdaftar · {{ $items->where('is_active', true)->count() }} aktif</p>
            </div>
        </div>

        @if ($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-box"></use></svg></div>
                <h3>Belum ada data barang Charger</h3>
                <p>Tambahkan barang terlebih dahulu agar Petugas dapat memilihnya saat mencatat transaksi Charger.</p>
                <a href="{{ route('admin.charger-items.create') }}" class="btn btn-primary">
                    <svg class="icon"><use href="#i-plus"></use></svg>
                    Tambah Barang
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Keterangan</th>
                            <th>Transaksi</th>
                            <th>Status</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td data-label="Nama Barang">
                                    <strong>{{ $item->name }}</strong>
                                </td>
                                <td data-label="Keterangan">
                                    {{ $item->description ?: '—' }}
                                </td>
                                <td data-label="Transaksi">
                                    <span class="count-cell">{{ $item->charger_transactions_count }}</span>
                                    <small class="cell-sub">Transaksi Charger</small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="row-actions">
                                        <a href="{{ route('admin.charger-items.edit', $item) }}" class="icon-btn" title="Edit barang" aria-label="Edit barang">
                                            <svg class="icon"><use href="#i-edit"></use></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.charger-items.toggle', $item) }}" data-confirm="{{ $item->is_active ? 'Nonaktifkan barang ini? Barang tidak akan muncul pada transaksi Charger baru.' : 'Aktifkan kembali barang ini?' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
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
