import $ from "jquery";
window.$ = $;
window.jQuery = $;

import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";

import "datatables.net-responsive";
import "datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css";
import "datatables.net-responsive-bs5";

import "datatables.net-buttons"; // core buttons
import "datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css";
import "datatables.net-buttons-bs5"; // bootstrap5 buttons integration

import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.colVis.js";

import "jszip";
import "bootstrap-icons/font/bootstrap-icons.css";
import currency from "currency.js";

let tablePrestamos;

$(document).ready(function () {
    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tablePrestamos = $("#prestamos_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/api/leyma-creditos/prestamos-familiares/data",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "familiar_nombre", name: "familiar_nombre" },
            { data: "familiar_dni", name: "familiar_dni" },
            {
                data: "monto",
                name: "monto",
                render: function (data, type, row) {
                    if (type === "display" || type === "type") {
                        return formatearMoneda(data || 0);
                    }
                    return data;
                },
            },
            { data: "fecha_prestamo", name: "fecha_prestamo" },
            { data: "fecha_vencimiento", name: "fecha_vencimiento" },
            { data: "familiar_telefono", name: "familiar_telefono" },
            { data: "estado", name: "estado" },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return `
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Acciones <i class="las la-angle-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item btn_editar_prestamo" type="button" data-id="${row.id}">Editar</a>
                                <a class="dropdown-item btn_ver_prestamo" type="button" data-id="${row.id}">Ver</a>
                                <a class="dropdown-item btn_registrar_pago_prestamo" type="button" data-id="${row.id}">Registrar Pago</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger btn_eliminar_prestamo" type="button" data-id="${row.id}">Eliminar</a>
                            </div>
                        </div>
                    `;
                },
            },
        ],
        language: {
            url: "/js/datatables/i18n/es-ES.json",
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100],
        ],
        dom: "<'row mb-2 g-0 justify-content-between'<'col-md-auto dt-length'f><'col-md-auto dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",
        initComplete: function () {
            // Agregar clase al info (footer con texto "Mostrando...")
            $(this.api().table().container()).find(".dt-info").addClass("my-2");
        },
        buttons: [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: "btn btn-success btn-sm",
            },
            {
                extend: "pdf",
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: "btn btn-danger btn-sm",
            },
            {
                extend: "colvis",
                text: '<i class="fas fa-columns"></i> Columnas',
                className: "btn btn-info btn-sm",
            },
        ],
    });
}

function inicializarEventos() {
    // Formulario de préstamo
    $("#formPrestamo").on("submit", function (e) {
        e.preventDefault();
        guardarPrestamo();
    });

    // Limpiar formulario al cerrar modal
    $("#modalPrestamo").on("hidden.bs.modal", function () {
        limpiarFormulario();
    });
}

// Event listeners para los dropdown items
$(document).on("click", ".btn_editar_prestamo", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    editarPrestamo(id);
});

$(document).on("click", ".btn_ver_prestamo", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verPrestamo(id);
});

$(document).on("click", ".btn_registrar_pago_prestamo", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    registrarPagoPrestamo(id);
});

$(document).on("click", ".btn_eliminar_prestamo", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    eliminarPrestamo(id);
});

function guardarPrestamo() {
    const formData = {
        familiar_nombre: $("#familiar_nombre").val(),
        familiar_dni: $("#familiar_dni").val(),
        familiar_telefono: $("#familiar_telefono").val(),
        monto: $("#monto").val(),
        fecha_prestamo: $("#fecha_prestamo").val(),
        fecha_vencimiento: $("#fecha_vencimiento").val(),
        observaciones: $("#observaciones").val(),
    };

    const id = $("#formPrestamo").attr("data-id");
    const url = id
        ? `/api/leyma-creditos/prestamos-familiares/${id}/update`
        : "/api/leyma-creditos/prestamos-familiares";
    const method = "POST";

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            Swal.fire({
                title: "Éxito",
                text: "Préstamo guardado correctamente",
                icon: "success",
                confirmButtonText: "OK",
            });
            $("#modalPrestamo").modal("hide");
            tablePrestamos.ajax.reload();
        },
        error: function (xhr) {
            let mensaje = "Error al guardar el préstamo";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Error",
                text: mensaje,
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function verPrestamo(id) {
    $.ajax({
        url: `/api/leyma-creditos/prestamos-familiares/${id}`,
        type: "GET",
        success: function (response) {
            // Mostrar datos en modal de visualización
            mostrarDetallesPrestamo(response);
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del préstamo",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function editarPrestamo(id) {
    $.ajax({
        url: `/api/leyma-creditos/prestamos-familiares/${id}`,
        type: "GET",
        success: function (response) {
            // Llenar formulario con datos existentes
            $("#modalPrestamoLabel").text("Editar Préstamo Familiar");
            $("#formPrestamo").attr("data-id", id);

            $("#familiar_nombre").val(response.familiar_nombre);
            $("#familiar_dni").val(response.familiar_dni);
            $("#familiar_telefono").val(response.familiar_telefono);
            $("#monto").val(response.monto);
            $("#fecha_prestamo").val(response.fecha_prestamo);
            $("#fecha_vencimiento").val(response.fecha_vencimiento);
            $("#observaciones").val(response.observaciones);

            $("#modalPrestamo").modal("show");
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del préstamo",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function registrarPagoPrestamo(id) {
    // Implementar modal de registro de pago
    console.log("Registrar pago para préstamo:", id);
}

function eliminarPrestamo(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "No podrás revertir esta acción",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/leyma-creditos/prestamos-familiares/${id}/delete`,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        title: "Eliminado",
                        text: "Préstamo eliminado correctamente",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                    tablePrestamos.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Error al eliminar el préstamo",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
}

function mostrarDetallesPrestamo(prestamo) {
    // Implementar modal de detalles
    console.log("Mostrar detalles:", prestamo);
}

function formatearMoneda(monto) {
    return currency(monto, {
        separator: ".",
        decimal: ",",
        precision: 2,
    }).format();
}

function limpiarFormulario() {
    $("#formPrestamo")[0].reset();
    $("#modalPrestamoLabel").text("Nuevo Préstamo Familiar");
    $("#formPrestamo").removeAttr("data-id");
}

// Funciones globales para uso en HTML
window.verPrestamo = verPrestamo;
window.editarPrestamo = editarPrestamo;
window.registrarPagoPrestamo = registrarPagoPrestamo;
window.eliminarPrestamo = eliminarPrestamo;
