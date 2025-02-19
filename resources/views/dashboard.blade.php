<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Empleados por Estado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .actions-column {
            white-space: nowrap;
            min-width: 160px;
        }
        .actions-column .btn {
            margin-right: 4px;
        }
        .actions-column .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Botones del lado izquierdo -->
                <div>
                    <a href="{{ route('tickets.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-ticket-detailed"></i> Dashboard Tickets
                    </a>
                </div>

                <!-- Botones del lado derecho -->
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.index') }}" class="btn btn-primary me-2">
                        <i class="bi bi-people"></i> Gestión de Usuarios
                    </a>
                    @include('components.notifications')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <h1 class="text-center fw-bold mb-5">Sistema de Empleados por Estado</h1>

    <div class="container">
        <div class="row justify-content-md-center">
            <!-- Formulario a la izquierda -->
            <div class="col-md-4" style="border-right: 1px solid #dee2e6;">
                @include('empleados.formulario')
            </div>

            <!-- Lista de empleados a la derecha -->
            <div class="col-md-8">
                <div class="mb-4">
                    <label for="estado" class="form-label">Seleccione un estado:</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Seleccione un estado...</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}">{{ $estado->estado }}</option>
                        @endforeach
                    </select>
                </div>

                <h2 class="text-center">Lista de Empleados
                    <span class="float-end">
                        <button class="btn btn-primary me-2" onclick="mostrarEstadisticas()" title="Ver Estadísticas">
                            <i class="bi bi-bar-chart-fill"></i>
                        </button>
                        <button class="btn btn-success me-2" onclick="exportarEmpleados()" title="Exportar a CSV">
                            <i class="bi bi-filetype-csv"></i>
                        </button>
                    </span>
                </h2>
                <hr>
                <div id="empleadosContainer">
                    @include('empleados.lista')
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    @include('empleados.modal-estadisticas')
    @include('empleados.modal-calificaciones')
    @include('empleados.modal-detalles-empleado')

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.js"></script>
    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
