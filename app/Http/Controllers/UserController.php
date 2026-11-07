<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'currentUser' => $request->user(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'account' => new User(['role' => User::ROLE_PETUGAS, 'is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PETUGAS])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun internal berhasil dibuat.');
    }

    public function edit(Request $request, User $user): View
    {
        return view('admin.users.edit', [
            'account' => $user,
            'currentUser' => $request->user(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PETUGAS])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $newRole = $validated['role'];
        $newActive = $request->boolean('is_active');

        if ($user->is($currentUser)) {
            if ($newRole !== $user->role) {
                throw ValidationException::withMessages([
                    'role' => 'Anda tidak dapat mengubah role akun sendiri.',
                ]);
            }

            if ($newActive !== $user->is_active) {
                throw ValidationException::withMessages([
                    'is_active' => 'Anda tidak dapat mengubah status akun sendiri.',
                ]);
            }
        }

        if ($user->isAdmin()) {
            $losingAdmin = $newRole !== User::ROLE_ADMIN || ! $newActive;

            if ($losingAdmin && $this->otherActiveAdmins($user) === 0) {
                throw ValidationException::withMessages([
                    'role' => 'Minimal harus ada satu Admin aktif di sistem.',
                ]);
            }
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $newRole,
            'is_active' => $newActive,
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun internal berhasil diperbarui.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->isAdmin() && $user->is_active && $this->otherActiveAdmins($user) === 0) {
            return back()->with('error', 'Minimal harus ada satu Admin aktif di sistem.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', $user->is_active
                ? 'Akun "'.$user->name.'" berhasil diaktifkan.'
                : 'Akun "'.$user->name.'" berhasil dinonaktifkan.');
    }

    private function otherActiveAdmins(User $except): int
    {
        return User::query()
            ->where('role', User::ROLE_ADMIN)
            ->where('is_active', true)
            ->whereKeyNot($except->getKey())
            ->count();
    }
}
