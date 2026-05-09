<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        // Pastikan user sudah login
        if (!auth()->check()) {

            abort(403, 'Unauthorized');
        }

        // Ambil user yang sedang login
        $user = auth()->user();

        // Cek apakah role user sesuai
        if (!in_array($user->role, $roles)) {

            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}
