<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login del administrador.
     */
    public function showLogin()
    {
        if (session('admin_token')) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }

    /**
     * Procesa el login enviando las credenciales a la FastAPI.
     * Consulta la tabla 'usuarios' en los campos 'correo' y 'contrasena'.
     */
    public function login(Request $request)
    {
        $request->validate([
            'correo'     => 'required|email',
            'contrasena' => 'required|string',
        ], [
            'correo.required'     => 'El correo es obligatorio.',
            'correo.email'        => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');

        try {
            $response = Http::timeout(8)->post("{$apiUrl}/login", [
                'correo'     => $request->correo,
                'contrasena' => $request->contrasena,
            ]);

            if ($response->successful()) {
                $data     = $response->json();
                $user     = $data['user'] ?? [];
                $rol      = $user['rol'] ?? null;

                // Solo permitir acceso a administradores (id_rol = 3)
                if ($rol !== 3) {
                    return back()->withErrors([
                        'correo' => 'Acceso restringido. Solo administradores pueden ingresar aquí.',
                    ])->withInput(['correo' => $request->correo]);
                }

                // Guardar datos de sesión
                session([
                    'admin_token'  => $data['access_token'],
                    'admin_id'     => $user['id'] ?? null,
                    'admin_nombre' => $user['nombre'] ?? 'Administrador',
                    'admin_rol'    => $rol,
                ]);

                return redirect('/admin/dashboard')->with('success', '¡Bienvenido, ' . ($user['nombre'] ?? 'Administrador') . '!');
            }

            // Error 401: credenciales incorrectas
            if ($response->status() === 401) {
                return back()->withErrors([
                    'correo' => 'Correo o contraseña incorrectos.',
                ])->withInput(['correo' => $request->correo]);
            }

            // Error 400: cuenta inactiva u otro error
            $detail = $response->json('detail', 'Error al iniciar sesión.');
            if (str_contains(strtolower($detail), 'inactive')) {
                $detail = 'Tu cuenta está suspendida. Contacta al administrador.';
            }

            return back()->withErrors(['correo' => $detail])
                         ->withInput(['correo' => $request->correo]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'correo' => 'No se pudo conectar con el servidor. Verifica que la API esté activa.',
            ])->withInput(['correo' => $request->correo]);
        }
    }

    /**
     * Cierra la sesión del administrador.
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'admin_token',
            'admin_id',
            'admin_nombre',
            'admin_rol',
        ]);
        return redirect('/admin/login')->with('success', 'Has cerrado sesión correctamente.');
    }
}
