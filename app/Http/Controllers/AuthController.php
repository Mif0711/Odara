<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (User::query()->doesntExist()) {
            return redirect()->route('setup');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang nonaktif. Silakan hubungi Admin.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Selamat datang, '.$user->name.'!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari ODARA.');
    }

    public function showSetup(): View|RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('login');
        }

        return view('auth.setup');
    }

    public function setup(Request $request): RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Akun Admin pertama berhasil dibuat. Selamat datang di ODARA!');
    }
}
