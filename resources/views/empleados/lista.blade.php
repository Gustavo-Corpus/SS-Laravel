<div id="employeesWrapper">
    <!-- Botones de cambio de vista -->
    <div class="mb-3">
        <div class="btn-group" role="group" aria-label="Vista de empleados">
            <button type="button" class="btn btn-outline-primary active" onclick="cambiarVista('tabla')" id="btnTabla">
                <i class="bi bi-table me-1"></i> Tabla
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="cambiarVista('tarjetas')" id="btnTarjetas">
                <i class="bi bi-grid-3x3-gap me-1"></i> Tarjetas
            </button>
        </div>
    </div>

    <!-- Vista de Tabla -->
    <div id="vistaTabla">
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
                        <td class="actions-column">
                            <div class="d-flex align-items-center gap-1">
                                <button class="btn btn-info btn-sm" onclick="verDetalles({{ $empleado->id_usuarios }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-success btn-sm" onclick="verCalificaciones({{ $empleado->id_usuarios }})">
                                    <i class="bi bi-star"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editarEmpleado({{ $empleado->id_usuarios }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarEmpleado({{ $empleado->id_usuarios }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vista de Tarjetas -->
    <div id="vistaTarjetas" style="display: none;">
        <div class="row g-4">
            @foreach($empleados ?? [] as $empleado)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-hover">
                    <div class="card-header bg-light text-center border-0 pt-4 pb-3">
                        @if($empleado->avatar)
                            <img src="{{ asset('storage/fotos_empleados/' . $empleado->avatar) }}"
                                class="rounded-circle mb-2"
                                style="width: 100px; height: 100px; object-fit: cover;"
                                alt="Avatar de {{ $empleado->nombre }}"
                                onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                        @else
                            <div class="avatar-placeholder mb-2">
                                <i class="bi bi-person-circle" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <h5 class="card-title mb-0">{{ $empleado->nombre }} {{ $empleado->apellido }}</h5>
                        <p class="text-muted small mb-0">{{ $empleado->ocupacion }}</p>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building text-muted me-2"></i>
                                <span class="small">{{ $empleado->departamento->nombre_departamento }}</span>
                            </div>
                            <span class="badge bg-{{ $empleado->promedio_calificacion >= 8 ? 'success' : ($empleado->promedio_calificacion >= 6 ? 'warning' : 'danger') }}">
                                {{ number_format($empleado->promedio_calificacion ?? 0, 1) }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt text-muted me-2"></i>
                            <span class="small">{{ $empleado->estado->estado }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="btn-group w-100">
                            <button class="btn btn-sm btn-outline-info"
                                    onclick="verDetallesEmpleado({{ $empleado->id_usuarios }})"
                                    title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success"
                                    onclick="abrirModalCalificaciones({{ $empleado->id_usuarios }}, '{{ $empleado->nombre }} {{ $empleado->apellido }}')"
                                    title="Calificaciones">
                                <i class="bi bi-star"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning"
                                    onclick="editarEmpleado({{ $empleado->id_usuarios }})"
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarEmpleado({{ $empleado->id_usuarios }})"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal de Vista Rápida Mejorado -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card border-0">
                    <div class="row g-0">
                        <!-- Columna de Perfil -->
                        <div class="col-md-4 bg-light p-4 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img id="qv-avatar" src="" class="rounded-circle img-thumbnail"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <span id="qv-status" class="position-absolute bottom-0 end-0 p-2 rounded-circle"></span>
                            </div>
                            <h4 id="qv-nombre" class="fw-bold mb-1"></h4>
                            <p id="qv-puesto" class="text-muted mb-3"></p>
                            <div id="qv-performance" class="d-inline-block px-3 py-1 rounded-pill"></div>

                            <!-- Indicador de Rendimiento Circular -->
                            <div class="performance-indicators mb-4">
                                <div class="d-flex justify-content-center gap-3">
                                    <div class="text-center">
                                        <!-- Reemplaza el SVG actual con este -->
                                        <div class="progress-circular">
                                            <svg viewBox="0 0 36 36" class="circular-chart">
                                                <circle cx="18" cy="18" r="16"
                                                        fill="none"
                                                        stroke="#eee"
                                                        stroke-width="2.5"/>
                                                <circle cx="18" cy="18" r="16"
                                                        fill="none"
                                                        stroke="#2196f3"
                                                        stroke-width="2.5"
                                                        stroke-linecap="round"
                                                        class="progress-ring-circle"
                                                        id="progress-ring-circle"/>
                                            </svg>
                                            <div class="progress-value">0%</div>
                                        </div>
                                        <div class="mt-2 text-muted small">Rendimiento Global</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de Información -->
                        <div class="col-md-8 p-4">
                            <!-- Métricas en Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-2">Departamento</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building me-2"></i>
                                                <span id="qv-departamento" class="fw-bold"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-2">Promedio</h6>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-star-fill text-warning me-2"></i>
                                                <span id="qv-promedio" class="fw-bold"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas Adicionales -->
                            <div class="stats-cards row g-2 mb-4">
                                <div class="col-4">
                                    <div class="stats-card bg-gradient-primary text-white p-3 rounded-3">
                                        <div class="stats-icon">
                                            <i class="bi bi-calendar-check fs-4"></i>
                                        </div>
                                        <div class="stats-info mt-3">
                                            <h6 class="mb-1" id="qv-asistencia">98%</h6>
                                            <small>Asistencia</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stats-card bg-gradient-success text-white p-3 rounded-3">
                                        <div class="stats-icon">
                                            <i class="bi bi-trophy fs-4"></i>
                                        </div>
                                        <div class="stats-info mt-3">
                                            <h6 class="mb-1" id="qv-logros">12</h6>
                                            <small>Logros</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stats-card bg-gradient-info text-white p-3 rounded-3">
                                        <div class="stats-icon">
                                            <i class="bi bi-graph-up fs-4"></i>
                                        </div>
                                        <div class="stats-info mt-3">
                                            <h6 class="mb-1" id="qv-progreso">+15%</h6>
                                            <small>Progreso</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gráfica con título -->
                            <h5 class="mb-3">Evaluaciones Recientes</h5>
                            <div class="chart-container position-relative" style="height: 200px;">
                                <canvas id="evaluacionesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
