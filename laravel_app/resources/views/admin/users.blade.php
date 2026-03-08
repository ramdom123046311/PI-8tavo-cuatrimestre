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
                        <!-- Row 1 -->
                        <tr>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-light-pink text-pink rounded-circle p-2 d-flex align-items-center justify-content-center">
                                        <i data-lucide="user" class="icon-sm"></i>
                                    </div>
                                    <div>
                                        <p class="fw-bold mb-0">Ana Martínez</p>
                                        <small class="text-muted">ana.martinez@email.com</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4"><span class="badge bg-light text-dark border">Paciente</span></td>
                            <td class="py-3 px-4"><span
                                    class="badge bg-success-subtle text-success border border-success">Activo</span>
                            </td>
                            <td class="py-3 px-4 text-muted small">12 Ene 2026</td>
                            <td class="py-3 px-4 text-end">
                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#userModal" title="Editar">
                                    <i data-lucide="edit-2" class="icon-xs"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i data-lucide="trash-2" class="icon-xs"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-light-primary text-primary rounded-circle p-2 d-flex align-items-center justify-content-center">
                                        👨‍⚕️
                                    </div>
                                    <div>
                                        <p class="fw-bold mb-0">Dr. Roberto Sánchez</p>
                                        <small class="text-muted">roberto.sanchez@email.com</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4"><span
                                    class="badge bg-light text-primary border border-primary">Médico</span></td>
                            <td class="py-3 px-4"><span
                                    class="badge bg-success-subtle text-success border border-success">Activo</span>
                            </td>
                            <td class="py-3 px-4 text-muted small">15 Ene 2026</td>
                            <td class="py-3 px-4 text-end">
                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#userModal" title="Editar">
                                    <i data-lucide="edit-2" class="icon-xs"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i data-lucide="trash-2" class="icon-xs"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Added/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title text-pink fw-bold" id="userModalLabel">Gestión de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre Completo</label>
                            <input type="text" class="form-control rounded-3" placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control rounded-3" placeholder="correo@ejemplo.com"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Rol</label>
                                <select class="form-select rounded-3">
                                    <option value="paciente">Paciente</option>
                                    <option value="medico">Médico</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Estado</label>
                                <select class="form-select rounded-3">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 border rounded-3 p-3 bg-light d-none" id="medicalFields">
                            <h6 class="fw-bold mb-3 text-dark">Información Médica</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Especialidad</label>
                                <input type="text" class="form-control rounded-3" placeholder="Ej. Ginecología">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Cédula Profesional</label>
                                <input type="text" class="form-control rounded-3" placeholder="Ej. COL-123456">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-pink rounded-3 px-4">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/users.js') }}"></script>
</body>

</html>