<style>
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.5rem;
    justify-content: flex-start;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    cursor: pointer;
    color: #ddd;
    font-size: 1.5rem;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.star-rating label i {
    transition: color 0.2s ease-in-out;
}
</style>

<div class="modal-header">
    <h5 class="modal-title">Detalles del Ticket #{{ $ticket->numero_ticket }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <h6 class="fw-bold">Estado</h6>
            <span class="badge bg-{{
                $ticket->estado === 'abierto' ? 'danger' :
                ($ticket->estado === 'en_proceso' ? 'warning' :
                ($ticket->estado === 'resuelto' ? 'success' : 'secondary'))
            }}">
                {{ ucfirst($ticket->estado) }}
            </span>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold">Prioridad</h6>
            <span class="badge bg-{{
                $ticket->prioridad === 'alta' ? 'danger' :
                ($ticket->prioridad === 'media' ? 'warning' : 'info')
            }}">
                {{ ucfirst($ticket->prioridad) }}
            </span>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <h6 class="fw-bold">Cliente</h6>
            <p>{{ $ticket->usuario->nombre ?? 'N/A' }} {{ $ticket->usuario->apellido ?? '' }}</p>

            <h6 class="fw-bold">Título</h6>
            <p>{{ $ticket->titulo }}</p>

            <h6 class="fw-bold">Descripción</h6>
            <p>{{ $ticket->descripcion }}</p>

            @if($ticket->comentario)
                <h6 class="fw-bold mt-3">Comentarios previos</h6>
                <p>{{ $ticket->comentario }}</p>
            @endif

            @if(auth()->user()->rol === 'empleado' && in_array($ticket->estado, ['en_proceso', 'abierto']))
                <h6 class="fw-bold mt-3">Agregar Comentario</h6>
                <div class="mb-3">
                    <textarea class="form-control" id="comentario_ticket" rows="3"
                              placeholder="Escribe aquí tu comentario o solución..."></textarea>
                </div>
            @endif
        </div>
    </div>
</div>

    @if(auth()->user()->rol === 'cliente' && $ticket->estado === 'resuelto' && !$ticket->calificaciones->count())
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="fw-bold text-center">Calificar Atención</h6>
                <form id="formCalificacion">
                    @csrf
                    <div class="mb-3 text-center">
                        <label class="form-label">Calificación</label>
                        <div class="star-rating mx-auto" style="width: fit-content;">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                                <label for="star{{ $i }}">
                                    <i class="bi bi-star-fill"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comentario_calificacion" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comentario_calificacion" name="comentario" rows="3"></textarea>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($ticket->calificaciones->count() > 0)
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="fw-bold">Calificación del Cliente</h6>
                <div class="d-flex align-items-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $ticket->calificaciones->first()->calificacion ? '-fill' : '' }} text-warning"></i>
                    @endfor
                    <span class="ms-2">({{ $ticket->calificaciones->first()->calificacion }}/5)</span>
                </div>
                @if($ticket->calificaciones->first()->comentario)
                    <p class="mb-0"><strong>Comentario:</strong> {{ $ticket->calificaciones->first()->comentario }}</p>
                @endif
            </div>
        </div>
    @endif
</div>

<div class="modal-footer">
    @if(auth()->user()->rol === 'empleado')
        @if($ticket->estado === 'abierto')
            <button type="button" class="btn btn-primary" onclick="marcarEnProceso({{ $ticket->id }})">
                <i class="bi bi-play-fill"></i> Marcar En Proceso
            </button>
        @elseif($ticket->estado === 'en_proceso')
            <button type="button" class="btn btn-success" onclick="marcarResuelto({{ $ticket->id }})">
                <i class="bi bi-check2-circle"></i> Marcar como Resuelto
            </button>
        @endif
    @endif
    @if(auth()->user()->rol === 'cliente' && $ticket->estado === 'resuelto' && !$ticket->calificaciones->count())
        <button type="button" class="btn btn-primary" onclick="enviarCalificacion({{ $ticket->id }})">
            <i class="bi bi-star-fill me-1"></i> Enviar Calificación
        </button>
    @endif
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>

<script>
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
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            rating: rating,
            comentario: comentario
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

// Reiniciar calificación al abrir el modal
$('#modalDetallesTicket').on('show.bs.modal', function() {
    $('input[name="rating"]').prop('checked', false);
    $('#comentario_calificacion').val('');
});
</script>
