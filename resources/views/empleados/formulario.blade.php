<form id="empleadoForm" class="mt-4">
    @csrf
    <!-- Campo oculto para el ID cuando se está editando -->
    <input type="hidden" name="id" value="">
    <!-- Resto del formulario -->

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>

    <div class="mb-3">
        <label for="apellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="apellido" name="apellido" required>
    </div>

    <div class="mb-3">
        <label for="edad" class="form-label">Edad</label>
        <input type="number" class="form-control" id="edad" name="edad" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Sexo</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sexo" id="masculino" value="Masculino" required>
                <label class="form-check-label" for="masculino">Masculino</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sexo" id="femenino" value="Femenino" required>
                <label class="form-check-label" for="femenino">Femenino</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
    </div>

    <div class="mb-3">
        <label for="estado_empleado" class="form-label">Estado</label>
        <select class="form-select" id="estado_empleado" name="estado_empleado" required>
            <option value="">Seleccione...</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id_estado }}">{{ $estado->estado }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="departamento" class="form-label">Departamento</label>
        <select class="form-select" id="departamento" name="departamento" required>
            <option value="">Seleccione...</option>
            @foreach($departamentos as $departamento)
                <option value="{{ $departamento->id_departamento }}">{{ $departamento->nombre_departamento }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="ocupacion" class="form-label">Puesto</label>
        <input type="text" class="form-control" id="ocupacion" name="ocupacion" required>
    </div>

    <div class="mb-3">
        <label for="avatar" class="form-label">Foto del empleado</label>
        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary w-100">Agregar empleado</button>
</form>
