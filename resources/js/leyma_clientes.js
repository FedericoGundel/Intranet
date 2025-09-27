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

import SlimSelect from "slim-select";

let tableClientes;

$(document).ready(function () {
    inicializarTabla();
    inicializarEventos();
    cargarEstadisticas();
});

function inicializarTabla() {
    tableClientes = $("#clientes_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/api/leyma-creditos/clientes/data",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "nombre", name: "nombre" },
            { data: "dni", name: "dni" },
            { data: "telefono", name: "telefono" },
            { data: "domicilio", name: "domicilio" },
            { data: "comercio_negocio", name: "comercio_negocio" },
            { data: "garante_nombre", name: "garante_nombre" },
            {
                data: "creditos_activos",
                name: "creditos_activos",
                render: function (data, type, row) {
                    return data || 0;
                }
            },
            {
                data: "permite_creditos_multiples",
                name: "permite_creditos_multiples",
                render: function (data, type, row) {
                    return data ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
                }
            },
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
                                <a class="dropdown-item btn_editar_cliente" type="button" data-id="${row.id}">Editar</a>
                                <a class="dropdown-item btn_ver_cliente" type="button" data-id="${row.id}">Ver</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger btn_eliminar_cliente" type="button" data-id="${row.id}">Eliminar</a>
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
    // Formulario de cliente
    $("#formCliente").on("submit", function (e) {
        e.preventDefault();
        guardarCliente();
    });

    // Limpiar formulario al cerrar modal
    $("#modalCliente").on("hidden.bs.modal", function () {
        limpiarFormulario();
    });
}

function guardarCliente() {
    const id = $("#formCliente").attr("data-id");
    const url = id
        ? `/api/leyma-creditos/clientes/${id}`
        : "/api/leyma-creditos/clientes";
    const method = id ? "PUT" : "POST";

    // Recopilar datos del formulario
    const formData = {
        nombre: $("#nombre").val(),
        dni: $("#dni").val(),
        telefono: $("#telefono").val(),
        domicilio: $("#domicilio").val(),
        comercio_negocio: $("#comercio_negocio").val(),
        email: $("#email").val(),
        estado: $("#estado").val(),
        garante_nombre: $("#garante_nombre").val(),
        garante_dni: $("#garante_dni").val(),
        garante_telefono: $("#garante_telefono").val(),
        garante_domicilio: $("#garante_domicilio").val(),
        observaciones: $("#observaciones").val(),
        _token: $('meta[name="csrf-token"]').attr("content"),
    };

    // Para PUT requests, agregar _method
    if (id) {
        formData._method = "PUT";
    }

    // Debug: mostrar datos del formulario
    console.log("ID del cliente:", id);
    console.log("URL:", url);
    console.log("Método:", method);
    console.log("Datos del formulario:", formData);

    $.ajax({
        url: url,
        type: "POST", // Siempre POST para Laravel
        data: formData,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            Swal.fire({
                title: "Éxito",
                text: id
                    ? "Cliente actualizado correctamente"
                    : "Cliente guardado correctamente",
                icon: "success",
                confirmButtonText: "OK",
            });
            $("#modalCliente").modal("hide");
            tableClientes.ajax.reload();
        },
        error: function (xhr) {
            console.error("Error en la petición:", xhr);
            console.error("Response:", xhr.responseJSON);

            let mensaje = id
                ? "Error al actualizar el cliente"
                : "Error al guardar el cliente";
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
$(document).on("click", ".btn_editar_cliente", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    editarCliente(id);
});

$(document).on("click", ".btn_ver_cliente", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verCliente(id);
});

$(document).on("click", ".btn_eliminar_cliente", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    eliminarCliente(id);
});

function verCliente(id) {
    $.ajax({
        url: `/api/leyma-creditos/clientes/${id}`,
        type: "GET",
        success: function (response) {
            // Mostrar datos en modal de visualización
            mostrarDetallesCliente(response);
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del cliente",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function editarCliente(id) {
    $.ajax({
        url: `/api/leyma-creditos/clientes/${id}`,
        type: "GET",
        success: function (response) {
            console.log("Datos del cliente recibidos:", response);

            // Llenar formulario con datos existentes
            $("#modalClienteLabel").text("Editar Cliente");
            $("#formCliente").attr("data-id", id);

            $("#nombre").val(response.nombre || "");
            $("#dni").val(response.dni || "");
            $("#telefono").val(response.telefono || "");
            $("#domicilio").val(response.domicilio || "");
            $("#comercio_negocio").val(response.comercio_negocio || "");
            $("#email").val(response.email || "");
            $("#estado").val(response.estado || "activo");
            $("#garante_nombre").val(response.garante_nombre || "");
            $("#garante_dni").val(response.garante_dni || "");
            $("#garante_telefono").val(response.garante_telefono || "");
            $("#garante_domicilio").val(response.garante_domicilio || "");
            $("#observaciones").val(response.observaciones || "");

            console.log("Formulario llenado. Nombre:", $("#nombre").val());
            $("#modalCliente").modal("show");
        },
        error: function (xhr) {
            console.error("Error al cargar cliente:", xhr);
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del cliente",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function eliminarCliente(id) {
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
                url: `/api/leyma-creditos/clientes/${id}`,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        title: "Eliminado",
                        text: "Cliente eliminado correctamente",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                    tableClientes.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Error al eliminar el cliente",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
}

function mostrarDetallesCliente(cliente) {
    // Implementar modal de detalles
    console.log("Mostrar detalles:", cliente);
}

function limpiarFormulario() {
    $("#formCliente")[0].reset();
    $("#modalClienteLabel").text("Nuevo Cliente");
    $("#formCliente").removeAttr("data-id");
}

function cargarEstadisticas() {
    fetch("/api/leyma-creditos/clientes/estadisticas")
        .then((response) => response.json())
        .then((data) => {
            // Actualizar las estadísticas en las cards
            $("#clientes_totales").text(data.clientes_totales || 0);
            $("#clientes_activos").text(data.clientes_activos || 0);
            $("#clientes_con_garante").text(data.clientes_con_garante || 0);
            $("#clientes_con_creditos").text(data.clientes_con_creditos || 0);
        })
        .catch((error) => {
            console.error("Error al cargar estadísticas:", error);
            // Mostrar valores por defecto en caso de error
            $("#clientes_totales").text("0");
            $("#clientes_activos").text("0");
            $("#clientes_con_garante").text("0");
            $("#clientes_con_creditos").text("0");
        });
}

function formatearMoneda(monto) {
    return currency(monto, {
        separator: ".",
        decimal: ",",
        precision: 2,
    }).format();
}

// Las funciones ahora se manejan a través de event listeners
