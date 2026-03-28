<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
</head>

<body>

    <div class="d-flex wrapper">
        <!-- Sidebar -->
        <div class="sidebar bg-white border-end">
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div
                        class="logo-icon bg-pink text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i data-lucide="heart" class="icon-sm"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-pink fw-bold">Salud Materna</h5>
                        <small class="text-muted">Administrador</small>
                    </div>
                </div>
            </div>
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div
                        class="admin-avatar bg-light-pink rounded-circle d-flex align-items-center justify-content-center fs-4">
                        👤
                    </div>
                    <div>
                        <h6 class="mb-0 text-pink fw-bold">Admin Sistema</h6>
                        <small class="text-muted">admin@demo.com</small>
                    </div>
                </div>
            </div>
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
                        <a href="{{ url('admin/users') }}" class="nav-link active d-flex align-items-center gap-3">
                            <i data-lucide="users" class="icon-md"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/citas') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="calendar" class="icon-md"></i>
                            <span>Gestión de Citas</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/articles') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="file-text" class="icon-md"></i>
                            <span>Artículos</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="p-4 border-top mt-auto mb-5 sidebar-bottom">
                <button class="btn btn-outline-pink w-100 d-flex align-items-center justify-content-center gap-2">
                    <i data-lucide="log-out" class="icon-sm"></i>
                    Cerrar Sesión
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-pink fw-bold mb-1">Gestión de Usuarios</h2>
                    <p class="text-muted mb-0">Administra los usuarios y médicos registrados en el sistema</p>
                </div>
                <button class="btn btn-pink d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#userModal">
                    <i data-lucide="plus" class="icon-sm"></i> Nuevo Usuario
                </button>
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
                            <th class="py-3 px-4 font-weight-semibold">Usuario</th>
                            <th class="py-3 px-4 font-weight-semibold">Rol</th>
                            <th class="py-3 px-4 font-weight-semibold">Estado</th>
                            <th class="py-3 px-4 font-weight-semibold">Fecha de Registro</th>
                            <th class="py-3 px-4 font-weight-semibold text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-light-pink text-pink rounded-circle d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0"
                                        style="width: 40px; height: 40px;">
                                        @if($user['foto_perfil'])
                                            <img src="{{ $user['foto_perfil'] }}" alt="{{ $user['nombre'] }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i data-lucide="user" class="icon-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="fw-bold mb-0">{{ $user['nombre'] }} {{ $user['apellido_paterno'] }}</p>
                                        <small class="text-muted">{{ $user['correo'] }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-light text-{{ $user['id_rol'] == 2 ? 'primary' : 'dark' }} border">
                                    {{ $user['id_rol'] == 1 ? 'Paciente' : ($user['id_rol'] == 2 ? 'Médico' : 'Admin') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-{{ $user['estatus'] == 1 ? 'success' : 'danger' }}-subtle text-{{ $user['estatus'] == 1 ? 'success' : 'danger' }} border border-{{ $user['estatus'] == 1 ? 'success' : 'danger' }}">
                                    {{ $user['estatus'] == 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-muted small">{{ substr($user['fecha_registro'] ?? '', 0, 10) }}</td>
                            <td class="py-3 px-4 text-end">
                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#userModalEdit{{ $user['id_usuario'] }}" title="Editar">
                                    <i data-lucide="edit-2" class="icon-xs"></i>
                                </button>
                                <form action="{{ url('admin/users/'.$user['id_usuario'].'/delete') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                        <i data-lucide="trash-2" class="icon-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="userModalEdit{{ $user['id_usuario'] }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 text-start">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title text-pink fw-bold">Editar Usuario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('admin/users/'.$user['id_usuario'].'/update') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Nombre</label>
                                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $user['nombre'] }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Apellido Paterno</label>
                                                <input type="text" name="apellido_paterno" class="form-control rounded-3" value="{{ $user['apellido_paterno'] }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Teléfono</label>
                                                <input type="text" name="telefono" class="form-control rounded-3" value="{{ $user['telefono'] ?? '' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Estatus</label>
                                                <select name="estatus" class="form-control rounded-3">
                                                    <option value="1" {{ $user['estatus'] == 1 ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ $user['estatus'] == 0 ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                            </div>
                                            <!-- Nota: Correo no es editable según endpoints de FastAPI actuales via PATCH -->
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-pink rounded-3 px-4">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title text-pink fw-bold" id="userModalLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('admin/users') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control rounded-3" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Rol</label>
                                <select name="id_rol" class="form-select rounded-3">
                                    <option value="1">Paciente</option>
                                    <option value="2">Médico</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Género</label>
                                <select name="genero" class="form-select rounded-3">
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pink rounded-3 px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/users.js') }}"></script>
</body>

</html>