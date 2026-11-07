<?php

namespace App\Http\Controllers;

use App\Models\ChargerTransaction;
use App\Models\ShopeeTransaction;
use App\Support\XlsxReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function shopee(Request $request): View
    {
        $period = $this->resolvePeriod($request);

        $monthQuery = ShopeeTransaction::query()
            ->whereYear('transaction_date', $period['year'])
            ->whereMonth('transaction_date', $period['month']);

        $yearQuery = ShopeeTransaction::query()
            ->whereYear('transaction_date', $period['year']);

        $monthSummary = (clone $monthQuery)
            ->selectRaw('COUNT(*) as transaction_total, COALESCE(SUM(item_price), 0) as price_total, COALESCE(SUM(service_fee), 0) as service_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total, COUNT(DISTINCT user_id) as officer_total')
            ->first();

        $yearSummary = (clone $yearQuery)
            ->selectRaw('COUNT(*) as transaction_total, COALESCE(SUM(item_price), 0) as price_total, COALESCE(SUM(service_fee), 0) as service_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total, COUNT(DISTINCT user_id) as officer_total')
            ->first();

        $monthlyRows = ShopeeTransaction::query()
            ->whereYear('transaction_date', $period['year'])
            ->selectRaw($this->monthExpression().' as period_month, COUNT(*) as transaction_total, COALESCE(SUM(item_price), 0) as price_total, COALESCE(SUM(service_fee), 0) as service_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total')
            ->groupBy('period_month')
            ->orderBy('period_month')
            ->get()
            ->keyBy('period_month');

        $officerRows = (clone $monthQuery)
            ->join('users', 'users.id', '=', 'shopee_transactions.user_id')
            ->selectRaw('users.id as user_id, users.name as officer_name, COUNT(shopee_transactions.id) as transaction_total, COALESCE(SUM(shopee_transactions.item_price), 0) as price_total, COALESCE(SUM(shopee_transactions.service_fee), 0) as service_total, COALESCE(SUM(CASE WHEN shopee_transactions.is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN shopee_transactions.is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('transaction_total')
            ->get();

        $transactions = (clone $monthQuery)
            ->with(['room', 'user'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.shopee', [
            'period' => $period,
            'monthSummary' => $monthSummary,
            'yearSummary' => $yearSummary,
            'monthlyRows' => $monthlyRows,
            'officerRows' => $officerRows,
            'transactions' => $transactions,
        ]);
    }

    public function charger(Request $request): View
    {
        $period = $this->resolvePeriod($request);

        $monthQuery = ChargerTransaction::query()
            ->whereYear('transaction_date', $period['year'])
            ->whereMonth('transaction_date', $period['month']);

        $yearQuery = ChargerTransaction::query()
            ->whereYear('transaction_date', $period['year']);

        $monthSummary = (clone $monthQuery)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(qty), 0) as qty_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total, COUNT(DISTINCT user_id) as officer_total')
            ->first();

        $yearSummary = (clone $yearQuery)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(qty), 0) as qty_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total, COUNT(DISTINCT user_id) as officer_total')
            ->first();

        $monthlyRows = ChargerTransaction::query()
            ->whereYear('transaction_date', $period['year'])
            ->selectRaw($this->monthExpression().' as period_month, COUNT(*) as total, COALESCE(SUM(qty), 0) as qty_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total')
            ->groupBy('period_month')
            ->orderBy('period_month')
            ->get()
            ->keyBy('period_month');

        $officerRows = (clone $monthQuery)
            ->join('users', 'users.id', '=', 'charger_transactions.user_id')
            ->selectRaw('users.id as user_id, users.name as officer_name, COUNT(charger_transactions.id) as total, COALESCE(SUM(charger_transactions.qty), 0) as qty_total, COALESCE(SUM(CASE WHEN charger_transactions.is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN charger_transactions.is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get();

        $transactions = (clone $monthQuery)
            ->with(['room', 'user', 'chargerItem'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.charger', [
            'period' => $period,
            'monthSummary' => $monthSummary,
            'yearSummary' => $yearSummary,
            'monthlyRows' => $monthlyRows,
            'officerRows' => $officerRows,
            'transactions' => $transactions,
        ]);
    }

    public function exportShopee(Request $request): StreamedResponse
    {
        $period = $this->resolvePeriod($request);

        $transactions = ShopeeTransaction::query()
            ->with(['room', 'user'])
            ->whereYear('transaction_date', $period['year'])
            ->whereMonth('transaction_date', $period['month'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $rows = [];
        $priceTotal = 0.0;
        $serviceTotal = 0.0;

        foreach ($transactions as $index => $transaction) {
            $price = (float) $transaction->item_price;
            $service = (float) $transaction->service_fee;
            $priceTotal += $price;
            $serviceTotal += $service;

            $rows[] = [
                $index + 1,
                $transaction->transaction_date->format('d/m/Y'),
                $transaction->receipt_number ?: '-',
                $transaction->customer_name,
                $transaction->room?->name ?? '-',
                $transaction->user?->name ?? '-',
                $transaction->item_type,
                $price,
                $service,
                $price + $service,
                $transaction->is_picked_up ? 'Sudah diambil' : 'Belum diambil',
                $transaction->notes ?? '',
            ];
        }

        return $this->downloadExcel(
            'laporan-shopee-'.$this->periodFilename($period).'.xlsx',
            XlsxReport::make(
                sheetName: 'Laporan Shopee',
                title: 'Laporan Shopee',
                meta: [
                    'Periode' => $period['label'],
                    'Diekspor' => now()->translatedFormat('d F Y H:i'),
                ],
                headers: [
                    'No', 'Tanggal', 'No Resi', 'Pelanggan', 'Kamar', 'Petugas',
                    'Jenis Barang', 'Harga Barang', 'Jasa', 'Grand Total', 'Status', 'Catatan',
                ],
                rows: $rows,
                totals: ['TOTAL', null, null, null, null, null, null, $priceTotal, $serviceTotal, $priceTotal + $serviceTotal, null, null],
                numericColumns: [7 => 'decimal', 8 => 'decimal', 9 => 'decimal'],
                widths: [6, 12, 18, 26, 18, 22, 24, 15, 12, 15, 15, 32],
            ),
        );
    }

    public function exportCharger(Request $request): StreamedResponse
    {
        $period = $this->resolvePeriod($request);

        $transactions = ChargerTransaction::query()
            ->with(['room', 'user'])
            ->whereYear('transaction_date', $period['year'])
            ->whereMonth('transaction_date', $period['month'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $rows = [];
        $qtyTotal = 0;

        foreach ($transactions as $index => $transaction) {
            $qty = (int) $transaction->qty;
            $qtyTotal += $qty;

            $rows[] = [
                $index + 1,
                $transaction->transaction_date->format('d/m/Y'),
                $transaction->pin_number ?: '-',
                $transaction->customer_name,
                $transaction->room?->name ?? '-',
                $transaction->user?->name ?? '-',
                $transaction->item_type,
                $qty,
                $transaction->is_picked_up ? 'Sudah diambil' : 'Belum diambil',
                $transaction->notes ?? '',
            ];
        }

        return $this->downloadExcel(
            'laporan-charger-'.$this->periodFilename($period).'.xlsx',
            XlsxReport::make(
                sheetName: 'Laporan Charger',
                title: 'Laporan Charger',
                meta: [
                    'Periode' => $period['label'],
                    'Diekspor' => now()->translatedFormat('d F Y H:i'),
                ],
                headers: [
                    'No', 'Tanggal', 'Nomor PIN', 'Pelanggan', 'Kamar', 'Petugas',
                    'Barang Charger', 'QTY', 'Status', 'Catatan',
                ],
                rows: $rows,
                totals: ['TOTAL', null, null, null, null, null, null, $qtyTotal, null, null],
                numericColumns: [7 => 'integer'],
                widths: [6, 12, 18, 26, 18, 22, 26, 10, 15, 32],
            ),
        );
    }

    private function downloadExcel(string $filename, string $contents): StreamedResponse
    {
        return response()->streamDownload(function () use ($contents): void {
            echo $contents;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @param  array{month: int, year: int}  $period
     */
    private function periodFilename(array $period): string
    {
        return sprintf('%04d-%02d', $period['year'], $period['month']);
    }

    /**
     * Ekspresi SQL nomor bulan yang kompatibel dengan driver database aktif.
     */
    private function monthExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%m', transaction_date) AS INTEGER)",
            'pgsql' => 'CAST(EXTRACT(MONTH FROM transaction_date) AS INTEGER)',
            default => 'MONTH(transaction_date)',
        };
    }

    /**
     * @return array{month: int, year: int, label: string, month_label: string, year_label: string, months: array<int, string>}
     */
    private function resolvePeriod(Request $request): array
    {
        $now = now();

        $month = (int) $request->query('month', $now->month);
        $year = (int) $request->query('year', $now->year);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        if ($year < 2000 || $year > $now->year + 5) {
            $year = $now->year;
        }

        $start = Carbon::create($year, $month, 1);

        return [
            'month' => $month,
            'year' => $year,
            'label' => $start->translatedFormat('F Y'),
            'month_label' => $start->translatedFormat('F'),
            'year_label' => (string) $year,
            'months' => [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ],
        ];
    }
}
