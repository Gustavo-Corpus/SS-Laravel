<div class="table-responsive">
    <table id="table_empleados" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Puesto</th>
                <th>Promedio</th>
                <th>Avatar</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados ?? [] as $empleado)
            <tr>
                <td>{{ $empleado->id_usuarios }}</td>
                <td>{{ $empleado->nombre }}</td>
                <td>{{ $empleado->apellido }}</td>
                <td>{{ $empleado->ocupacion }}</td>
                <td>{{ number_format($empleado->promedio_calificacion ?? 0, 1) }}</td>
                <td>
                    @if($empleado->avatar)
                        <img src="{{ asset('storage/fotos_empleados/' . $empleado->avatar) }}"
                            class="rounded-circle"
                            width="50"
                            height="50"
                            alt="Avatar de {{ $empleado->nombre }}"
                            onerror="this.src='{{ asset('images/default-avatar.png') }}'"
                        >
                    @else
                        <div class="text-center">
                            <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <button class="btn btn-info btn-sm"
                            onclick="verDetallesEmpleado({{ $empleado->id_usuarios }})"
                            title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm"
                            onclick="abrirModalCalificaciones({{ $empleado->id_usuarios }}, '{{ $empleado->nombre }} {{ $empleado->apellido }}')"
                            title="Calificaciones">
                        <i class="bi bi-star-fill"></i>
                    </button>
                    <button class="btn btn-warning btn-sm"
                            onclick="editarEmpleado({{ $empleado->id_usuarios }})"
                            title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm"
                            onclick="eliminarEmpleado({{ $empleado->id_usuarios }})"
                            title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal de Vista Rápida -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card border-0">
                    <div class="row g-0">
                        <div class="col-md-4 p-4 text-center">
                            <img id="qv-avatar" src="" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h4 id="qv-nombre" class="mb-0"></h4>
                            <p id="qv-puesto" class="text-muted"></p>
                        </div>
                        <div class="col-md-8 p-4">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-building me-2"></i>
                                        <span id="qv-departamento"></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope me-2"></i>
                                        <span id="qv-correo"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        <span id="qv-promedio" class="h4 mb-0"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="evaluacionesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
