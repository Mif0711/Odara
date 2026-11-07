<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPetugas
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPetugas()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Petugas.');
        }

        return $next($request);
    }
}
