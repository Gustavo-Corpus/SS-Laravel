$(document).ready(function () {
    // Configuración inicial de AJAX
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Gráfico de Tickets por Estado
    function initTicketsEstadoChart() {
        $.get(
            "/SS-Laravel/public/tickets/estadisticas/estado",
            function (data) {
                const ctx = document
                    .getElementById("ticketsEstadoChart")
                    .getContext("2d");
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: data.map((item) => item.estado.toUpperCase()),
                        datasets: [
                            {
                                data: data.map((item) => item.total),
                                backgroundColor: [
                                    "#dc3545", // Abierto
                                    "#ffc107", // En proceso
                                    "#28a745", // Resuelto
                                    "#6c757d", // Cerrado
                                ],
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                });
            }
        );
    }

    // Gráfico de Tickets por Prioridad
    function initTicketsPrioridadChart() {
        $.get(
            "/SS-Laravel/public/tickets/estadisticas/prioridad",
            function (data) {
                const ctx = document
                    .getElementById("ticketsPrioridadChart")
                    .getContext("2d");
                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: data.map((item) =>
                            item.prioridad.toUpperCase()
                        ),
                        datasets: [
                            {
                                label: "Cantidad de Tickets",
                                data: data.map((item) => item.total),
                                backgroundColor: [
                                    "#17a2b8", // Baja
                                    "#ffc107", // Media
                                    "#dc3545", // Alta
                                ],
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            },
                        },
                    },
                });
            }
        );
    }

    // Inicializar las gráficas cuando el documento esté listo
    $(document).ready(function () {
        if (
            $("#ticketsEstadoChart").length &&
            $("#ticketsPrioridadChart").length
        ) {
            initTicketsEstadoChart();
            initTicketsPrioridadChart();
        }
    });
});
