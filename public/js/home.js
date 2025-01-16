function getBaseUrl() {
    return window.location.pathname.includes("/public")
        ? "/SS-Laravel/public" // Ajusta 'SS-Laravel' al nombre de tu proyecto
        : "";
}

let dataTable;
let selectedEstado = "";
let pieChart = null;
let barChart = null;
let quickViewTimeout;
const quickViewDelay = 500; // medio segundo de delay antes de mostrar
let currentChart = null;

let currentView = "table";

// Agregar eventos de hover a las imágenes de avatar
$(document).on(
    {
        mouseenter: function () {
            const empleadoId = $(this).closest("tr").find("td:first").text();
            quickViewTimeout = setTimeout(
                () => showQuickView(empleadoId),
                quickViewDelay
            );
        },
        mouseleave: function () {
            clearTimeout(quickViewTimeout);
            // Pequeño delay antes de cerrar para permitir mover el mouse al modal
            setTimeout(() => {
                if (!$("#quickViewModal:hover").length) {
                    $("#quickViewModal").modal("hide");
                }
            }, 300);
        },
    },
    "#table_empleados tbody tr img"
);

// Mantener modal abierto mientras el mouse está sobre él
$("#quickViewModal").hover(
    function () {
        /* no hacer nada al entrar */
    },
    function () {
        $(this).modal("hide");
    }
);

function showQuickView(empleadoId) {
    console.log("ID del empleado:", empleadoId);

    $.get(
        `${getBaseUrl()}/empleado/${empleadoId}/evaluaciones`,
        function (response) {
            console.log("Respuesta completa:", response);

            if (response.success) {
                const empleado = response.empleado;

                // Actualizar datos básicos con animación
                $("#qv-nombre")
                    .text(`${empleado.nombre} ${empleado.apellido}`)
                    .hide()
                    .fadeIn(500);
                $("#qv-puesto").text(empleado.ocupacion).hide().fadeIn(700);
                $("#qv-departamento")
                    .text(empleado.departamento)
                    .hide()
                    .fadeIn(900);

                // Actualizar avatar con animación
                $("#qv-avatar")
                    .attr(
                        "src",
                        empleado.avatar
                            ? `${getBaseUrl()}/storage/fotos_empleados/${
                                  empleado.avatar
                              }`
                            : `${getBaseUrl()}/images/default-avatar.png`
                    )
                    .hide()
                    .fadeIn(1000);

                // Calcular promedio y porcentaje
                const promedio = parseFloat(empleado.promedio || 0);
                const porcentaje = Math.min(Math.round(promedio * 10), 100); // Asegurar que no exceda 100%
                console.log("Promedio:", promedio);
                console.log("Porcentaje calculado:", porcentaje);

                // Actualizar anillo de progreso y rendimiento
                updateProgressRing(porcentaje);

                // Determinar clase de rendimiento basada en el promedio
                const performanceClass =
                    promedio < 6
                        ? "bg-danger text-white"
                        : promedio < 8
                        ? "bg-warning text-dark"
                        : "bg-success text-white";

                $("#qv-performance")
                    .removeClass()
                    .addClass(performanceClass)
                    .html(
                        `<i class="bi bi-graph-up me-1"></i>${promedio.toFixed(
                            1
                        )}`
                    )
                    .hide()
                    .fadeIn(1200);

                // Actualizar el promedio con animación
                $("#qv-promedio")
                    .prop("Counter", 0)
                    .animate(
                        {
                            Counter: promedio,
                        },
                        {
                            duration: 1000,
                            step: function (now) {
                                $(this).text(now.toFixed(1));
                            },
                        }
                    );

                // Actualizar estadísticas adicionales con animación
                animateValue("#qv-asistencia", 0, 98, 1500);
                animateValue("#qv-logros", 0, empleado.logros || 12, 1500);
                animateValue("#qv-progreso", 0, 15, 1500, "+");

                // Actualizar gráfica si hay evaluaciones
                if (empleado.evaluaciones?.length > 0) {
                    updateChart(empleado.evaluaciones);
                }

                // Mostrar modal
                $("#quickViewModal").modal("show");
            }
        }
    ).fail(function (error) {
        console.error("Error al cargar datos:", error);
    });
}

function animateValue(
    selector,
    start,
    end,
    duration,
    prefix = "",
    suffix = "%"
) {
    $({ val: start }).animate(
        { val: end },
        {
            duration: duration,
            step: function (now) {
                $(selector).text(prefix + Math.round(now) + suffix);
            },
        }
    );
}

function updateChart(evaluaciones) {
    if (currentChart) {
        currentChart.destroy();
    }

    const ctx = document.getElementById("evaluacionesChart").getContext("2d");
    currentChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: evaluaciones.map((e) => e.mes),
            datasets: [
                {
                    label: "Calificación",
                    data: evaluaciones.map((e) => e.valor),
                    borderColor: "#0d6efd",
                    backgroundColor: "rgba(13, 110, 253, 0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#ffffff",
                    pointBorderColor: "#0d6efd",
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    grid: {
                        drawBorder: false,
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
            animation: {
                duration: 1500,
                easing: "easeInOutQuart",
            },
        },
    });
}

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
    // Manejo de vistas
    $("#tableViewBtn, #cardViewBtn").on("click", function (e) {
        e.preventDefault();
        console.log("Botón clickeado:", this.id); // Debug

        const targetView = this.id === "tableViewBtn" ? "table" : "card";
        switchView(targetView);
    });

    // Token CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#quickViewModal").on("hidden.bs.modal", function () {
        const circle = document.getElementById("progress-ring-circle");
        const valueDisplay = circle
            ?.closest(".progress-circular")
            ?.querySelector(".progress-value");

        if (circle && valueDisplay) {
            const radius = circle.r.baseVal.value;
            const circumference = 2 * Math.PI * radius;

            circle.style.transition = "none";
            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = circumference;
            valueDisplay.textContent = "0%";
        }
    });

    // Añadir efecto hover en las tarjetas de estadísticas
    $(".stats-card").hover(
        function () {
            $(this).addClass("transform-active");
        },
        function () {
            $(this).removeClass("transform-active");
        }
    );

    // Inicializar tooltips de Bootstrap
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
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

// Función para cambiar entre vistas
function cambiarVista(vista) {
    console.log("Cambiando a vista:", vista);

    // Actualizar botones
    $("#btnTabla, #btnTarjetas").removeClass("active");
    $(`#btn${vista.charAt(0).toUpperCase() + vista.slice(1)}`).addClass(
        "active"
    );

    // Cambiar vistas con animación
    if (vista === "tabla") {
        $("#vistaTarjetas").fadeOut(300, function () {
            $("#vistaTabla").fadeIn(300);
            if (dataTable) {
                dataTable.columns.adjust();
            }
        });
    } else {
        $("#vistaTabla").fadeOut(300, function () {
            $("#vistaTarjetas").fadeIn(300);
            // Animar la entrada de las tarjetas
            $(".card").each(function (index) {
                $(this)
                    .delay(index * 100)
                    .animate({ opacity: 1 }, 500);
            });
        });
    }
}

// Función para animar las tarjetas
function animateCards() {
    console.log("Animando tarjetas"); // Debug
    $(".employee-card").each(function (index) {
        $(this)
            .css("opacity", 0)
            .delay(index * 100)
            .animate({ opacity: 1 }, 500);
    });
}

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

// Llamar a la animación cuando se muestra la vista de tarjetas
$("#cardViewBtn").on("click", animateCards);

function updateProgressRing(percentage) {
    console.log("Iniciando updateProgressRing con valor:", percentage);

    const circle = document.getElementById("progress-ring-circle");
    const valueDisplay = circle
        ?.closest(".progress-circular")
        ?.querySelector(".progress-value");

    console.log("Elementos encontrados:", {
        circle: !!circle,
        valueDisplay: !!valueDisplay,
    });

    if (!circle || !valueDisplay) {
        console.log("Elementos no encontrados");
        return;
    }

    // Calcular longitud del círculo
    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;

    // Establecer valores iniciales
    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = circumference;
    valueDisplay.textContent = "0%";

    // Forzar reflow
    circle.getBoundingClientRect();

    requestAnimationFrame(() => {
        // Calcular el offset basado en el porcentaje
        const offset = circumference - (percentage / 100) * circumference;

        // Aplicar la transición y el nuevo offset
        circle.style.transition = "stroke-dashoffset 1s ease-in-out";
        circle.style.strokeDashoffset = offset;

        // Animar el número
        $({ val: 0 }).animate(
            { val: percentage },
            {
                duration: 1000,
                step: function (now) {
                    valueDisplay.textContent = `${Math.round(now)}%`;
                },
            }
        );
    });
}

// Función auxiliar para animar valores numéricos
function animateValue(
    selector,
    start,
    end,
    duration,
    prefix = "",
    suffix = "%"
) {
    $({ val: start }).animate(
        { val: end },
        {
            duration: duration,
            step: function (now) {
                $(selector).text(prefix + Math.round(now) + suffix);
            },
        }
    );
}

function cambiarVista(vista) {
    console.log("Cambiando a vista:", vista); // Debug

    if (vista === "tabla") {
        $("#vistaTabla").show();
        $("#vistaTarjetas").hide();
        $("#btnTabla").addClass("active");
        $("#btnTarjetas").removeClass("active");
    } else {
        $("#vistaTabla").hide();
        $("#vistaTarjetas").show();
        $("#btnTabla").removeClass("active");
        $("#btnTarjetas").addClass("active");
    }
}

// Asegúrate de que la tabla se ajuste cuando se muestra
$(document).ready(function () {
    $("#btnTabla, #btnTarjetas").on("click", function () {
        if (dataTable) {
            setTimeout(() => {
                dataTable.columns.adjust();
            }, 0);
        }
    });
});
