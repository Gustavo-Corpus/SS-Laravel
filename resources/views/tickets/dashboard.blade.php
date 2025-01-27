<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard de Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header con botones de navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-ticket-detailed text-primary"></i>
                Dashboard de Tickets
            </h2>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-house-door"></i> Inicio
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-primary me-2">
                <i class="bi bi-list-check"></i> Ver Todos los Tickets
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</div>

    <h1 class="text-center fw-bold mb-5">Dashboard de Tickets</h1>

    <div class="container">
        <!-- Resumen de Tickets -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Tickets</h5>
                        <h2>{{ $totalTickets }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5>En Proceso</h5>
                        <h2>{{ $ticketsEnProceso }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Resueltos</h5>
                        <h2>{{ $ticketsResueltos }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Sin Asignar</h5>
                        <h2>{{ $ticketsSinAsignar }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tickets por Estado</h5>
                        <div style="height: 300px;">
                            <canvas id="ticketsEstadoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tickets por Prioridad</h5>
                        <div style="height: 300px;">
                            <canvas id="ticketsPrioridadChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Top Empleados -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Empleados</h5>
                    @if($topEmpleados->isEmpty())
                        <p class="text-muted text-center">No hay empleados con tickets resueltos aún</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Empleado</th>
                                        <th class="text-center">Tickets Resueltos</th>
                                        <th class="text-center">Calificación Promedio</th>
                                        <th class="text-center">Tickets Pendientes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topEmpleados as $empleado)
                                        <tr>
                                            <td>{{ $empleado['nombre'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $empleado['tickets_resueltos'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star{{ $i <= $empleado['calificacion_promedio'] ? '-fill' : '' }} text-warning"></i>
                                                    @endfor
                                                    <span class="ms-2">({{ $empleado['calificacion_promedio'] }})</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $empleado['tickets_pendientes'] > 0 ? 'warning' : 'secondary' }}">
                                                    {{ $empleado['tickets_pendientes'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/tickets-dashboard.js') }}"></script>
</body>
</html>
