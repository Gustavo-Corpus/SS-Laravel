<div class="modal fade" id="modalCalificaciones" tabindex="-1" aria-labelledby="modalCalificacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCalificacionesLabel">Calificaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de calificación -->
                <form id="calificacionForm">
                    @csrf
                    <input type="hidden" id="empleado_id" name="empleado_id">
                    <input type="hidden" id="calificacion_id" name="calificacion_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mes" class="form-label">Mes</label>
                            <select class="form-select" id="mes" name="mes" required>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="anio" class="form-label">Año</label>
                            <select class="form-select" id="anio" name="anio" required>
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="calificacion" class="form-label">Calificación (0-10)</label>
                        <input type="number" class="form-control" id="calificacion" name="calificacion"
                               min="0" max="10" step="0.1" required>
                    </div>

                    <div class="mb-3">
                        <label for="comentarios" class="form-label">Comentarios</label>
                        <textarea class="form-control" id="comentarios" name="comentarios" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="limpiarFormularioCalificacion()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">Guardar Calificación</button>
                    </div>
                </form>

                <hr>

                <!-- Tabla de calificaciones existentes -->
                <h5 class="mt-4">Evaluaciones existentes</h5>
                <div class="table-responsive">
                    <table class="table table-striped" id="tablaCalificaciones">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Año</th>
                                <th>Calificación</th>
                                <th>Comentarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="calificacionesBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
