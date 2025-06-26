<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Verificar si el usuario tiene alguno de los roles especificados
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si no tiene los roles necesarios, redirigir según su rol actual
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('secretaria')) {
            return redirect()->route('secretaria.dashboard');
        } elseif ($user->hasRole('cliente')) {
            return redirect()->route('cliente.dashboard');
        }

        return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}