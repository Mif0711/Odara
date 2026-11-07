@extends('layouts.app')

@section('title', 'Laporan Charger')
@section('page-title', 'Laporan Charger')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Laporan Admin</span>
            <h1>Laporan Charger</h1>
            <p>Rekap seluruh transaksi Charger yang dicatat oleh Petugas, lengkap dengan total bulanan dan tahunan.</p>
        </div>
        <div class="page-actions">
            <a class="btn btn-outline" href="{{ route('admin.reports.charger.export', ['month' => $period['month'], 'year' => $period['year']]) }}"
               title="Unduh laporan bulan terpilih sebagai tabel Excel (.xlsx)">
                <svg class="icon"><use href="#i-download"></use></svg>
                Export Excel (.xlsx)
            </a>
        </div>
    </div>

    @include('admin.reports._tabs', ['active' => 'charger'])

    <section class="panel filter-panel">
        <div class="panel-head panel-head-compact">
            <div>
                <h2>Periode Laporan</h2>
                <p>Pilih bulan dan tahun yang ingin diperiksa.</p>
            </div>
        </div>

        @include('admin.reports._period-filter', [
            'action' => route('admin.reports.charger'),
            'period' => $period,
        ])
    </section>

    <section class="stats-grid" aria-label="Ringkasan laporan Charger">
        <article class="stat-card">
            <div class="stat-icon stat-icon-teal">
                <svg class="icon"><use href="#i-zap"></use></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Transaksi {{ $period['label'] }}</span>
                <strong class="stat-value">{{ number_format($monthSummary->total ?? 0, 0, ',', '.') }}</strong>
                <span class="stat-meta">{{ number_format($monthSummary->officer_total ?? 0, 0, ',', '.') }} Petugas terlibat</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon stat-icon-amber">
                <svg class="icon"><use href="#i-box"></use></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Total QTY {{ $period['label'] }}</span>
                <strong class="stat-value">{{ number_format($monthSummary->qty_total ?? 0, 0, ',', '.') }}</strong>
                <span class="stat-meta">Jumlah barang Charger</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg class="icon"><use href="#i-calendar"></use></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Transaksi Tahun {{ $period['year_label'] }}</span>
                <strong class="stat-value">{{ number_format($yearSummary->total ?? 0, 0, ',', '.') }}</strong>
                <span class="stat-meta">{{ number_format($yearSummary->officer_total ?? 0, 0, ',', '.') }} Petugas terlibat</span>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon stat-icon-violet">
                <svg class="icon"><use href="#i-box"></use></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Total QTY Tahun {{ $period['year_label'] }}</span>
                <strong class="stat-value">{{ number_format($yearSummary->qty_total ?? 0, 0, ',', '.') }}</strong>
                <span class="stat-meta">Total keseluruhan setahun</span>
            </div>
        </article>
    </section>

    <section class="filter-summary" aria-label="Rincian bulan terpilih">
        <div class="filter-summary-item">
            <small>Total QTY {{ $period['label'] }}</small>
            <strong>{{ number_format($monthSummary->qty_total ?? 0, 0, ',', '.') }} unit</strong>
        </div>
        <div class="filter-summary-item">
            <small>Sudah diambil</small>
            <strong>{{ number_format($monthSummary->picked_total ?? 0, 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="filter-summary-item">
            <small>Belum diambil</small>
            <strong>{{ number_format($monthSummary->pending_total ?? 0, 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="filter-summary-item filter-summary-highlight">
            <small>Petugas terlibat</small>
            <strong>{{ number_format($monthSummary->officer_total ?? 0, 0, ',', '.') }} Petugas</strong>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Rekap Bulanan {{ $period['year_label'] }}</h2>
                <p>Total keseluruhan setiap bulan pada tahun {{ $period['year_label'] }}.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table table-report">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Transaksi</th>
                        <th>Total QTY</th>
                        <th>Sudah</th>
                        <th>Belum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($period['months'] as $number => $name)
                        @php($row = $monthlyRows->get($number))
                        <tr class="{{ $number === $period['month'] ? 'row-highlight' : '' }}">
                            <td data-label="Bulan"><strong>{{ $name }}</strong></td>
                            <td data-label="Transaksi">{{ number_format($row->total ?? 0, 0, ',', '.') }}</td>
                            <td data-label="Total QTY">{{ number_format($row->qty_total ?? 0, 0, ',', '.') }}</td>
                            <td data-label="Sudah">{{ number_format($row->picked_total ?? 0, 0, ',', '.') }}</td>
                            <td data-label="Belum">{{ number_format($row->pending_total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total {{ $period['year_label'] }}</strong></td>
                        <td><strong>{{ number_format($yearSummary->total ?? 0, 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($yearSummary->qty_total ?? 0, 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($yearSummary->picked_total ?? 0, 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($yearSummary->pending_total ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Rekap per Petugas — {{ $period['label'] }}</h2>
                <p>Semua aktivitas Petugas pada bulan terpilih.</p>
            </div>
        </div>

        @if ($officerRows->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-users"></use></svg></div>
                <h3>Belum ada aktivitas Petugas</h3>
                <p>Tidak ada transaksi Charger yang dicatat pada {{ $period['label'] }}.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th>Petugas</th>
                            <th>Transaksi</th>
                            <th>Total QTY</th>
                            <th>Sudah</th>
                            <th>Belum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($officerRows as $row)
                            <tr>
                                <td data-label="Petugas"><strong>{{ $row->officer_name }}</strong></td>
                                <td data-label="Transaksi">{{ number_format($row->total, 0, ',', '.') }}</td>
                                <td data-label="Total QTY">{{ number_format($row->qty_total, 0, ',', '.') }}</td>
                                <td data-label="Sudah">{{ number_format($row->picked_total, 0, ',', '.') }}</td>
                                <td data-label="Belum">{{ number_format($row->pending_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Detail Transaksi — {{ $period['label'] }}</h2>
                <p>{{ $transactions->total() }} transaksi · halaman {{ $transactions->currentPage() }} dari {{ max(1, $transactions->lastPage()) }}</p>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-box"></use></svg></div>
                <h3>Belum ada transaksi</h3>
                <p>Tidak ada transaksi Charger pada {{ $period['label'] }}.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kamar</th>
                            <th>Petugas</th>
                            <th>QTY</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td data-label="Tanggal">
                                    <strong class="nowrap">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</strong>
                                    <small class="cell-sub">{{ $transaction->pin_number ?: 'Tanpa PIN' }}</small>
                                </td>
                                <td data-label="Pelanggan">
                                    <strong>{{ $transaction->customer_name }}</strong>
                                    <small class="cell-sub">{{ $transaction->item_type }}</small>
                                </td>
                                <td data-label="Kamar">{{ $transaction->room?->name ?? '—' }}</td>
                                <td data-label="Petugas">{{ $transaction->user?->name ?? '—' }}</td>
                                <td data-label="QTY"><strong>{{ number_format($transaction->qty, 0, ',', '.') }}</strong></td>
                                <td data-label="Status">
                                    <span class="badge {{ $transaction->is_picked_up ? 'badge-success' : 'badge-warning' }}">
                                        {{ $transaction->is_picked_up ? 'Sudah diambil' : 'Belum diambil' }}
                                    </span>
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
