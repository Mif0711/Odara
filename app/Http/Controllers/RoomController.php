<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::query()
            ->withCount(['shopeeTransactions', 'chargerTransactions'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('admin.rooms.create', ['room' => new Room(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Room::create($data);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $room->update($this->validatedData($request, $room));

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function toggle(Room $room): RedirectResponse
    {
        $room->update(['is_active' => ! $room->is_active]);

        if ($room->is_active) {
            return redirect()
                ->route('admin.rooms.index')
                ->with('success', 'Kamar "'.$room->name.'" berhasil diaktifkan.');
        }

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar "'.$room->name.'" berhasil dinonaktifkan. Riwayat transaksi lama tetap tersimpan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Room $room = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rooms', 'name')->ignore($room?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
