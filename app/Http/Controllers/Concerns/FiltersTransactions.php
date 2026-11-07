<?php

namespace App\Http\Controllers\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FiltersTransactions
{
    /**
     * Parse and normalise the date filter query string.
     *
     * Supported query params: date, start_date, end_date.
     *
     * @return array{date: ?string, start_date: ?string, end_date: ?string, mode: string, label: string}
     */
    protected function resolveDateFilter(Request $request): array
    {
        $date = $this->normaliseDate($request->query('date'));
        $start = $this->normaliseDate($request->query('start_date'));
        $end = $this->normaliseDate($request->query('end_date'));

        if ($start !== null && $end !== null && $start > $end) {
            [$start, $end] = [$end, $start];
        }

        if ($date !== null) {
            $start = null;
            $end = null;
            $mode = 'single';
            $label = Carbon::createFromFormat('Y-m-d', $date)->translatedFormat('d F Y');
        } elseif ($start !== null && $end !== null) {
            $mode = 'range';
            if ($start === $end) {
                $label = Carbon::createFromFormat('Y-m-d', $start)->translatedFormat('d F Y');
            } else {
                $label = Carbon::createFromFormat('Y-m-d', $start)->translatedFormat('d M Y')
                    .' – '.
                    Carbon::createFromFormat('Y-m-d', $end)->translatedFormat('d M Y');
            }
        } elseif ($start !== null) {
            $mode = 'range';
            $label = 'Sejak '.Carbon::createFromFormat('Y-m-d', $start)->translatedFormat('d M Y');
        } elseif ($end !== null) {
            $mode = 'range';
            $label = 'Sampai '.Carbon::createFromFormat('Y-m-d', $end)->translatedFormat('d M Y');
        } else {
            $mode = 'all';
            $label = 'Semua tanggal';
        }

        return [
            'date' => $date,
            'start_date' => $start,
            'end_date' => $end,
            'mode' => $mode,
            'label' => $label,
        ];
    }

    protected function applyDateFilter(Builder $query, array $filter, string $column = 'transaction_date'): Builder
    {
        if ($filter['date'] !== null) {
            $query->whereDate($column, $filter['date']);
        }

        if ($filter['start_date'] !== null) {
            $query->whereDate($column, '>=', $filter['start_date']);
        }

        if ($filter['end_date'] !== null) {
            $query->whereDate($column, '<=', $filter['end_date']);
        }

        return $query;
    }

    private function normaliseDate(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $value));

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
