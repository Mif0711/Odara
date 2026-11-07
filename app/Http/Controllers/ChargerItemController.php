<?php

namespace App\Http\Controllers;

use App\Models\ChargerItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChargerItemController extends Controller
{
    public function index(): View
    {
        $items = ChargerItem::query()
            ->withCount('chargerTransactions')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.charger-items.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.charger-items.create', [
            'item' => new ChargerItem(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ChargerItem::create($this->validatedData($request));

        return redirect()
            ->route('admin.charger-items.index')
            ->with('success', 'Barang Charger berhasil ditambahkan.');
    }

    public function edit(ChargerItem $chargerItem): View
    {
        return view('admin.charger-items.edit', ['item' => $chargerItem]);
    }

    public function update(Request $request, ChargerItem $chargerItem): RedirectResponse
    {
        $chargerItem->update($this->validatedData($request, $chargerItem));

        return redirect()
            ->route('admin.charger-items.index')
            ->with('success', 'Barang Charger berhasil diperbarui.');
    }

    public function toggle(ChargerItem $chargerItem): RedirectResponse
    {
        $chargerItem->update(['is_active' => ! $chargerItem->is_active]);

        if ($chargerItem->is_active) {
            return redirect()
                ->route('admin.charger-items.index')
                ->with('success', 'Barang "'.$chargerItem->name.'" berhasil diaktifkan.');
        }

        return redirect()
            ->route('admin.charger-items.index')
            ->with('success', 'Barang "'.$chargerItem->name.'" berhasil dinonaktifkan. Riwayat transaksi lama tetap tersimpan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?ChargerItem $item = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('charger_items', 'name')->ignore($item?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
