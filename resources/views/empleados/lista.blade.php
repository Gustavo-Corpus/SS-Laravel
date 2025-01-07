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
