<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}">
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
                        <a href="{{ url('admin/users') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="users" class="icon-md"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ url('admin/citas') }}" class="nav-link active d-flex align-items-center gap-3">
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
                    <h2 class="text-pink fw-bold mb-1">Gestión de Citas</h2>
                    <p class="text-muted mb-0">Administra todas las citas médicas del sistema</p>
                </div>
                <button class="btn btn-pink d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#citaModalNew">
                    <i data-lucide="plus" class="icon-sm"></i> Nueva Cita
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
                            <th class="py-3 px-4 font-weight-semibold">Paciente</th>
                            <th class="py-3 px-4 font-weight-semibold">Médico</th>
                            <th class="py-3 px-4 font-weight-semibold">Fecha</th>
                            <th class="py-3 px-4 font-weight-semibold">Hora</th>
                            <th class="py-3 px-4 font-weight-semibold">Estado</th>
                            <th class="py-3 px-4 font-weight-semibold text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $cita)
                        <tr class="{{ $cita['is_deleted'] ? 'opacity-50' : '' }}">
                            <td class="py-3 px-4">
                                @php
                                    $usuario = collect($usuarios)->firstWhere('id_usuario', $cita['id_usuario']);
                                @endphp
                                <div>
                                    <p class="fw-bold mb-0">{{ $usuario['nombre'] ?? 'Usuario' }} {{ $usuario['apellido_paterno'] ?? '' }}</p>
                                    <small class="text-muted">{{ $usuario['correo'] ?? '' }}</small>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $medico = collect($medicos)->firstWhere('id_medico', $cita['id_medico']);
                                @endphp
                                <div>
                                    <p class="fw-bold mb-0">Dr(a). {{ $medico['usuario']['nombre'] ?? 'Médico' }}</p>
                                    <small class="text-muted">{{ $medico['especialidad'] ?? 'Médico General' }}</small>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-muted small">{{ $cita['fecha'] }}</td>
                            <td class="py-3 px-4 text-muted small">{{ $cita['hora'] }}</td>
                            <td class="py-3 px-4">
                                @if($cita['is_deleted'])
                                    <span class="badge badge-deleted">Eliminada</span>
                                @else
                                    <span class="badge badge-estado-{{ strtolower($cita['estado']) }} text-decoration-none">
                                        {{ ucfirst($cita['estado']) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-end">
                                @if(!$cita['is_deleted'])
                                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                        data-bs-target="#citaModalEdit{{ $cita['id_cita'] }}" title="Editar">
                                        <i data-lucide="edit-2" class="icon-xs"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="modal"
                                        data-bs-target="#citaModalCancel{{ $cita['id_cita'] }}" title="Cancelar"
                                        @if($cita['estado'] == 'cancelada') disabled @endif>
                                        <i data-lucide="x-circle" class="icon-xs"></i>
                                    </button>
                                @endif
                                <form action="{{ url('admin/citas/'.$cita['id_cita']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" 
                                        onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                                        <i data-lucide="trash-2" class="icon-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Cita -->
                        <div class="modal fade" id="citaModalEdit{{ $cita['id_cita'] }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 text-start">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title text-white fw-bold">Editar Cita</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('admin/citas/'.$cita['id_cita']) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Fecha</label>
                                                <input type="date" name="fecha" class="form-control rounded-3" value="{{ $cita['fecha'] }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Hora</label>
                                                <input type="time" name="hora" class="form-control rounded-3" value="{{ substr($cita['hora'], 0, 5) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Estado</label>
                                                <select name="estado" class="form-control rounded-3">
                                                    <option value="pendiente" {{ $cita['estado'] == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="aprobada" {{ $cita['estado'] == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                                    <option value="rechazada" {{ $cita['estado'] == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-pink rounded-3 px-4">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Cancel Cita -->
                        <div class="modal fade" id="citaModalCancel{{ $cita['id_cita'] }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 text-start">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title text-white fw-bold">Cancelar Cita</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted mb-0">¿Estás seguro que deseas cancelar esta cita? Se notificará al paciente y al médico.</p>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">No, mantener</button>
                                        <form action="{{ url('admin/citas/'.$cita['id_cita'].'/cancelar') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning rounded-3 px-4">Sí, cancelar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay citas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Cita -->
    <div class="modal fade" id="citaModalNew" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 text-start">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title text-white fw-bold">Nueva Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('admin/citas') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Paciente</label>
                            <select name="id_usuario" class="form-control rounded-3" required>
                                <option value="">-- Seleccionar Paciente --</option>
                                @foreach($usuarios as $usuario)
                                    @if($usuario['id_rol'] == 1)
                                        <option value="{{ $usuario['id_usuario'] }}">
                                            {{ $usuario['nombre'] }} {{ $usuario['apellido_paterno'] }} ({{ $usuario['correo'] }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Médico</label>
                            <select name="id_medico" class="form-control rounded-3" required>
                                <option value="">-- Seleccionar Médico --</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico['id_medico'] }}">
                                        Dr(a). {{ $medico['usuario']['nombre'] }} - {{ $medico['especialidad'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Fecha</label>
                            <input type="date" name="fecha" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Hora</label>
                            <input type="time" name="hora" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Motivo de Consulta (Opcional)</label>
                            <textarea name="motivo_cancelacion" class="form-control rounded-3" rows="3" placeholder="Describe el motivo de la cita..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pink rounded-3 px-4">Crear Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>

</html>
