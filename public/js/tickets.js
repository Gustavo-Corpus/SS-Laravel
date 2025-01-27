// Configuración inicial de AJAX
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

let editMode = false;

function toggleEdit() {
    editMode = !editMode;
    if (editMode) {
        $("#viewDetails").hide();
        $("#editForm").show();
        $(".btn-primary").hide();
        $("#btnGuardar").show();
    } else {
        $("#viewDetails").show();
        $("#editForm").hide();
        $(".btn-primary").show();
        $("#btnGuardar").hide();
    }
}

function guardarCambios(ticketId) {
    const formData = new FormData($("#ticketEditForm")[0]);
    formData.append("_method", "PUT");

    $.ajax({
        url: `/SS-Laravel/public/tickets/${ticketId}`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $("#modalDetallesTicket").modal("hide");
            alert("Ticket actualizado exitosamente");
            location.reload();
        },
        error: function (xhr) {
            alert("Error al actualizar el ticket");
        },
    });
}

function eliminarTicket(ticketId) {
    if (confirm("¿Estás seguro de que deseas eliminar este ticket?")) {
        $.ajax({
            url: `/SS-Laravel/public/tickets/${ticketId}`,
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#modalDetallesTicket").modal("hide");
                alert("Ticket eliminado exitosamente");
                location.reload();
            },
            error: function (xhr) {
                alert("Error al eliminar el ticket");
            },
        });
    }
}

function verTicketDesdeNotificacion(ticketId, notificationId) {
    // Marcar la notificación como leída primero
    $.ajax({
        url: "/SS-Laravel/public/notifications/mark-as-read",
        method: "POST",
        data: { id: notificationId },
        success: function () {
            // Después de marcar como leída, cargar el ticket
            $.ajax({
                url: `/SS-Laravel/public/tickets/${ticketId}`,
                method: "GET",
                success: function (response) {
                    $("#modalDetallesTicket .modal-content").html(response);
                    $("#modalDetallesTicket").modal("show");
                    // Actualizar solo el menú de notificaciones
                    location.reload();
                },
                error: function (xhr) {
                    console.error("Error al cargar ticket:", xhr);
                    alert("Error al cargar los detalles del ticket");
                },
            });
        },
    });
}

function marcarEnProceso(ticketId) {
    const comentario = $("#comentario_ticket").val();

    $.ajax({
        url: `/SS-Laravel/public/tickets/${ticketId}`,
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            _method: "PUT",
            estado: "en_proceso",
            comentario: comentario,
        },
        success: function (response) {
            $("#modalDetallesTicket").modal("hide");
            alert("Ticket marcado en proceso");
            window.location.reload();
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Error al actualizar el estado del ticket");
        },
    });
}

function marcarResuelto(ticketId) {
    const comentario = $("#comentario_ticket").val();

    $.ajax({
        url: `/SS-Laravel/public/tickets/${ticketId}`,
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            _method: "PUT",
            estado: "resuelto",
            comentario: comentario,
        },
        success: function (response) {
            $("#modalDetallesTicket").modal("hide");
            alert("Ticket marcado como resuelto");
            window.location.reload();
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Error al actualizar el estado del ticket");
        },
    });
}

// Función para marcar notificación como leída
function marcarNotificacionLeida(notificationId) {
    $.post("/SS-Laravel/public/notifications/mark-as-read", {
        id: notificationId,
    });
}

// Función para ver detalles del ticket
function verTicket(id) {
    $.ajax({
        url: "/SS-Laravel/public/tickets/" + id,
        method: "GET",
        headers: {
            Accept: "text/html",
        },
        success: function (response) {
            $("#modalDetallesTicket .modal-content").html(response);
            $("#modalDetallesTicket").modal("show");
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los detalles:", xhr.responseText);
            alert("Error al cargar los detalles del ticket");
        },
    });
}

// Funciones para el manejo de tickets
function mostrarFormularioTicket() {
    $("#formCrearTicket")[0].reset();
    $("#modalCrearTicket").modal("show");
}

function guardarTicket() {
    const formData = new FormData($("#formCrearTicket")[0]);

    console.log("Enviando datos:", Object.fromEntries(formData));

    $.ajax({
        url: BASE_URL + "/tickets",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log("Respuesta exitosa:", response);
            $("#modalCrearTicket").modal("hide");
            Swal.fire({
                title: "¡Éxito!",
                text: "Ticket creado correctamente",
                icon: "success",
            }).then(() => {
                window.location.reload();
            });
        },
        error: function (xhr, status, error) {
            console.error("Error:", { xhr, status, error });
            Swal.fire({
                title: "Error",
                text:
                    "Hubo un problema al crear el ticket: " +
                    (xhr.responseJSON?.message || error),
                icon: "error",
            });
        },
    });
}

function actualizarEstadoTicket(id, estado) {
    $.ajax({
        url: `/tickets/${id}`,
        method: "PUT",
        data: { estado: estado },
        success: function (response) {
            Swal.fire({
                title: "¡Actualizado!",
                text: "Estado del ticket actualizado correctamente",
                icon: "success",
            }).then(() => {
                $("#modalDetallesTicket").modal("hide");
                window.location.reload();
            });
        },
    });
}

function calificarTicket(id) {
    $("#ticketId").val(id);
    $("#formCalificarTicket")[0].reset();
    $("#modalCalificarTicket").modal("show");
}

function enviarCalificacion(ticketId) {
    const rating = $('input[name="rating"]:checked').val();
    const comentario = $("#comentario_calificacion").val();

    if (!rating) {
        alert("Por favor, seleccione una calificación");
        return;
    }

    $.ajax({
        url: `/SS-Laravel/public/tickets/${ticketId}/calificar`,
        method: "POST",
        data: {
            rating: rating,
            comentario: comentario,
        },
        success: function (response) {
            $("#modalDetallesTicket").modal("hide");
            alert("¡Gracias por tu calificación!");
            location.reload();
        },
        error: function (xhr) {
            alert("Error al enviar la calificación");
        },
    });
}

// Manejo de estrellas en la calificación
$(".rating input").on("change", function () {
    const rating = $(this).val();
    $(".rating label i").removeClass("text-warning");
    $(this).prevAll("label").find("i").addClass("text-warning");
    $(this).next("label").find("i").addClass("text-warning");
});
