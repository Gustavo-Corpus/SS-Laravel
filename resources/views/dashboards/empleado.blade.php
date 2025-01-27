<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 1rem;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            transition: background-color 0.2s;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }

        .badge {
            padding: 0.5em 0.8em;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Barra superior -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Panel de Empleado</a>
                <div class="d-flex">
                    <!-- Botón de notificaciones -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill"></i>
                            @php
                                $notificationCount = auth()->user()->unreadNotifications->count();
                                \Log::info('Conteo de notificaciones:', ['count' => $notificationCount]);
                            @endphp
                            @if($notificationCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $notificationCount }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);"
                                    onclick="verTicketDesdeNotificacion({{ $notification->data['ticket_id'] }}, '{{ $notification->id }}')">
                                        <div class="d-flex flex-column">
                                            <span>{{ $notification->data['mensaje'] }}</span>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item">No hay notificaciones nuevas</span></li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Botón de cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Contenido principal -->
        <div class="container mt-5 text-center">
            <img src="{{ asset('images/employee.svg') }}" alt="Empleado" class="img-fluid mb-4" style="max-width: 300px;">
            <h2 class="mb-4">Bienvenido, {{ auth()->user()->username }}</h2>
            <p class="lead">Panel de gestión de tickets asignados</p>

            <!-- Estadísticas del empleado -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tickets Resueltos</h5>
                            <p class="card-text display-4">{{ auth()->user()->tickets_resolved }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Calificación Promedio</h5>
                            <p class="card-text display-4">{{ number_format(auth()->user()->average_rating, 1) }}</p>
                        </div>
                    </div>
                </div>
            </div>

         <!-- Secciones de Tickets -->
        <div class="row mt-4">
            <!-- Tickets Asignados -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Tickets Asignados</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse(auth()->user()->ticketsAsignados->where('estado', 'abierto') as $ticket)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">#{{ $ticket->numero_ticket }} - {{ $ticket->titulo }}</h6>
                                        <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($ticket->descripcion, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-info">Nuevo</span>
                                        <button class="btn btn-sm btn-primary" onclick="verTicket({{ $ticket->id }})">
                                            Atender
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-0">No hay tickets nuevos asignados</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets En Proceso -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Tickets En Proceso</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse(auth()->user()->ticketsAsignados->where('estado', 'en_proceso') as $ticket)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">#{{ $ticket->numero_ticket }} - {{ $ticket->titulo }}</h6>
                                        <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($ticket->descripcion, 100) }}</p>
                                    <button class="btn btn-sm btn-info mt-2" onclick="verTicket({{ $ticket->id }})">
                                        Ver Detalles
                                    </button>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-0">No hay tickets en proceso</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Resueltos -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tickets Resueltos</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse(auth()->user()->ticketsAsignados->whereIn('estado', ['resuelto', 'cerrado']) as $ticket)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">#{{ $ticket->numero_ticket }} - {{ $ticket->titulo }}</h6>
                                        <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($ticket->descripcion, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge bg-{{ $ticket->estado === 'resuelto' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($ticket->estado) }}
                                        </span>
                                        <button class="btn btn-sm btn-info" onclick="verTicket({{ $ticket->id }})">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-0">No hay tickets resueltos</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalDetallesTicket" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.js"></script>
    <script src="{{ asset('js/tickets.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inicializar los dropdowns de Bootstrap
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });
        });
    </script>
    <script>
    function verTicket(id) {
        $.ajax({
            url: `/SS-Laravel/public/tickets/${id}`,
            method: 'GET',
            success: function(response) {
                $("#modalDetallesTicket .modal-content").html(response);
                var modal = new bootstrap.Modal(document.getElementById('modalDetallesTicket'), {
                    backdrop: 'static',  // Esto evita que el modal se cierre al hacer clic fuera
                    keyboard: false      // Esto evita que se cierre con la tecla ESC
                });
                modal.show();
            },
            error: function(xhr) {
                alert('Error al cargar los detalles del ticket');
            }
        });
    }

    function marcarEnProceso(ticketId) {
        const comentario = $('#comentario_ticket').val();

        $.ajax({
            url: `/SS-Laravel/public/tickets/${ticketId}`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                estado: 'en_proceso',
                comentario: comentario
            },
            success: function(response) {
                $('#modalDetallesTicket').modal('hide');
                alert('Ticket marcado en proceso');
                location.reload();
            },
            error: function(xhr) {
                alert('Error al actualizar el estado del ticket');
            }
        });
    }

    function marcarResuelto(ticketId) {
        const comentario = $('#comentario_ticket').val();

        if (!comentario) {
            alert('Por favor, agrega un comentario con la solución antes de marcar como resuelto');
            return;
        }

        $.ajax({
            url: `/SS-Laravel/public/tickets/${ticketId}`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                estado: 'resuelto',
                comentario: comentario
            },
            success: function(response) {
                $('#modalDetallesTicket').modal('hide');
                alert('Ticket marcado como resuelto');
                location.reload();
            },
            error: function(xhr) {
                alert('Error al actualizar el estado del ticket');
            }
        });
    }
    </script>

</body>
</html>
