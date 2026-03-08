<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Seguridad - Salud Materna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/security.css') }}">
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
                        <a href="{{ url('admin/security') }}" class="nav-link active d-flex align-items-center gap-3">
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
            <h2 class="text-pink fw-bold mb-1">Centro de Seguridad</h2>
            <p class="text-muted mb-4">Gestiona reportes de seguridad y suspensiones de usuarios</p>

            <!-- Contador de reportes pendientes -->
            <div class="bg-white p-4 rounded-4 border mb-4">
                <h6 class="fw-bold mb-1">Reportes Pendientes</h6>
                <h2 class="text-pink fw-bold mb-0">2</h2>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-12">
                    <!-- Reporte de Chat -->
                    <div class="bg-white p-4 rounded-4 border">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h5 class="text-pink fw-bold">Reporte de Chat</h5>
                            <span
                                class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 border border-danger">Alta
                                Prioridad</span>
                        </div>
                        <div class="row g-3 mb-4 text-sm">
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Reportado por:</p>
                                <p class="fw-bold mb-0">María González</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Usuario reportado:</p>
                                <p class="fw-bold mb-0">Dr. Juan Pérez</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Motivo:</p>
                                <p class="fw-bold mb-0">Contenido inapropiado en el chat</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Fecha:</p>
                                <p class="fw-bold mb-0">22/02/2026</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-pink text-white px-4">Restringir Chat</button>
                            <button class="btn btn-outline-secondary px-4">Suspender Cuenta</button>
                            <button class="btn btn-outline-secondary px-4">Desestimar</button>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <!-- Reporte de Conducta -->
                    <div class="bg-white p-4 rounded-4 border">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h5 class="text-pink fw-bold">Reporte de Conducta</h5>
                            <span
                                class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 border border-warning">Prioridad
                                Media</span>
                        </div>
                        <div class="row g-3 mb-4 text-sm">
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Reportado por:</p>
                                <p class="fw-bold mb-0">Carmen López</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Usuario reportado:</p>
                                <p class="fw-bold mb-0">Dr. Miguel Soto</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Motivo:</p>
                                <p class="fw-bold mb-0">Comportamiento no profesional</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Fecha:</p>
                                <p class="fw-bold mb-0">23/02/2026</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-pink text-white px-4">Restringir Chat</button>
                            <button class="btn btn-outline-secondary px-4">Suspender Cuenta</button>
                            <button class="btn btn-outline-secondary px-4">Desestimar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuentas Suspendidas -->
            <div class="bg-white p-4 rounded-4 border">
                <h5 class="text-pink fw-bold mb-4">Cuentas Suspendidas</h5>
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-2">
                    <div>
                        <h6 class="fw-bold mb-1">Dr. Alberto Ramos</h6>
                        <small class="text-muted">Fecha de suspensión: 15/02/2026</small>
                    </div>
                    <button class="btn btn-link text-muted text-decoration-none small">Cerrar Sesión</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/security.js') }}"></script>
</body>

</html>