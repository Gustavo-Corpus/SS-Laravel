<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Lista de Tickets</h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
                <a href="{{ route('tickets.dashboard') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-graph-up"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="ticketsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Título</th>
                            <th>Usuario</th>
                            <th>Asignado a</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->numero_ticket }}</td>
                            <td>{{ $ticket->titulo }}</td>
                            <td>{{ $ticket->usuario->nombre ?? 'N/A' }} {{ $ticket->usuario->apellido ?? '' }}</td>
                            <td>
                                @if($ticket->empleadoAsignado)
                                    {{ $ticket->empleadoAsignado->nombre }} {{ $ticket->empleadoAsignado->apellido }}
                                @else
                                    <span class="badge bg-danger">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->prioridad === 'alta' ? 'danger' : ($ticket->prioridad === 'media' ? 'warning' : 'info') }}">
                                    {{ ucfirst($ticket->prioridad) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{
                                    $ticket->estado === 'abierto' ? 'danger' :
                                    ($ticket->estado === 'en_proceso' ? 'warning' :
                                    ($ticket->estado === 'resuelto' ? 'success' : 'secondary'))
                                }}">
                                    {{ ucfirst($ticket->estado) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="verTicket({{ $ticket->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="modalDetallesTicket" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        // Destruir la tabla si ya está inicializada
        if ($.fn.DataTable.isDataTable('#ticketsTable')) {
            $('#ticketsTable').DataTable().destroy();
        }

        // Inicializar DataTable con configuración en español
        $('#ticketsTable').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar columna ascendente",
                    "sortDescending": ": activar para ordenar columna descendente"
                }
            },
            order: [[6, 'desc']], // Ordenar por fecha descendente
            responsive: true
        });
    });

    // Función para ver detalles del ticket
    function verTicket(id) {
        $.ajax({
            url: `/SS-Laravel/public/tickets/${id}`,
            method: 'GET',
            success: function(response) {
                $("#modalDetallesTicket .modal-content").html(response);
                $("#modalDetallesTicket").modal('show');
            },
            error: function(xhr) {
                console.error('Error al cargar ticket:', xhr);
                alert('Error al cargar los detalles del ticket');
            }
        });
    }
    </script>
</body>
</html>
