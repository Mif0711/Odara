<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersTransactions;
use App\Http\Controllers\Concerns\ManagesTransactions;
use App\Models\ChargerTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChargerTransactionController extends Controller
{
    use FiltersTransactions, ManagesTransactions;

    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $this->resolveDateFilter($request);
        $search = trim((string) $request->query('q', ''));

        $query = ChargerTransaction::query()
            ->with(['room', 'user', 'chargerItem'])
            ->visibleTo($user);

        $this->applyDateFilter($query, $filter);

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('item_type', 'like', '%'.$search.'%')
                    ->orWhere('pin_number', 'like', '%'.$search.'%');
            });
        }

        $summary = (clone $query)
            ->reorder()
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(qty), 0) as qty_total, COALESCE(SUM(CASE WHEN is_picked_up = 1 THEN 1 ELSE 0 END), 0) as picked_total, COALESCE(SUM(CASE WHEN is_picked_up = 0 THEN 1 ELSE 0 END), 0) as pending_total')
            ->first();

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('charger.index', [
            'transactions' => $transactions,
            'filter' => $filter,
            'search' => $search,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        return view('charger.create', [
            'transaction' => null,
            'rooms' => $this->roomOptions(),
            'items' => $this->chargerItemOptions(),
            'customerNames' => $this->customerNameSuggestions($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $this->assertRoomIsAvailable((int) $data['room_id']);
        $item = $this->assertChargerItemIsAvailable((int) $data['charger_item_id']);

        $data['item_type'] = $item->name;
        $data['user_id'] = $request->user()->id;
        ChargerTransaction::create($data);

        return redirect()
            ->route('charger.index')
            ->with('success', 'Transaksi Charger berhasil disimpan.');
    }

    public function edit(Request $request, ChargerTransaction $charger): View
    {
        $this->ensureChargerCanModify($request->user(), $charger);

        return view('charger.edit', [
            'transaction' => $charger,
            'rooms' => $this->roomOptions($charger->room_id),
            'items' => $this->chargerItemOptions($charger->charger_item_id),
            'customerNames' => $this->customerNameSuggestions($request->user()),
        ]);
    }

    public function update(Request $request, ChargerTransaction $charger): RedirectResponse
    {
        $this->ensureChargerCanModify($request->user(), $charger);

        $data = $this->validatedData($request);
        $this->assertRoomIsAvailable((int) $data['room_id'], $charger->room_id);
        $item = $this->assertChargerItemIsAvailable((int) $data['charger_item_id'], $charger->charger_item_id);

        $data['item_type'] = $item->name;
        $charger->update($data);

        return redirect()
            ->route('charger.index')
            ->with('success', 'Transaksi Charger berhasil diperbarui.');
    }

    public function destroy(Request $request, ChargerTransaction $charger): RedirectResponse
    {
        $this->ensureChargerCanModify($request->user(), $charger);

        $charger->delete();

        return redirect()
            ->route('charger.index')
            ->with('success', 'Transaksi Charger berhasil dihapus.');
    }

    public function togglePickup(Request $request, ChargerTransaction $charger): RedirectResponse
    {
        $this->ensureChargerCanModify($request->user(), $charger);

        $charger->update(['is_picked_up' => ! $charger->is_picked_up]);

        return back()->with(
            'success',
            $charger->is_picked_up
                ? 'Barang ditandai sudah diambil.'
                : 'Barang ditandai belum diambil.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'pin_number' => ['required', 'integer', 'between:1,30'],
            'customer_name' => ['required', 'string', 'max:150'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'charger_item_id' => ['required', 'integer', 'exists:charger_items,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_picked_up'] = $request->boolean('is_picked_up');

        return $validated;
    }
}
