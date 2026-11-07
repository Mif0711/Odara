<div class="report-tabs" role="tablist" aria-label="Jenis laporan">
    <a href="{{ route('admin.reports.shopee', ['month' => $period['month'], 'year' => $period['year']]) }}"
       class="report-tab {{ $active === 'shopee' ? 'is-active' : '' }}">
        <svg class="icon"><use href="#i-bag"></use></svg>
        Laporan Shopee
    </a>
    <a href="{{ route('admin.reports.charger', ['month' => $period['month'], 'year' => $period['year']]) }}"
       class="report-tab {{ $active === 'charger' ? 'is-active' : '' }}">
        <svg class="icon"><use href="#i-zap"></use></svg>
        Laporan Charger
    </a>
</div>
