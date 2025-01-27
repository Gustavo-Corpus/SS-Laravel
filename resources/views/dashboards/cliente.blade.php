<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
        gap: 0.5rem;
    }

    .rating input {
        display: none;
    }

    .rating label {
        cursor: pointer;
        color: #ddd;
        font-size: 1.5rem;
    }

    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffc107;
    }
</style>
</head>
<body>
    <div class="container-fluid">
        <!-- Barra superior -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Portal de Cliente</a>
                <div class="d-flex">
                    <!-- Botón de notificaciones -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notificaciones</h6></li>
                            @forelse(auth()->user()->notifications->take(5) as $notification)
                                <li><a class="dropdown-item" href="#">{{ $notification->data['mensaje'] }}</a></li>
                            @empty
                                <li><a class="dropdown-item" href="#">No hay notificaciones</a></li>
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
            <img src="{{ asset('images/support.svg') }}" alt="Soporte" class="img-fluid mb-4" style="max-width: 300px;">
            <h2 class="mb-4">Bienvenido al Portal de Soporte</h2>
            <p class="lead mb-4">¿Necesitas ayuda? Crea un nuevo ticket y te atenderemos lo antes posible</p>

            <!-- Botón para crear ticket -->
            <button class="btn btn-primary btn-lg" onclick="mostrarFormularioTicket()">
                <i class="bi bi-plus-circle"></i> Crear Nuevo Ticket
            </button>

            <!-- Lista de mis tickets -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3>Mis Tickets</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->numero_ticket }}</td>
                                    <td>{{ $ticket->titulo }}</td>
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
                                            <i class="bi bi-eye"></i> Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay tickets creados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal para detalles del ticket -->
            <div class="modal fade" id="modalDetallesTicket" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- El contenido se cargará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear ticket -->
    @include('tickets.modals.crear')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para ver ticket
        function verTicket(id) {
            $.ajax({
                url: `/SS-Laravel/public/tickets/${id}`,
                method: 'GET',
                success: function(response) {
                    $("#modalDetallesTicket .modal-content").html(response);
                    $("#modalDetallesTicket").modal('show');
                },
                error: function(xhr) {
                    alert('Error al cargar los detalles del ticket');
                }
            });
        }

        // Función para enviar calificación
        function enviarCalificacion(ticketId) {
            const rating = $('input[name="rating"]:checked').val();
            const comentario = $('#comentario_calificacion').val();

            if (!rating) {
                alert('Por favor, seleccione una calificación');
                return;
            }

            $.ajax({
                url: `/SS-Laravel/public/tickets/${ticketId}/calificar`,
                method: 'POST',
                data: {
                    rating: rating,
                    comentario: comentario
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalDetallesTicket').modal('hide');
                    alert('¡Gracias por tu calificación!');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error al enviar la calificación');
                }
            });
        }

        // Inicializar estrellas de calificación
        $(document).on('change', '.rating input', function() {
            const rating = $(this).val();
            $('.rating label i').removeClass('text-warning');
            $(this).prevAll('label').find('i').addClass('text-warning');
            $(this).next('label').find('i').addClass('text-warning');
        });
    </script>
    <script>
        const BASE_URL = '{{ url("/") }}';
    </script>
    <script src="{{ asset('js/tickets.js') }}"></script>
</body>
</html>
