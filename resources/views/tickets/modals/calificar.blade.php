<div class="modal fade" id="modalCalificarTicket" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Cómo calificarías la resolución de tu ticket?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCalificarTicket">
                    @csrf
                    <div class="mb-3 text-center">
                        <div class="rating">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                <label for="star{{ $i }}">
                                    <i class="bi bi-star-fill fs-2"></i>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentarios adicionales</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="¿Qué podríamos mejorar?"></textarea>
                    </div>

                    <input type="hidden" id="ticketId" name="ticketId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarCalificacion()">Enviar Calificación</button>
            </div>
        </div>
    </div>
</div>
