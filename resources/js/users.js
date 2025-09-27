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
function toBool(v) {
    // soporta true/false, 1/0, "1"/"0", "true"/"false"
    if (typeof v === "boolean") return v;
    if (typeof v === "number") return v === 1;
    if (typeof v === "string") return v === "1" || v.toLowerCase() === "true";
    return false;
}

$("#usuarios_table").DataTable({
    ajax: {
        url: "/users/data",
        dataSrc: function (json) {
            const rows = Array.isArray(json.data) ? json.data : [];

            const total = rows.length;
            const aprobados = rows.filter((r) => toBool(r.approved)).length;
            const pendientes = total - aprobados;

            // pintar cards
            document.querySelector("#card_total").textContent = total;
            document.querySelector("#card_aprobados").textContent = aprobados;
            document.querySelector("#card_pendientes").textContent = pendientes;

            return rows; // devolver filas a DataTables
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
            data: "name",
            render: function (data, type, row) {
                return `
<div class="d-flex align-items-center">
                                                        <img src="${
                                                            row.imagen != null
                                                                ? "/storage/" +
                                                                  row.imagen
                                                                : "/storage/defaults/default_user.png"
                                                        }" class="me-2 thumb-md align-self-center rounded" alt="...">
                                                        <div class="flex-grow-1 text-truncate"> 
                                                            <h6 class="m-0">${
                                                                row.name
                                                            }</h6>
                                                            <p class="fs-12 text-muted mb-0">${
                                                                row.email
                                                            }</p>                                                                                           
                                                        </div><!--end media body-->
                                                    </div>


                    
                `;
            },
        },
        {
            data: "email",
        },
        {
            data: "approved",
            render: function (data) {
                return data
                    ? '<span class="badge rounded bg-success bg-success-subtle text-success">Sí</span>'
                    : '<span class="badge rounded bg-warning bg-warning-subtle text-dark">No</span>';
            },
            className: "text-center",
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: function (data, type, row) {
                const accionesHtml = `
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Acciones <i class="las la-angle-down ms-1"></i>
                    </button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item  btn_rol_usuario" href="#" data-id="${
                        row.id
                    }">Rol</a>
                        ${
                            row.approved
                                ? `<a class="dropdown-item text-danger btn_rechazar_usuario" href="#" data-id="${row.id}">Rechazar</a>`
                                : `<a class="dropdown-item btn_aprobar_usuario" href="#" data-id="${row.id}">Aprobar</a>`
                        }
                        <a class="dropdown-item text-danger btn_eliminar_usuario" href="#" data-id="${
                            row.id
                        }">Eliminar</a>
                    </div>
                </div>
                `;
                return accionesHtml;
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
// Aprobar usuario
// Aprobar usuario
$(document).on("click", ".btn_aprobar_usuario", function (e) {
    e.preventDefault();
    const id = $(this).data("id");

    fetch(`/users/${id}/approve`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
            "Content-Type": "application/json",
        },
    })
        .then((res) => {
            if (!res.ok) throw new Error("Error al aprobar");
            return res.json();
        })
        .then(() => {
            Swal.fire({
                title: "¡Aprobado!",
                text: "El usuario fue aprobado correctamente.",
                icon: "success",
                confirmButtonText: "OK",
                confirmButtonColor: "#28a745",
            });
            $("#usuarios_table").DataTable().ajax.reload(null, false);
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                title: "Error",
                text: "Ocurrió un error al aprobar el usuario.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        });
});

// Rechazar usuario
$(document).on("click", ".btn_rechazar_usuario", function (e) {
    e.preventDefault();
    const id = $(this).data("id");

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción denegará el ingreso al usuario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${id}/reject`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            })
                .then((res) => {
                    if (!res.ok) throw new Error("Error al rechazar");
                    return res.json();
                })
                .then(() => {
                    Swal.fire({
                        title: "¡Rechazado!",
                        text: "El usuario fue rechazado correctamente.",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#28a745",
                    });
                    $("#usuarios_table").DataTable().ajax.reload(null, false);
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire({
                        title: "Error",
                        text: "Ocurrió un error al rechazar el usuario.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#dc3545",
                    });
                });
        }
    });
});

$(document).on("click", ".btn_eliminar_usuario", function (e) {
    e.preventDefault();
    const id = $(this).data("id");

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará permanentemente al usuario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            })
                .then((res) => {
                    if (!res.ok) throw new Error("Error al eliminar");
                    return res.json();
                })
                .then(() => {
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "El usuario fue eliminado correctamente.",
                        icon: "success",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#28a745",
                    });
                    $("#usuarios_table").DataTable().ajax.reload(null, false);
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire({
                        title: "Error",
                        text: "Ocurrió un error al eliminar el usuario.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#dc3545",
                    });
                });
        }
    });
});
