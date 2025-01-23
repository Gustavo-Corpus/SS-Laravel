<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de Casos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Estilos para la ventana plegable */
        #encuestaWrapper {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: height 0.3s ease-in-out;
            height: 50px;
        }

        #encuestaWrapper.expanded {
            height: 400px;
        }

        #encuestaHeader {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        #encuestaContent {
            display: none;
            padding: 15px;
        }

        #encuestaWrapper.expanded #encuestaContent {
            display: block;
        }

        .floating-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Encabezado con botones de navegación -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Administración de Casos</h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Regresar al Dashboard</a>
                <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>

        <!-- Filtros -->
        <div class="d-flex justify-content-between mb-4">
            <button class="btn btn-primary" onclick="filtrarCasos('abierto')">Casos Abiertos</button>
            <button class="btn btn-success" onclick="filtrarCasos('cerrado')">Casos Cerrados</button>
            <button class="btn btn-warning" onclick="filtrarCasos('proceso')">Casos en Proceso</button>
            <button class="btn btn-secondary" onclick="filtrarCasos('todos')">Todos los Casos</button>
        </div>

        <!-- Tabla de casos -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Estatus</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaCasos">
                    <tr data-status="abierto">
                        <td>1</td>
                        <td>El sistema no carga correctamente</td>
                        <td><span class="badge bg-primary">Abierto</span></td>
                        <td>2025-01-22</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editarCaso(1, 'El sistema no carga correctamente', 'abierto')">Editar</button>
                        </td>
                    </tr>
                    <tr data-status="cerrado">
                        <td>2</td>
                        <td>Problema con la conexión a la base de datos</td>
                        <td><span class="badge bg-success">Cerrado</span></td>
                        <td>2025-01-21</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editarCaso(2, 'Problema con la conexión a la base de datos', 'cerrado')">Editar</button>
                        </td>
                    </tr>
                    <tr data-status="proceso">
                        <td>3</td>
                        <td>Error en la generación de reportes</td>
                        <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                        <td>2025-01-20</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editarCaso(3, 'Error en la generación de reportes', 'proceso')">Editar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Encuesta -->
    <div id="encuestaWrapper">
        <div id="encuestaHeader">
            <span>Encuesta de Satisfacción</span>
            <button class="btn btn-sm btn-light" id="toggleEncuesta"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div id="encuestaContent">
            <form id="encuestaForm" action="{{ route('encuesta.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="pregunta1" class="form-label">1. ¿Cómo calificarías nuestro servicio? (1 a 5)</label>
                    <select id="pregunta1" name="pregunta1" class="form-select" required>
                        <option value="">Selecciona una opción</option>
                        <option value="1">1 - Muy Malo</option>
                        <option value="2">2 - Malo</option>
                        <option value="3">3 - Regular</option>
                        <option value="4">4 - Bueno</option>
                        <option value="5">5 - Excelente</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pregunta2" class="form-label">2. ¿Qué tan rápido atendimos tu solicitud?</label>
                    <select id="pregunta2" name="pregunta2" class="form-select" required>
                        <option value="">Selecciona una opción</option>
                        <option value="1">1 - Muy Lento</option>
                        <option value="2">2 - Lento</option>
                        <option value="3">3 - Regular</option>
                        <option value="4">4 - Rápido</option>
                        <option value="5">5 - Muy Rápido</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pregunta3" class="form-label">3. ¿Qué sugerencias tienes para mejorar nuestro servicio?</label>
                    <textarea id="pregunta3" name="pregunta3" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Enviar Encuesta</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggleEncuesta');
            const encuestaWrapper = document.getElementById('encuestaWrapper');

            toggleButton.addEventListener('click', function () {
                encuestaWrapper.classList.toggle('expanded');
                toggleButton.innerHTML = encuestaWrapper.classList.contains('expanded')
                    ? '<i class="bi bi-chevron-up"></i>'
                    : '<i class="bi bi-chevron-down"></i>';
            });
        });

        function filtrarCasos(status) {
            const rows = document.querySelectorAll('#tablaCasos tr');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                row.style.display = (status === 'todos' || rowStatus === status) ? '' : 'none';
            });
        }

        function editarCaso(id, descripcion, estatus) {
            document.getElementById('editarCasoId').value = id;
            document.getElementById('editarCasoDescripcion').value = descripcion;
            document.getElementById('editarCasoEstatus').value = estatus;
            new bootstrap.Modal(document.getElementById('editarCasoModal')).show();
        }
    </script>
</body>
</html>
