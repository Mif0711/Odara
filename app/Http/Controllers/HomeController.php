<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index(): RedirectResponse
    {
        if (User::query()->doesntExist()) {
            return redirect()->route('setup');
        }

        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    }
}
