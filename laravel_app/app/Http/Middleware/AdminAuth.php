<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Verifica que exista un token de administrador en la sesión.
     * Si no, redirige al login.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login')
                ->with('error', 'Debes iniciar sesión para acceder al panel.');
        }

        return $next($request);
    }
}
