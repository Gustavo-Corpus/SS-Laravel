<div class="modal fade" id="modalDetallesEmpleado" tabindex="-1" aria-labelledby="modalDetallesEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesEmpleadoLabel">Datos del empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="detalleAvatar" src="" class="rounded-circle" width="100" height="100"
                         style="display: none;" alt="Avatar del empleado">
                    <i id="defaultAvatar" class="bi bi-person-circle" style="font-size: 4rem; display: none;"></i>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p><strong>Nombre completo:</strong> <span id="detalleNombreCompleto"></span></p>
                        <p><strong>Edad:</strong> <span id="detalleEdad"></span></p>
                        <p><strong>Correo:</strong> <span id="detalleCorreo"></span></p>
                        <p><strong>Puesto:</strong> <span id="detallePuesto"></span></p>
                        <p><strong>Departamento:</strong> <span id="detalleDepartamento"></span></p>
                        <p><strong>Estado:</strong> <span id="detalleEstado"></span></p>
                        <p><strong>Promedio de calificación:</strong> <span id="detallePromedio"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
