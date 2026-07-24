<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        $user = Auth::user();
        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'No tienes permisos suficientes para acceder a este apartado.');
        }

        return $next($request);
    }
}
