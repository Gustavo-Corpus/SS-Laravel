<div class="modal-header">
    <h5 class="modal-title">Detalles del Ticket #{{ $ticket->numero_ticket }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row">
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

    <!-- Form para edición -->
    <div id="editForm" style="display: none;">
        <hr>
        <form id="ticketEditForm">
            @csrf
            <div class="mb-3">
                <label for="edit_titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="edit_titulo" name="titulo" value="{{ $ticket->titulo }}">
            </div>
            <div class="mb-3">
                <label for="edit_descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3">{{ $ticket->descripcion }}</textarea>
            </div>
            <div class="mb-3">
                <label for="edit_prioridad" class="form-label">Prioridad</label>
                <select class="form-select" id="edit_prioridad" name="prioridad">
                    <option value="baja" {{ $ticket->prioridad === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ $ticket->prioridad === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ $ticket->prioridad === 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-12">
            <h6 class="fw-bold">Título</h6>
            <p>{{ $ticket->titulo }}</p>

            <h6 class="fw-bold">Descripción</h6>
            <p>{{ $ticket->descripcion }}</p>

            @if($ticket->comentario)
                <h6 class="fw-bold mt-3">Comentarios del Empleado</h6>
                <p>{{ $ticket->comentario }}</p>
            @endif
        </div>
    </div>

    @if(auth()->user()->rol === 'cliente' && $ticket->estado === 'resuelto' && !$ticket->calificaciones->count())
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="fw-bold">Calificar Atención</h6>
                <form id="formCalificacion">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Calificación</label>
                        <div class="rating">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
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
    @if(auth()->user()->rol === 'cliente' && $ticket->estado === 'abierto')
        <button type="button" class="btn btn-primary" onclick="toggleEdit()">Editar</button>
        <button type="button" class="btn btn-danger" onclick="eliminarTicket({{ $ticket->id }})">Eliminar</button>
        <button type="button" class="btn btn-success" id="btnGuardar" style="display: none;" onclick="guardarCambios({{ $ticket->id }})">Guardar Cambios</button>
    @endif
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
