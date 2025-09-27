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

let tableGastos;

$(document).ready(function () {
    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tableGastos = $("#gastos_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/api/leyma-creditos/gastos/data",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "concepto", name: "concepto" },
            { data: "categoria_label", name: "categoria" },
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
            { data: "fecha", name: "fecha" },
            { data: "observaciones", name: "observaciones" },
            { data: "created_at", name: "created_at" },
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
                                <a class="dropdown-item btn_editar_gasto" type="button" data-id="${row.id}">Editar</a>
                                <a class="dropdown-item btn_ver_gasto" type="button" data-id="${row.id}">Ver</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger btn_eliminar_gasto" type="button" data-id="${row.id}">Eliminar</a>
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
    // Formulario de gasto
    $("#formGasto").on("submit", function (e) {
        e.preventDefault();
        guardarGasto();
    });

    // Limpiar formulario al cerrar modal
    $("#modalGasto").on("hidden.bs.modal", function () {
        limpiarFormulario();
    });
}

function guardarGasto() {
    const formData = {
        concepto: $("#concepto").val(),
        categoria: $("#categoria").val(),
        monto: $("#monto").val(),
        fecha: $("#fecha").val(),
        observaciones: $("#observaciones").val(),
    };

    const id = $("#formGasto").attr("data-id");
    const url = id
        ? `/api/leyma-creditos/gastos/${id}`
        : "/api/leyma-creditos/gastos";
    const method = id ? "PUT" : "POST";

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
                text: "Gasto guardado correctamente",
                icon: "success",
                confirmButtonText: "OK",
            });
            $("#modalGasto").modal("hide");
            tableGastos.ajax.reload();
        },
        error: function (xhr) {
            let mensaje = "Error al guardar el gasto";
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

// Event listeners para los dropdown items
$(document).on("click", ".btn_editar_gasto", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    editarGasto(id);
});

$(document).on("click", ".btn_ver_gasto", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verGasto(id);
});

$(document).on("click", ".btn_eliminar_gasto", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    eliminarGasto(id);
});

function verGasto(id) {
    $.ajax({
        url: `/api/leyma-creditos/gastos/${id}`,
        type: "GET",
        success: function (response) {
            // Mostrar datos en modal de visualización
            mostrarDetallesGasto(response);
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del gasto",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function editarGasto(id) {
    $.ajax({
        url: `/api/leyma-creditos/gastos/${id}`,
        type: "GET",
        success: function (response) {
            // Llenar formulario con datos existentes
            $("#modalGastoLabel").text("Editar Gasto");
            $("#formGasto").attr("data-id", id);

            $("#concepto").val(response.concepto);
            $("#categoria").val(response.categoria);
            $("#monto").val(response.monto);
            $("#fecha").val(response.fecha);
            $("#observaciones").val(response.observaciones);

            $("#modalGasto").modal("show");
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del gasto",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function eliminarGasto(id) {
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
                url: `/api/leyma-creditos/gastos/${id}`,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        title: "Eliminado",
                        text: "Gasto eliminado correctamente",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                    tableGastos.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Error al eliminar el gasto",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
}

function mostrarDetallesGasto(gasto) {
    // Implementar modal de detalles
    console.log("Mostrar detalles:", gasto);
}

function formatearMoneda(monto) {
    return currency(monto, {
        separator: ".",
        decimal: ",",
        precision: 2,
    }).format();
}

function limpiarFormulario() {
    $("#formGasto")[0].reset();
    $("#modalGastoLabel").text("Nuevo Gasto");
    $("#formGasto").removeAttr("data-id");
}

// Las funciones ahora se manejan a través de event listeners
