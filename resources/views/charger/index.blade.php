@extends('layouts.app')

@section('title', 'Transaksi Charger')
@section('page-title', 'Transaksi Charger')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Operasional</span>
            <h1>Transaksi Charger</h1>
            <p>Catat dan kelola transaksi penitipan charger pelanggan.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('charger.create') }}" class="btn btn-primary">
                <svg class="icon"><use href="#i-plus"></use></svg>
                Tambah Transaksi
            </a>
        </div>
    </div>

    <section class="panel filter-panel">
        <div class="panel-head panel-head-compact">
            <div>
                <h2>Filter &amp; Pencarian</h2>
                <p>Pilih satu tanggal atau rentang tanggal untuk menyaring transaksi.</p>
            </div>
        </div>

        @include('partials.date-filter', [
            'action' => route('charger.index'),
            'filter' => $filter,
            'search' => $search,
            'searchPlaceholder' => 'Cari nama, barang, atau nomor PIN...',
        ])

        @if ($filter['mode'] !== 'all' || $search !== '')
            <div class="active-filter">
                <svg class="icon"><use href="#i-calendar"></use></svg>
                <span>Filter aktif: <strong>{{ $filter['label'] }}</strong></span>
                @if ($search !== '')
                    <span class="active-filter-divider"></span>
                    <span>Pencarian: <strong>{{ $search }}</strong></span>
                @endif
                <a href="{{ route('charger.index') }}" class="active-filter-reset">Hapus filter</a>
            </div>
        @endif
    </section>

    <section class="filter-summary" aria-label="Ringkasan hasil filter">
        <div class="filter-summary-item">
            <small>Transaksi ditemukan</small>
            <strong>{{ number_format($summary->total ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div class="filter-summary-item">
            <small>Total QTY</small>
            <strong>{{ number_format($summary->qty_total ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div class="filter-summary-item">
            <small>Sudah diambil</small>
            <strong>{{ number_format($summary->picked_total ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div class="filter-summary-item filter-summary-highlight">
            <small>Belum diambil</small>
            <strong>{{ number_format($summary->pending_total ?? 0, 0, ',', '.') }}</strong>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Daftar Transaksi</h2>
                <p>{{ $transactions->total() }} transaksi · halaman {{ $transactions->currentPage() }} dari {{ max(1, $transactions->lastPage()) }}</p>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-zap"></use></svg></div>
                <h3>Belum ada transaksi Charger</h3>
                <p>{{ $filter['mode'] !== 'all' || $search !== '' ? 'Tidak ada transaksi pada filter ini. Coba ubah tanggal atau reset filter.' : 'Mulai catat transaksi Charger pertama Anda.' }}</p>
                @if ($filter['mode'] !== 'all' || $search !== '')
                    <a href="{{ route('charger.index') }}" class="btn btn-outline">Reset filter</a>
                @else
                    <a href="{{ route('charger.create') }}" class="btn btn-primary">
                        <svg class="icon"><use href="#i-plus"></use></svg>
                        Tambah Transaksi
                    </a>
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table class="table table-transactions">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor PIN</th>
                            <th>Pelanggan</th>
                            <th>Kamar</th>
                            <th>QTY</th>
                            <th>Status</th>
                            <th>Petugas</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td data-label="Tanggal">
                                    <strong class="nowrap">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</strong>
                                    <small class="cell-sub">{{ $transaction->transaction_date->translatedFormat('l') }}</small>
                                </td>
                                <td data-label="Nomor PIN">
                                    @if ($transaction->pin_number)
                                        <span class="mono">{{ $transaction->pin_number }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Pelanggan">
                                    <strong>{{ $transaction->customer_name }}</strong>
                                    <small class="cell-sub">{{ $transaction->item_type }}</small>
                                    @if ($transaction->notes)
                                        <small class="cell-note" title="{{ $transaction->notes }}">{{ $transaction->notes }}</small>
                                    @endif
                                </td>
                                <td data-label="Kamar">
                                    @if ($transaction->room)
                                        <span class="room-badge {{ ! $transaction->room->is_active ? 'room-inactive' : '' }}">
                                            <svg class="icon"><use href="#i-door"></use></svg>
                                            {{ $transaction->room->name }}
                                        </span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td data-label="QTY"><span class="qty-badge">{{ $transaction->qty }}&times;</span></td>
                                <td data-label="Status">
                                    <form method="POST" action="{{ route('charger.pickup', $transaction) }}" data-confirm="{{ $transaction->is_picked_up ? 'Tandai barang ini belum diambil?' : 'Tandai barang ini sudah diambil?' }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge badge-button {{ $transaction->is_picked_up ? 'badge-success' : 'badge-warning' }}" title="Ubah status pengambilan">
                                            {{ $transaction->is_picked_up ? 'Sudah diambil' : 'Belum diambil' }}
                                        </button>
                                    </form>
                                </td>
                                <td data-label="Petugas">
                                    <span class="officer-cell">
                                        <span class="avatar avatar-xs">{{ $transaction->user?->initials() ?? '—' }}</span>
                                        {{ $transaction->user?->name ?? '—' }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="row-actions">
                                        <a href="{{ route('charger.edit', $transaction) }}" class="icon-btn" title="Edit transaksi" aria-label="Edit transaksi">
                                            <svg class="icon"><use href="#i-edit"></use></svg>
                                        </a>
                                        <form method="POST" action="{{ route('charger.destroy', $transaction) }}" data-confirm="Hapus transaksi Charger ini? Tindakan ini tidak dapat dibatalkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-danger" title="Hapus transaksi" aria-label="Hapus transaksi">
                                                <svg class="icon"><use href="#i-trash"></use></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>
@endsection
