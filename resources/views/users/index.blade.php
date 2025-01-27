<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .role-badge {
            font-size: 0.9em;
            padding: 0.4em 0.8em;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.03);
            transition: background-color 0.2s ease-in-out;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .role-select {
            min-width: 140px;
        }
        .table > :not(caption) > * > * {
        padding: 1rem 1.5rem;
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .form-select {
            border-radius: 6px;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            font-size: 0.9rem;
            border-color: #dee2e6;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-select:hover {
            border-color: #adb5bd;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }

        .rounded-circle {
            transition: transform 0.2s;
        }

        tr:hover .rounded-circle {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header con botones de navegación -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-people-fill text-primary"></i>
                    Gestión de Usuarios y Roles
                </h2>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        <!-- Tarjeta principal -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Usuarios Registrados</h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary rounded-pill">
                            Total: {{ count($users) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Rol Actual</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="align-middle">{{ $user->id }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 32px; height: 32px; background-color: {{
                                                $user->rol === 'admin' ? '#dc3545' :
                                                ($user->rol === 'empleado' ? '#198754' : '#0dcaf0')
                                            }}">
                                            <span class="text-white fw-medium" style="font-size: 0.9rem;">
                                                {{ strtoupper(substr($user->username, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="fw-medium">{{ $user->username }}</span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge rounded-pill {{
                                        $user->rol === 'admin' ? 'bg-danger' :
                                        ($user->rol === 'empleado' ? 'bg-success' : 'bg-info')
                                    }} px-3">
                                        {{ ucfirst($user->rol ?? 'Sin rol') }}
                                    </span>
                                </td>
                                <td class="align-middle text-end">
                                    <select class="form-select form-select-sm d-inline-block w-auto"
                                            style="max-width: 200px;"
                                            onchange="actualizarRol({{ $user->id }}, this.value)">
                                        <option value="">Cambiar rol...</option>
                                        <option value="admin" {{ $user->rol === 'admin' ? 'selected' : '' }}>
                                            Administrador
                                        </option>
                                        <option value="empleado" {{ $user->rol === 'empleado' ? 'selected' : '' }}>
                                            Empleado
                                        </option>
                                        <option value="cliente" {{ $user->rol === 'cliente' ? 'selected' : '' }}>
                                            Cliente
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function actualizarRol(userId, rol) {
            if (!rol) return;

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas cambiar el rol de este usuario a ${rol}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cambiar rol',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/SS-Laravel/public/users/${userId}/role`,
                        method: 'POST',
                        data: {
                            rol: rol,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire(
                                '¡Actualizado!',
                                'El rol ha sido actualizado exitosamente.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error',
                                'No se pudo actualizar el rol',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
