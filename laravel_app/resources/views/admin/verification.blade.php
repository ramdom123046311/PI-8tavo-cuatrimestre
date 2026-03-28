<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Médicos - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/verification.css') }}">
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
                        <a href="{{ url('admin/verification') }}"
                            class="nav-link active d-flex align-items-center gap-3">
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
            <h2 class="text-pink fw-bold mb-1">Verificación de Médicos</h2>
            <p class="text-muted mb-4">Revisa y aprueba las solicitudes de registro de nuevos profesionales</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @forelse($medicos_pendientes as $medico)
            <!-- Doctor Profile -->
            <div class="doctor-card bg-white rounded-4 border p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-4">
                        <div
                            class="avatar-bg rounded-circle d-flex align-items-center justify-content-center fs-2 bg-light-pink p-3">
                            👨‍⚕️
                        </div>
                        <div>
                            <h5 class="text-pink fw-bold mb-1">{{ $medico['nombre_completo'] }}</h5>
                            <p class="mb-2 text-dark">{{ $medico['especialidad'] ?: 'Especialidad no especificada' }}</p>
                            
                            <div class="d-flex gap-3 mb-4 flex-wrap mt-3">
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Experiencia</small>
                                    <strong>{{ $medico['anios_experiencia'] }} años</strong>
                                </div>
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Estado Actual</small>
                                    <strong>{{ $medico['estatus'] == 0 ? 'Inactivo' : $medico['estatus'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 action-buttons">
                        <button type="button" class="w-100 btn btn-primary d-flex align-items-center justify-content-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#modalDetails{{ $medico['id_medico'] }}">
                            <i data-lucide="eye" class="icon-sm"></i> Ver Detalles
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Details -->
            <div class="modal fade" id="modalDetails{{ $medico['id_medico'] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4">
                        <div class="modal-header bg-light border-0 rounded-top-4">
                            <h5 class="modal-title text-pink fw-bold">Detalles de Solicitud</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <div class="avatar-bg mx-auto rounded-circle d-flex align-items-center justify-content-center fs-1 bg-light-pink mb-3" style="width: 80px; height: 80px;">
                                    👨‍⚕️
                                </div>
                                <h4 class="fw-bold text-dark">{{ $medico['nombre_completo'] }}</h4>
                                <span class="badge bg-purple">{{ $medico['especialidad'] ?: 'Médico General' }}</span>
                            </div>
                            <div class="border rounded-3 p-3 bg-light mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Cédula Profesional</small>
                                        <strong class="text-dark">{{ $medico['cp'] ?? 'No informada' }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Experiencia</small>
                                        <strong class="text-dark">{{ $medico['anios_experiencia'] }} años</strong>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div>
                                    <small class="text-muted d-block mb-1">Descripción / Bio</small>
                                    <p class="mb-0 text-dark small">{{ $medico['descripcion_profesional'] ?: 'Sin descripción.' }}</p>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 w-100">
                                <form action="{{ url('admin/verification/approve/'.$medico['id_medico']) }}" method="POST" class="flex-grow-1 m-0 p-0">
                                    @csrf
                                    <button type="submit" class="w-100 btn btn-success py-2 d-flex align-items-center justify-content-center gap-2">
                                        <i data-lucide="check-circle" class="icon-sm"></i> Aprobar Médico
                                    </button>
                                </form>
                                <form action="{{ url('admin/verification/reject/'.$medico['id_medico']) }}" method="POST" class="flex-grow-1 m-0 p-0">
                                    @csrf
                                    <button type="submit" class="w-100 btn btn-outline-danger py-2 d-flex align-items-center justify-content-center gap-2">
                                        <i data-lucide="x-circle" class="icon-sm"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-5 bg-white rounded-4 border">
                <p class="text-muted mb-0">No hay médicos pendientes de verificación.</p>
            </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/verification.js') }}"></script>
</body>

</html>