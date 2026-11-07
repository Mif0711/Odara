<form method="GET" action="{{ $action }}" class="report-period-form" data-mp>
    <input type="hidden" name="month" value="{{ $period['month'] }}" data-mp-month>
    <input type="hidden" name="year" value="{{ $period['year'] }}" data-mp-year>

    <div class="monthpicker">
        <button type="button" class="filter-date-btn has-value" data-mp-trigger>
            <svg class="icon"><use href="#i-calendar"></use></svg>
            <span data-mp-label>{{ $period['label'] }}</span>
            <svg class="icon dp-trigger-caret"><use href="#i-chevron-down"></use></svg>
        </button>

        <div class="datepicker-popup monthpicker-popup" data-mp-popup hidden>
            <div class="dp-header">
                <button type="button" class="dp-nav" data-mp-year-prev aria-label="Tahun sebelumnya">
                    <svg class="icon"><use href="#i-chevron-left"></use></svg>
                </button>
                <strong class="dp-month" data-mp-year-label>{{ $period['year'] }}</strong>
                <button type="button" class="dp-nav" data-mp-year-next aria-label="Tahun berikutnya">
                    <svg class="icon"><use href="#i-chevron-right"></use></svg>
                </button>
            </div>

            <div class="monthpicker-grid">
                @foreach ($period['months'] as $number => $name)
                    <button type="button" class="month-option {{ $number === $period['month'] ? 'is-selected' : '' }}"
                            data-mp-month-option="{{ $number }}">
                        {{ $name }}
                    </button>
                @endforeach
            </div>

            <div class="dp-footer">
                <button type="button" class="dp-text-btn" data-mp-current>Bulan ini</button>
                <span class="dp-footer-spacer"></span>
                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
            </div>
        </div>
    </div>

    <span class="report-period-hint">Pilih bulan dan tahun untuk melihat rekap.</span>
</form>
