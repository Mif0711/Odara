<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersTransactions;
use App\Http\Controllers\Concerns\ManagesTransactions;
use App\Models\ShopeeTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopeeTransactionController extends Controller
{
    use FiltersTransactions, ManagesTransactions;

    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $this->resolveDateFilter($request);
        $search = trim((string) $request->query('q', ''));

        $query = ShopeeTransaction::query()
            ->with(['room', 'user'])
            ->visibleTo($user);

        $this->applyDateFilter($query, $filter);

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('item_type', 'like', '%'.$search.'%')
                    ->orWhere('receipt_number', 'like', '%'.$search.'%');
            });
        }

        $summary = (clone $query)
            ->reorder()
            ->selectRaw('COUNT(*) as transaction_total, COALESCE(SUM(item_price), 0) as price_total, COALESCE(SUM(service_fee), 0) as service_total')
            ->first();

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('shopee.index', [
            'transactions' => $transactions,
            'filter' => $filter,
            'search' => $search,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        return view('shopee.create', [
            'transaction' => null,
            'rooms' => $this->roomOptions(),
            'customerNames' => $this->customerNameSuggestions($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $this->assertRoomIsAvailable((int) $data['room_id']);

        $data['user_id'] = $request->user()->id;
        ShopeeTransaction::create($data);

        return redirect()
            ->route('shopee.index')
            ->with('success', 'Transaksi Shopee berhasil disimpan.');
    }

    public function edit(Request $request, ShopeeTransaction $shopee): View
    {
        $this->ensureShopeeCanModify($request->user(), $shopee);

        return view('shopee.edit', [
            'transaction' => $shopee,
            'rooms' => $this->roomOptions($shopee->room_id),
            'customerNames' => $this->customerNameSuggestions($request->user()),
        ]);
    }

    public function update(Request $request, ShopeeTransaction $shopee): RedirectResponse
    {
        $this->ensureShopeeCanModify($request->user(), $shopee);

        $data = $this->validatedData($request);
        $this->assertRoomIsAvailable((int) $data['room_id'], $shopee->room_id);

        $shopee->update($data);

        return redirect()
            ->route('shopee.index')
            ->with('success', 'Transaksi Shopee berhasil diperbarui.');
    }

    public function destroy(Request $request, ShopeeTransaction $shopee): RedirectResponse
    {
        $this->ensureShopeeCanModify($request->user(), $shopee);

        $shopee->delete();

        return redirect()
            ->route('shopee.index')
            ->with('success', 'Transaksi Shopee berhasil dihapus.');
    }

    public function togglePickup(Request $request, ShopeeTransaction $shopee): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user->isPetugas() && $shopee->user_id === $user->id,
            403,
            'Anda tidak berhak mengubah status pengambilan transaksi ini.'
        );

        $shopee->update(['is_picked_up' => ! $shopee->is_picked_up]);

        return back()->with(
            'success',
            $shopee->is_picked_up
                ? 'Barang ditandai sudah diambil.'
                : 'Barang ditandai belum diambil.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $this->normalizeRupiahFields($request, ['item_price', 'service_fee']);

        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:150'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'item_type' => ['required', 'string', 'max:150'],
            'item_price' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'service_fee' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_picked_up'] = $request->boolean('is_picked_up');

        return $validated;
    }
}
