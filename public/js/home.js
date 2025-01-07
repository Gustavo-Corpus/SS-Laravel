function getBaseUrl() {
    return window.location.pathname.includes("/public")
        ? "/SS-Laravel/public" // Ajusta 'SS-Laravel' al nombre de tu proyecto
        : "";
}

let dataTable;
let selectedEstado = "";
let pieChart = null;
let barChart = null;

function mostrarEstadisticas() {
    $.ajax({
        url: `${getBaseUrl()}/estadisticas`,
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response) {
                // Actualizar métricas
                $("#totalEmpleados").text(response.totalEmpleados || 0);
                $("#promedioGlobal").text(
                    Number(response.promedioGlobal || 0).toFixed(1)
                );
                $("#totalEstados").text(response.totalEstados || 0);

                // Preparar datos para gráficas
                const pieData = {
                    labels: response.distribucionEstados.map(
                        (item) => item.estado
                    ),
                    datasets: [
                        {
                            data: response.distribucionEstados.map(
                                (item) => item.total
                            ),
                            backgroundColor: generarColores(
                                response.distribucionEstados.length
                            ),
                        },
                    ],
                };

                const barData = {
                    labels: response.promediosPorEstado.map(
                        (item) => item.estado
                    ),
                    datasets: [
                        {
                            label: "Promedio de Calificaciones",
                            data: response.promediosPorEstado.map((item) =>
                                Number(item.promedio).toFixed(1)
                            ),
                            backgroundColor: "rgba(54, 162, 235, 0.5)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                        },
                    ],
                };

                actualizarGraficas(pieData, barData);
                $("#modalEstadisticas").modal("show");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error completo:", { xhr, status, error });
            alert("Error al cargar las estadísticas");
        },
    });
}

fetch(`${getBaseUrl()}/empleados/estadisticas`, {
    headers: {
        Accept: "application/json",
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
})
    .then((response) => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // primero obtén el texto
    })
    .then((text) => {
        console.log("Respuesta cruda:", text); // mira el texto crudo
        return JSON.parse(text); // luego intenta parsearlo
    })
    .then((data) => {
        console.log("Datos parseados:", data);
    })
    .catch((error) => {
        console.error("Error:", error);
    });

function exportarEmpleados() {
    if (!selectedEstado) {
        alert("Por favor seleccione un estado primero");
        return;
    }

    window.location.href = `${getBaseUrl()}/empleados/exportar?estado=${selectedEstado}`;
}

function cargarEmpleadosPorEstado(estadoId) {
    $.ajax({
        url: `${getBaseUrl()}/empleados/por-estado`,
        method: "GET",
        data: { estado: estadoId },
        success: function (response) {
            if (dataTable) {
                dataTable.destroy();
            }
            $("#empleadosContainer").html(response);
            initDataTable();
        },
    });
}

function editarEmpleado(id) {
    $.ajax({
        url: `${getBaseUrl()}/empleados/${id}`,
        type: "GET",
        success: function (empleado) {
            $("#nombre").val(empleado.nombre);
            $("#apellido").val(empleado.apellido);
            $("#edad").val(empleado.edad);
            $(`input[name="sexo"][value="${empleado.sexo}"]`).prop(
                "checked",
                true
            );
            $("#correo").val(empleado.correo);
            $("#estado_empleado").val(empleado.id_estado);
            $("#departamento").val(empleado.id_departamento);
            $("#ocupacion").val(empleado.ocupacion);

            // Agregar ID para actualización
            if (!$('input[name="id"]').length) {
                $("<input>")
                    .attr({
                        type: "hidden",
                        name: "id",
                        value: empleado.id_usuarios,
                    })
                    .appendTo("#empleadoForm");
            } else {
                $('input[name="id"]').val(empleado.id_usuarios);
            }

            // Mostrar avatar actual
            if (empleado.avatar) {
                if ($("#currentAvatar").length === 0) {
                    const avatarPreview = `
                        <div id="currentAvatar" class="mt-2">
                            <img src="${getBaseUrl()}/storage/fotos_empleados/${
                        empleado.avatar
                    }"
                                 class="rounded-circle"
                                 width="50" height="50">
                            <small class="text-muted ms-2">Avatar actual</small>
                        </div>`;
                    $("#avatar").after(avatarPreview);
                } else {
                    $("#currentAvatar img").attr(
                        "src",
                        `${getBaseUrl()}/storage/fotos_empleados/${
                            empleado.avatar
                        }`
                    );
                }
            }

            $('button[type="submit"]').text("Actualizar empleado");
        },
    });
}

function eliminarEmpleado(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este empleado?")) {
        $.ajax({
            url: `${getBaseUrl()}/empleados/${id}`,
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (result) {
                if (result.success) {
                    if (selectedEstado) {
                        cargarEmpleadosPorEstado(selectedEstado);
                    }
                    alert("Empleado eliminado correctamente");
                }
            },
        });
    }
}

function cargarCalificaciones(empleadoId) {
    $.ajax({
        url: `${getBaseUrl()}/calificaciones/${empleadoId}`,
        type: "GET",
        success: function (calificaciones) {
            const tbody = $("#calificacionesBody");
            tbody.empty();

            calificaciones.forEach(function (cal) {
                const mes = obtenerNombreMes(cal.mes);
                tbody.append(`
                    <tr>
                        <td>${mes}</td>
                        <td>${cal.anio}</td>
                        <td>${cal.calificacion}</td>
                        <td>${cal.comentarios || ""}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick='editarCalificacion(${JSON.stringify(
                                cal
                            ).replace(/"/g, "&quot;")})'>
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        },
    });
}

// Eventos DOM Ready
$(document).ready(function () {
    // Token CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Recuperar estado seleccionado
    selectedEstado = localStorage.getItem("selectedEstado") || "";
    if (selectedEstado) {
        $("#estado").val(selectedEstado);
        cargarEmpleadosPorEstado(selectedEstado);
    }

    // Inicializar DataTable
    initDataTable();

    // Cambio de estado
    $("#estado").change(function () {
        selectedEstado = $(this).val();
        localStorage.setItem("selectedEstado", selectedEstado);
        if (selectedEstado) {
            cargarEmpleadosPorEstado(selectedEstado);
        } else {
            if (dataTable) {
                dataTable.clear().draw();
            }
        }
    });

    // Formulario de empleado
    $("#empleadoForm").on("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = $('input[name="id"]').val();

        // URL y método varían según si es actualización o creación
        const url = id
            ? `${getBaseUrl()}/empleados/${id}`
            : `${getBaseUrl()}/empleados`;

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    alert(
                        id
                            ? "Empleado actualizado correctamente"
                            : "Empleado agregado correctamente"
                    );

                    // Limpiar formulario
                    $("#empleadoForm")[0].reset();
                    $("#currentAvatar").remove();
                    $('input[name="id"]').remove();
                    $('button[type="submit"]').text("Agregar empleado");

                    // Recargar la tabla
                    if (selectedEstado) {
                        cargarEmpleadosPorEstado(selectedEstado);
                    }
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseJSON);
                alert(
                    "Error: " +
                        (xhr.responseJSON?.message || "Error desconocido")
                );
            },
        });
    });

    // Formulario de calificaciones
    $("#calificacionForm").on("submit", function (e) {
        e.preventDefault();
        const formData = {
            empleado_id: $("#empleado_id").val(),
            mes: $("#mes").val(),
            anio: $("#anio").val(),
            calificacion: $("#calificacion").val(),
            comentarios: $("#comentarios").val(),
        };

        const calificacionId = $("#calificacion_id").val();
        const url = calificacionId
            ? `${getBaseUrl()}/calificaciones/${calificacionId}`
            : `${getBaseUrl()}/calificaciones`;

        $.ajax({
            url: url,
            type: calificacionId ? "PUT" : "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    alert("Calificación guardada correctamente");
                    limpiarFormularioCalificacion();
                    cargarCalificaciones($("#empleado_id").val());
                    if (selectedEstado) {
                        cargarEmpleadosPorEstado(selectedEstado);
                    }
                }
            },
        });
    });
});

// Funciones auxiliares
// En public/js/home.js, modifica la función initDataTable:
function initDataTable() {
    dataTable = $("#table_empleados").DataTable({
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente",
            },
        },
        searching: true,
        processing: true,
        pageLength: 10,
    });
}

function actualizarGraficas(pieData, barData) {
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();

    const pieCtx = document.getElementById("pieChart").getContext("2d");
    pieChart = new Chart(pieCtx, {
        type: "pie",
        data: pieData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "right",
                },
            },
        },
    });

    const barCtx = document.getElementById("barChart").getContext("2d");
    barChart = new Chart(barCtx, {
        type: "bar",
        data: barData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                },
            },
        },
    });
}

function generarColores(cantidad) {
    const colores = [];
    for (let i = 0; i < cantidad; i++) {
        const hue = (i * 360) / cantidad;
        colores.push(`hsl(${hue}, 70%, 60%)`);
    }
    return colores;
}

function obtenerNombreMes(numeroMes) {
    const meses = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
    ];
    return meses[numeroMes - 1];
}

function abrirModalCalificaciones(empleadoId, nombreEmpleado) {
    // Limpiar el formulario
    limpiarFormularioCalificacion();

    // Establecer el ID del empleado
    $("#empleado_id").val(empleadoId);

    // Actualizar el título del modal con el nombre del empleado
    $("#modalCalificacionesLabel").text(`Calificaciones - ${nombreEmpleado}`);

    // Cargar las calificaciones existentes
    cargarCalificaciones(empleadoId);

    // Mostrar el modal
    $("#modalCalificaciones").modal("show");
}

function limpiarFormularioCalificacion() {
    $("#calificacionForm")[0].reset();
    $("#calificacion_id").val("");
    $("#empleado_id").val("");

    // Establecer valores por defecto
    const fechaActual = new Date();
    $("#mes").val(fechaActual.getMonth() + 1);
    $("#anio").val(fechaActual.getFullYear());
}

function editarCalificacion(calificacion) {
    $("#calificacion_id").val(calificacion.id_evaluacion);
    $("#mes").val(calificacion.mes);
    $("#anio").val(calificacion.anio);
    $("#calificacion").val(calificacion.calificacion);
    $("#comentarios").val(calificacion.comentarios || "");
}

function verDetallesEmpleado(id) {
    $.ajax({
        url: `${getBaseUrl()}/empleados/${id}`,
        type: "GET",
        success: function (empleado) {
            // Actualizar la información en el modal
            $("#detalleNombreCompleto").text(
                `${empleado.nombre} ${empleado.apellido}`
            );
            $("#detalleEdad").text(empleado.edad);
            $("#detalleCorreo").text(empleado.correo);
            $("#detallePuesto").text(empleado.ocupacion);
            $("#detalleDepartamento").text(
                empleado.departamento?.nombre_departamento || "No asignado"
            );
            $("#detalleEstado").text(empleado.estado?.estado || "No asignado");

            // Manejo específico del promedio
            const promedio =
                empleado.promedio_calificacion || empleado.promedio || 0;
            $("#detallePromedio").text(
                promedio ? Number(promedio).toFixed(1) : "0.0"
            );

            // Manejar el avatar
            if (empleado.avatar) {
                $("#detalleAvatar")
                    .attr(
                        "src",
                        `${getBaseUrl()}/storage/fotos_empleados/${
                            empleado.avatar
                        }`
                    )
                    .show();
                $("#defaultAvatar").hide();
            } else {
                $("#detalleAvatar").hide();
                $("#defaultAvatar").show();
            }

            // Mostrar el modal
            $("#modalDetallesEmpleado").modal("show");
        },
        error: function (xhr) {
            console.error(xhr.responseJSON);
            alert("Error al cargar los detalles del empleado");
        },
    });
}
