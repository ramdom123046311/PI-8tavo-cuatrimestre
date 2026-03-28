<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;

// ── Raíz ──────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return session('admin_token')
        ? redirect('/admin/dashboard')
        : redirect('/admin/login');
});

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::get('/admin/login',  [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout',[AuthController::class, 'logout'])->name('admin.logout');

// ── Panel Admin (protegido con sesión) ────────────────────────────────────────
Route::middleware('admin.auth')->group(function () {

    Route::get('/admin/dashboard', function () {
        $apiUrl  = env('API_URL', 'http://127.0.0.1:8001/api');
        $apiData = [];
        $stats   = [
            'usuarios' => 0, 
            'medicos_activos' => 0, 
            'citas_pendientes' => 0, 
            'articulos_publicados' => 0
        ];

        try {
            $token   = session('admin_token');
            $headers = ['Authorization' => "Bearer {$token}"];

            $health = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/health");
            if ($health->successful()) {
                $apiData = $health->json();
            }

            $usuarios = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/usuarios");
            if ($usuarios->successful()) {
                $stats['usuarios'] = count($usuarios->json());
            }

            $medicos = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/medicos");
            if ($medicos->successful()) {
                $meds = $medicos->json();
                $stats['medicos_activos'] = count(array_filter($meds, fn($m) => !empty($m['aprobado'])));
            }

            $citas = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/citas");
            if ($citas->successful()) {
                $cts = $citas->json();
                $stats['citas_pendientes'] = count(array_filter($cts, fn($c) => strtolower($c['estado']) === 'pendiente'));
            }
            
            $articulos = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/articulos");
            if ($articulos->successful()) {
                $arts = $articulos->json();
                $stats['articulos_publicados'] = count(array_filter($arts, fn($a) => !empty($a['estatus'])));
            }
        } catch (\Exception $e) {   
            $apiData = ['error' => 'No se pudo conectar a la API FastAPI'];
        }

        return view('admin.dashboard', compact('apiData', 'stats'));
    });

    Route::get('/admin/verification', function () {
        $apiUrl  = env('API_URL', 'http://127.0.0.1:8001/api');
        $medicos_pendientes = [];
        try {
            $token    = session('admin_token');
            $headers  = ['Authorization' => "Bearer {$token}"];
            $med_res = Http::withHeaders($headers)->timeout(5)->get("{$apiUrl}/medicos/con-nombre");
            
            if ($med_res->successful()) {
                $todos_medicos = $med_res->json();
                // pending doctors are those where (estatus == 0 and/or aprobado == false/0)
                $medicos_pendientes = array_filter($todos_medicos, function($m) {
                    return empty($m['aprobado']) || empty($m['estatus']);
                });
            }
        } catch (\Exception $e) {
            $medicos_pendientes = [];
        }
        return view('admin.verification', compact('medicos_pendientes'));
    });
    
    // Ruta para que el admin apruebe a un medico
    Route::post('/admin/verification/approve/{id_medico}', function ($id_medico) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        $headers = ['Authorization' => "Bearer {$token}"];
        
        // Primero, obtener el id_usuario asociado al medico
        $med_res = Http::withHeaders($headers)->get("{$apiUrl}/medicos/con-nombre");
        if ($med_res->successful()) {
            $medicos = $med_res->json();
            $medico = collect($medicos)->firstWhere('id_medico', $id_medico);
            if ($medico) {
                // Actualizar estatus = 1 en usuarios (NOTA: necesitamos un endpoint. Ojo, /usuarios es POST, /usuarios/{id} PATCH)
                // Usamos form-data para PATCH en usuarios
                Http::withHeaders($headers)->asForm()->patch("{$apiUrl}/usuarios/{$medico['id_usuario']}", [
                    'estatus' => 1
                ]);
                
                // Actualizar aprobado = 1 en medico
                Http::withHeaders($headers)->patch("{$apiUrl}/medicos/{$id_medico}", [
                    'aprobado' => 1
                ]);
            }
        }
        return redirect('/admin/verification')->with('success', 'Médico aprobado correctamente.');
    });
    
    // Ruta para que el admin rechace a un medico
    Route::post('/admin/verification/reject/{id_medico}', function ($id_medico) {
         // Para rechazar (eliminar de la db)
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        $headers = ['Authorization' => "Bearer {$token}"];
        
        // Se podria llamar un endpoint para eliminar el registro si existe
        // Como no hay endpoint DELETE /medicos/{id}, lo dejaremos con estatus = 'rechazado'
        Http::withHeaders($headers)->patch("{$apiUrl}/medicos/{$id_medico}", [
            'aprobado' => 0,
            'estatus' => 'rechazado' // campo estatus en medicos de string
        ]);

        return redirect('/admin/verification')->with('error', 'Médico rechazado.');
    });

    Route::get('/admin/security', function () {
        return view('admin.security');
    });

    Route::get('/admin/users', function () {
        $apiUrl   = env('API_URL', 'http://127.0.0.1:8001/api');
        $usuarios = [];
        try {
            $token    = session('admin_token');
            $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                            ->timeout(3)->get("{$apiUrl}/usuarios");
            if ($response->successful()) {
                $usuarios = $response->json();
            }
        } catch (\Exception $e) {
            $usuarios = [];
        }
        return view('admin.users', compact('usuarios'));
    });
    
    // CRUD Usuarios - Crear
    Route::post('/admin/users', function (Illuminate\Http\Request $request) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        $data = $request->except('_token');
        // Aseguramos que el id_rol sea entero
        if (isset($data['id_rol'])) $data['id_rol'] = (int)$data['id_rol'];
        
        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->post("{$apiUrl}/usuarios", $data);

        if ($response->successful()) {
            return redirect('/admin/users')->with('success', 'Usuario creado correctamente.');
        }
        return redirect('/admin/users')->with('error', 'Error al crear usuario: ' . $response->json('detail', 'Error desconocido'));
    });

    // CRUD Usuarios - Actualizar
    Route::post('/admin/users/{id}/update', function (Illuminate\Http\Request $request, $id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        // El endpoint de fastapi es PATCH y recibe FormData para el usuario
        // pero tambien tenemos uno de medicos. El form de admin puede actualizar cosas básicas.
        $data = $request->only(['nombre', 'apellido_paterno', 'telefono']);
        
        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->asForm()
                        ->patch("{$apiUrl}/usuarios/{$id}", $data);

        return redirect('/admin/users')->with('success', 'Usuario actualizado correctamente.');
    });

    // CRUD Usuarios - Eliminar
    Route::post('/admin/users/{id}/delete', function ($id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->delete("{$apiUrl}/usuarios/{$id}");

        return redirect('/admin/users')->with('success', 'Usuario eliminado correctamente.');
    });

    Route::get('/admin/articles', function () {
        $apiUrl   = env('API_URL', 'http://127.0.0.1:8001/api');
        $articulos = [];
        $medicos = [];
        try {
            $token    = session('admin_token');
            $headers  = ['Authorization' => "Bearer {$token}"];
            $response = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/articulos");
            if ($response->successful()) {
                $articulos = $response->json();
            }
            $meds = Http::withHeaders($headers)->timeout(3)->get("{$apiUrl}/medicos");
            if ($meds->successful()) {
                $medicos = $meds->json();
            }
        } catch (\Exception $e) {}
        return view('admin.articles', compact('articulos', 'medicos'));
    });

    Route::post('/admin/articles', function (Illuminate\Http\Request $request) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        $data = $request->except('_token');
        // Asegurar tipos
        $data['id_medico'] = (int) $data['id_medico'];
        $data['estatus'] = $data['estatus'] == '1' ? 1 : 0;
        
        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->post("{$apiUrl}/articulos", $data);

        return redirect('/admin/articles')->with('success', 'Artículo creado correctamente.');
    });

    Route::post('/admin/articles/{id}/delete', function ($id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->delete("{$apiUrl}/articulos/{$id}");
        return redirect('/admin/articles')->with('success', 'Artículo eliminado correctamente.');
    });

    Route::get('/admin/chats', function () {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        $chats = [];
        try {
            $headers = ['Authorization' => "Bearer {$token}"];
            $res = Http::withHeaders($headers)->timeout(5)->get("{$apiUrl}/chats");
            if ($res->successful()) {
                $chats = $res->json();
            }
        } catch (\Exception $e) {}
        return view('admin.chats', compact('chats'));
    });

    Route::post('/admin/chats/{id}/toggle-estatus', function ($id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        $estatus = request('estatus') === 'restringido' ? 'restringido' : 'activo';
        
        Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->patch("{$apiUrl}/chats/{$id}/estatus?estatus={$estatus}");

        return redirect('/admin/chats')->with('success', 'Estatus del chat actualizado correctamente.');
    });

    // ── CITAS ADMIN ─────────────────────────────────────────────────────────────
    Route::get('/admin/citas', function () {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        $citas = [];
        $usuarios = [];
        $medicos = [];
        
        try {
            $headers = ['Authorization' => "Bearer {$token}"];
            
            // Traer citas
            $res = Http::withHeaders($headers)->timeout(5)->get("{$apiUrl}/admin/citas");
            if ($res->successful()) {
                $citas = $res->json();
            }
            
            // Traer usuarios
            $res_users = Http::withHeaders($headers)->timeout(5)->get("{$apiUrl}/usuarios");
            if ($res_users->successful()) {
                $usuarios = $res_users->json();
            }
            
            // Traer médicos
            $res_meds = Http::withHeaders($headers)->timeout(5)->get("{$apiUrl}/medicos");
            if ($res_meds->successful()) {
                $medicos = $res_meds->json();
            }
        } catch (\Exception $e) {}
        
        return view('admin.citas', compact('citas', 'usuarios', 'medicos'));
    });

    // Crear cita
    Route::post('/admin/citas', function (Illuminate\Http\Request $request) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->post("{$apiUrl}/admin/citas", [
                            'id_usuario' => (int) $request->input('id_usuario'),
                            'id_medico' => (int) $request->input('id_medico'),
                            'fecha' => $request->input('fecha'),
                            'hora' => $request->input('hora'),
                            'motivo_cancelacion' => $request->input('motivo_cancelacion'),
                        ]);

        if ($response->successful()) {
            return redirect('/admin/citas')->with('success', '✅ Cita creada correctamente.');
        }
        return redirect('/admin/citas')->with('error', 'Error al crear cita: ' . $response->json('detail', 'Error desconocido'));
    });

    // Editar cita
    Route::patch('/admin/citas/{id}', function (Illuminate\Http\Request $request, $id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->patch("{$apiUrl}/admin/citas/{$id}/edit", [
                            'fecha' => $request->input('fecha'),
                            'hora' => $request->input('hora'),
                            'estado' => $request->input('estado'),
                        ]);

        if ($response->successful()) {
            return redirect('/admin/citas')->with('success', '✅ Cita actualizada correctamente.');
        }
        return redirect('/admin/citas')->with('error', 'Error al actualizar cita.');
    });

    // Cancelar cita
    Route::patch('/admin/citas/{id}/cancelar', function ($id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->patch("{$apiUrl}/admin/citas/{$id}/cancelar");

        return redirect('/admin/citas')->with('success', '✅ Cita cancelada correctamente.');
    });

    // Eliminar cita (soft delete)
    Route::delete('/admin/citas/{id}', function ($id) {
        $apiUrl = env('API_URL', 'http://127.0.0.1:8001/api');
        $token  = session('admin_token');
        
        Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->delete("{$apiUrl}/admin/citas/{$id}");

        return redirect('/admin/citas')->with('success', '✅ Cita eliminada correctamente.');
    });
});

