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

            <!-- Doctor Profile 1 -->
            <div class="doctor-card bg-white rounded-4 border p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-4">
                        <div
                            class="avatar-bg rounded-circle d-flex align-items-center justify-content-center fs-2 bg-light-pink p-3">
                            👨‍⚕️
                        </div>
                        <div>
                            <h5 class="text-pink fw-bold mb-1">Dr. Roberto Sánchez</h5>
                            <p class="mb-2 text-dark">Ginecología y Obstetricia</p>
                            <div class="d-flex gap-4 text-muted small mb-4">
                                <span class="d-flex align-items-center gap-1"><i data-lucide="mail" class="icon-xs"></i>
                                    roberto.sanchez@email.com</span>
                                <span class="d-flex align-items-center gap-1"><i data-lucide="phone"
                                        class="icon-xs"></i> +34 678 123 456</span>
                            </div>

                            <div class="d-flex gap-3 mb-4 flex-wrap">
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Cédula Profesional</small>
                                    <strong>COL-28456</strong>
                                </div>
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Experiencia</small>
                                    <strong>10 años</strong>
                                </div>
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Fecha de Solicitud</small>
                                    <strong>20/02/2026</strong>
                                </div>
                            </div>

                            <div class="documents-section">
                                <p class="small text-muted mb-2">Documentos Adjuntos</p>
                                <div class="d-flex gap-2">
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Título Médico
                                    </span>
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Certificado de
                                        Especialidad
                                    </span>
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Cédula Profesional
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 action-buttons">
                        <button class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="check-circle" class="icon-sm"></i> Aprobar
                        </button>
                        <button
                            class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="x-circle" class="icon-sm"></i> Rechazar
                        </button>
                        <button
                            class="btn btn-outline-pink d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="file" class="icon-sm"></i> Ver Documentos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Doctor Profile 2 -->
            <div class="doctor-card bg-white rounded-4 border p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-4">
                        <div
                            class="avatar-bg rounded-circle d-flex align-items-center justify-content-center fs-2 bg-light-pink p-3">
                            👩‍⚕️
                        </div>
                        <div>
                            <h5 class="text-pink fw-bold mb-1">Dra. Patricia López</h5>
                            <p class="mb-2 text-dark">Pediatría Neonatal</p>
                            <div class="d-flex gap-4 text-muted small mb-4">
                                <span class="d-flex align-items-center gap-1"><i data-lucide="mail" class="icon-xs"></i>
                                    patricia.lopez@email.com</span>
                                <span class="d-flex align-items-center gap-1"><i data-lucide="phone"
                                        class="icon-xs"></i> +34 612 987 654</span>
                            </div>

                            <div class="d-flex gap-3 mb-4 flex-wrap">
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Cédula Profesional</small>
                                    <strong>COL-31289</strong>
                                </div>
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Experiencia</small>
                                    <strong>8 años</strong>
                                </div>
                                <div class="info-pill bg-light rounded-3 p-3 flex-grow-1 border-0">
                                    <small class="text-muted d-block">Fecha de Solicitud</small>
                                    <strong>21/02/2026</strong>
                                </div>
                            </div>

                            <div class="documents-section">
                                <p class="small text-muted mb-2">Documentos Adjuntos</p>
                                <div class="d-flex gap-2">
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Título Médico
                                    </span>
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Certificado de
                                        Especialidad
                                    </span>
                                    <span
                                        class="badge document-badge bg-light text-pink border border-pink p-2 d-flex align-items-center gap-1 fw-normal">
                                        <i data-lucide="file-text" class="icon-xs text-pink"></i> Cédula Profesional
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 action-buttons">
                        <button class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="check-circle" class="icon-sm"></i> Aprobar
                        </button>
                        <button
                            class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="x-circle" class="icon-sm"></i> Rechazar
                        </button>
                        <button
                            class="btn btn-outline-pink d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="file" class="icon-sm"></i> Ver Documentos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/verification.js') }}"></script>
</body>

</html>