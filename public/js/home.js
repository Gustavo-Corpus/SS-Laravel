let dataTable;
let selectedEstado = "";
let pieChart = null;
let barChart = null;

function mostrarEstadisticas() {
    $.ajax({
        url: "/empleados/estadisticas",
        type: "GET",
        success: function (stats) {
            // Actualizar métricas generales
            $("#totalEmpleados").text(stats.totalEmpleados);
            $("#promedioGlobal").text(stats.promedioGlobal.toFixed(2));
            $("#totalEstados").text(stats.totalEstados);

            // Preparar datos para la gráfica de pastel
            const pieData = {
                labels: stats.distribucionEstados.map((item) => item.estado),
                datasets: [
                    {
                        data: stats.distribucionEstados.map(
                            (item) => item.total
                        ),
                        backgroundColor: generarColores(
                            stats.distribucionEstados.length
                        ),
                    },
                ],
            };

            // Preparar datos para la gráfica de barras
            const barData = {
                labels: stats.promediosPorEstado.map((item) => item.estado),
                datasets: [
                    {
                        label: "Promedio de Calificaciones",
                        data: stats.promediosPorEstado.map(
                            (item) => item.promedio
                        ),
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            };

            // Actualizar gráficas
            actualizarGraficas(pieData, barData);

            // Mostrar modal
            $("#modalEstadisticas").modal("show");
        },
    });
}

function exportarEmpleados() {
    if (!selectedEstado) {
        alert("Por favor seleccione un estado primero");
        return;
    }

    window.location.href = `/empleados/exportar?estado=${selectedEstado}`;
}

function cargarEmpleadosPorEstado(estadoId) {
    $.ajax({
        url: "/empleados/por-estado",
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
        url: `/empleados/${id}`,
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
                            <img src="/storage/fotos_empleados/${empleado.avatar}"
                                 class="rounded-circle"
                                 width="50" height="50">
                            <small class="text-muted ms-2">Avatar actual</small>
                        </div>`;
                    $("#avatar").after(avatarPreview);
                } else {
                    $("#currentAvatar img").attr(
                        "src",
                        `/storage/fotos_empleados/${empleado.avatar}`
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
            url: `/empleados/${id}`,
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
        url: `/calificaciones/${empleadoId}`,
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
        const url = id ? `/empleados/${id}` : "/empleados";

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

        $.ajax({
            url: calificacionId
                ? `/calificaciones/${calificacionId}`
                : "/calificaciones",
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
