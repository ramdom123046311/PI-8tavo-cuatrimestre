<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                        <a href="{{ url('admin/dashboard') }}" class="nav-link active d-flex align-items-center gap-3">
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
                        <a href="{{ url('admin/chats') }}" class="nav-link d-flex align-items-center gap-3">
                            <i data-lucide="message-square" class="icon-md"></i>
                            <span>Gestión de Chats</span>
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
            <h2 class="text-pink fw-bold mb-1">Panel de Administración</h2>
            <p class="text-muted mb-4">Gestión centralizada de la plataforma Salud Materna Juvenil</p>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card p-4 bg-white rounded-4 border">
                        <div class="stat-icon-wrapper text-primary bg-light-primary rounded-circle mb-4">
                            <i data-lucide="users" class="icon-lg"></i>
                        </div>
                        <h2 class="text-primary fw-bold mb-1">{{ $stats['usuarios'] }}</h2>
                        <p class="text-muted mb-0 small">Total Usuarios</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-4 bg-white rounded-4 border">
                        <div class="stat-icon-wrapper text-success bg-light-success rounded-circle mb-4">
                            <i data-lucide="user-check" class="icon-lg"></i>
                        </div>
                        <h2 class="text-success fw-bold mb-1">{{ $stats['medicos_activos'] }}</h2>
                        <p class="text-muted mb-0 small">Médicos Activos</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-4 bg-white rounded-4 border">
                        <div class="stat-icon-wrapper text-warning bg-light-warning rounded-circle mb-4">
                            <i data-lucide="calendar" class="icon-lg"></i>
                        </div>
                        <h2 class="text-warning fw-bold mb-1">{{ $stats['citas_pendientes'] }}</h2>
                        <p class="text-muted mb-0 small">Citas Pendientes</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-4 bg-white rounded-4 border">
                        <div class="stat-icon-wrapper text-purple bg-light-purple rounded-circle mb-4">
                            <i data-lucide="file-text" class="icon-lg"></i>
                        </div>
                        <h2 class="text-purple fw-bold mb-1">{{ $stats['articulos_publicados'] }}</h2>
                        <p class="text-muted mb-0 small">Artículos Publicados</p>
                    </div>
                </div>
            </div>

            <h4 class="text-pink fw-bold mb-4">Acciones Rápidas</h4>
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="{{ url('admin/verification') }}" class="text-decoration-none text-dark">
                        <div class="action-card d-flex align-items-center p-4 bg-white rounded-4 border">
                            <div class="action-icon text-pink bg-light-pink rounded-circle me-3">
                                <i data-lucide="user-plus" class="icon-md"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-pink fw-bold d-flex align-items-center gap-2">
                                    Verificar Médicos
                                </h6>
                                <small class="text-muted">Revisar solicitudes de registro</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('admin/security') }}" class="text-decoration-none text-dark">
                        <div class="action-card d-flex align-items-center p-4 bg-white rounded-4 border">
                            <div class="action-icon text-danger bg-light-danger rounded-circle me-3">
                                <i data-lucide="shield" class="icon-md"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-danger fw-bold d-flex align-items-center gap-2">
                                    Centro de Seguridad
                                </h6>
                                <small class="text-muted">Gestionar restricciones</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('admin/users') }}" class="text-decoration-none text-dark">
                        <div class="action-card d-flex align-items-center p-4 bg-white rounded-4 border">
                            <div class="action-icon text-primary bg-light-primary rounded-circle me-3">
                                <i data-lucide="users" class="icon-md"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-primary fw-bold d-flex align-items-center gap-2">
                                    Gestión de Usuarios
                                </h6>
                                <small class="text-muted">CRUD completo de usuarios</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('admin/articles') }}" class="text-decoration-none text-dark">
                        <div class="action-card d-flex align-items-center p-4 bg-white rounded-4 border">
                            <div class="action-icon text-purple bg-light-purple rounded-circle me-3">
                                <i data-lucide="file-text" class="icon-md"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-purple fw-bold d-flex align-items-center gap-2">
                                    Gestión de Contenido
                                </h6>
                                <small class="text-muted">Moderar artículos médicos</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>