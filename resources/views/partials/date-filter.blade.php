@php
    $isFiltered = $filter['mode'] !== 'all' || $search !== '';
@endphp

<form method="GET" action="{{ $action }}" class="filter-form" data-filter-form>
    <div class="search-field">
        <svg class="icon"><use href="#i-search"></use></svg>
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ $searchPlaceholder ?? 'Cari data transaksi...' }}" aria-label="Cari transaksi">
    </div>

    <div class="datepicker" data-datepicker>
        <input type="hidden" name="date" value="{{ $filter['date'] }}" data-dp-date>
        <input type="hidden" name="start_date" value="{{ $filter['start_date'] }}" data-dp-start>
        <input type="hidden" name="end_date" value="{{ $filter['end_date'] }}" data-dp-end>

        <button type="button" class="filter-date-btn {{ $filter['mode'] !== 'all' ? 'has-value' : '' }}" data-dp-trigger>
            <svg class="icon"><use href="#i-calendar"></use></svg>
            <span data-dp-label>{{ $filter['label'] }}</span>
            <svg class="icon dp-trigger-caret"><use href="#i-chevron-down"></use></svg>
        </button>

        <div class="datepicker-popup" data-dp-popup hidden>
            <div class="dp-header">
                <button type="button" class="dp-nav" data-dp-prev aria-label="Bulan sebelumnya">
                    <svg class="icon"><use href="#i-chevron-left"></use></svg>
                </button>
                <strong class="dp-month" data-dp-month></strong>
                <button type="button" class="dp-nav" data-dp-next aria-label="Bulan berikutnya">
                    <svg class="icon"><use href="#i-chevron-right"></use></svg>
                </button>
            </div>

            <div class="dp-mode-switch">
                <button type="button" data-dp-mode="single">Satu tanggal</button>
                <button type="button" data-dp-mode="range">Rentang tanggal</button>
            </div>

            <div class="dp-weekdays" aria-hidden="true">
                <span>Sn</span><span>Sl</span><span>Rb</span><span>Km</span><span>Jm</span><span>Sb</span><span>Mg</span>
            </div>

            <div class="dp-days" data-dp-days></div>

            <div class="dp-selection" data-dp-selection></div>

            <div class="dp-footer">
                <button type="button" class="dp-text-btn" data-dp-today>Hari ini</button>
                <span class="dp-footer-spacer"></span>
                <button type="button" class="dp-text-btn dp-text-muted" data-dp-clear>Reset</button>
                <button type="button" class="btn btn-primary btn-sm" data-dp-apply>Terapkan</button>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Cari</button>

    @if ($isFiltered)
        <a href="{{ $action }}" class="btn btn-ghost">Reset filter</a>
    @endif
</form>
