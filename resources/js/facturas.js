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
// ✅ Y su CSS
import "bootstrap-icons/font/bootstrap-icons.css";

import SlimSelect from "slim-select";
import currency from "currency.js";
$("#facturas_table").DataTable({
    ajax: {
        url: "/facturas/data",
        dataSrc: function (json) {
            console.log(json);

            const rows = Array.isArray(json.data) ? json.data : [];

            const total = rows.length;
            document.querySelector("#facturas_totales").textContent = total;
            return json.data;
        },
    },
    buttons: [
        {
            extend: "colvis",
            text: "Visibilidad",
            className: "btn btn-sm",
        },
        "excelHtml5",
    ],
    dom: "<'row mb-2 g-0 justify-content-between'<'col-md-auto dt-length'f><'col-md-auto dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",

    initComplete: function () {
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },

    columns: [
        {
            data: "numero",
        },
        {
            data: "nombre",
        },
        {
            data: "fecha",
        },
        {
            data: "fecha_vencimiento",
        },
        {
            data: "total",
            render: function (data) {
                return currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
            className: "text-end",
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: function (data, type, row) {
                return `
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Acciones <i class="las la-angle-down ms-1"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn_ver_factura" data-id="${row.id}" type="button">Ver</a>
                           
                            <a class="dropdown-item text-danger btn_eliminar_factura" href="#" data-id="${row.id}">Eliminar</a>
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
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});

$(document).on("click", ".btn_ver_factura", function () {
    let id = $(this).data("id");
    let url = `/facturas/${id}/ver`; // Asegurate que la ruta coincida con tu route()

    // Setear la URL del iframe
    $("#iframeFactura").attr("src", url);

    // Mostrar el modal
    $("#modalVerFactura").modal("show");
});
$(document).on("click", ".btn_eliminar_factura", function () {
    var id = $(this).attr("data-id");

    if (!id) return false;

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta factura se eliminará permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/facturas/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    Swal.fire("Eliminada", data.message, "success");
                    // Recargar la tabla si existe
                    if ($("#facturas_table").length) {
                        $("#facturas_table").DataTable().ajax.reload();
                    }
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar la factura.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});
