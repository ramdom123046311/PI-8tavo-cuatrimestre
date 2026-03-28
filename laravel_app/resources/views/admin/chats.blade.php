<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
</head>

<body>

    <div class="d-flex wrapper">
        <!-- Sidebar -->
        <div class="sidebar bg-white border-end">
            <!-- Sidebar Header -->
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="logo-icon bg-pink text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i data-lucide="heart" class="icon-sm"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-pink fw-bold">Salud Materna</h5>
                        <small class="text-muted">Administrador</small>
                    </div>
                </div>
            </div>
            <!-- Admin Info -->
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-avatar bg-light-pink rounded-circle d-flex align-items-center justify-content-center fs-4">
                        👤
                    </div>
                    <div>
                        <h6 class="mb-0 text-pink fw-bold">Admin Sistema</h6>
                        <small class="text-muted">admin@demo.com</small>
                    </div>
                </div>
            </div>
            <!-- Sidebar Links -->
            <div class="p-3">
                <ul class="nav flex-column sidebar-nav empty-padding">
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/dashboard') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="layout-dashboard" class="icon-md"></i>
                            <span>Panel Principal</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/verification') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="user-check" class="icon-md"></i>
                            <span>Verificar Médicos</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/security') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="shield" class="icon-md"></i>
                            <span>Centro de Seguridad</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/users') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="users" class="icon-md"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/articles') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="file-text" class="icon-md"></i>
                            <span>Artículos</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/chats') }}" class="nav-link active d-flex align-items-center gap-3">
                            <i data-lucide="message-square" class="icon-md"></i>
                            <span>Gestión de Chats</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Logout Form -->
            <div class="p-4 border-top mt-auto mb-5 sidebar-bottom">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-pink w-100 d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="log-out" class="icon-sm"></i>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-pink fw-bold mb-1">Gestión de Chats</h2>
                    <p class="text-muted mb-0">Revisa y restringe las conversaciones activas</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Table -->
            <div class="bg-white rounded-4 border overflow-hidden">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="py-3 px-4 font-weight-semibold">ID Chat</th>
                            <th class="py-3 px-4 font-weight-semibold">ID Paciente</th>
                            <th class="py-3 px-4 font-weight-semibold">ID Médico</th>
                            <th class="py-3 px-4 font-weight-semibold">Estatus</th>
                            <th class="py-3 px-4 font-weight-semibold">Último Mensaje</th>
                            <th class="py-3 px-4 font-weight-semibold text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chats as $chat)
                        <tr>
                            <td class="py-3 px-4 fw-bold">#{{ $chat['id_chat'] }}</td>
                            <td class="py-3 px-4 text-muted">Usuario {{ $chat['id_usuario'] }}</td>
                            <td class="py-3 px-4 text-muted">Médico {{ $chat['id_medico'] }}</td>
                            <td class="py-3 px-4">
                                @if($chat['estatus'] == 'activo')
                                    <span class="badge bg-success-subtle text-success border border-success">Activo</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Restringido</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-muted small">{{ substr($chat['ultimo_mensaje'] ?? '', 0, 16) }}</td>
                            <td class="py-3 px-4 text-end">
                                <form action="{{ url('admin/chats/'.$chat['id_chat'].'/toggle-estatus') }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($chat['estatus'] == 'activo')
                                        <input type="hidden" name="estatus" value="restringido">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Restringir Chat" onclick="return confirm('¿Seguro que deseas restringir este chat?')">
                                            <i data-lucide="lock" class="icon-xs"></i> Restringir
                                        </button>
                                    @else
                                        <input type="hidden" name="estatus" value="activo">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar Chat" onclick="return confirm('¿Seguro que deseas activar este chat?')">
                                            <i data-lucide="unlock" class="icon-xs"></i> Activar
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay chats registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>lucide.createIcons();</script>
</body>

</html>
