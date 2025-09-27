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
$("#cobros_table").DataTable({
    ajax: {
        url: "/cobros/data",
        dataSrc: function (json) {
            const rows = Array.isArray(json.data) ? json.data : [];

            let pendientes = 0;
            let parciales = 0;
            let pagadas = 0;

            const eps = 0.005;

            rows.forEach((r) => {
                const pagado = parseFloat(r.pagado) || 0;
                const total = parseFloat(r.total) || 0;

                if (pagado <= eps) {
                    pendientes++;
                } else if (pagado + eps < total) {
                    parciales++;
                } else {
                    pagadas++;
                }
            });

            // 👇 Pinta las métricas en las cards
            document.querySelector("#facturas_pendientes").textContent =
                pendientes;
            document.querySelector("#facturas_parciales").textContent =
                parciales;
            document.querySelector("#facturas_pagadas").textContent = pagadas;

            return rows;
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
        { data: "numero" },
        { data: "cliente" },
        { data: "fecha" },
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
            data: "pagado",
            render: function (data) {
                return currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
        },
        {
            data: "saldo",
            render: function (data) {
                return currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
        },

        // 👇 NUEVA COLUMNA ESTADO
        {
            data: null,
            title: "Estado",
            className: "text-center",
            render: function (row) {
                const pagado = parseFloat(row.pagado) || 0;
                const total = parseFloat(row.total) || 0;

                // tolerancia por redondeo a 2 decimales
                const eps = 0.005;

                if (pagado <= eps) {
                    // Nada pagado
                    return '<span class="badge rounded bg-danger bg-danger-subtle text-dark">Pendiente</span>';
                } else if (pagado + eps < total) {
                    // Parcial
                    return '<span class="badge rounded bg-warning bg-warning-subtle text-dark">Parcial</span>';
                } else {
                    // Total
                    return '<span class="badge rounded bg-success bg-success-subtle text-dark">Pagada</span>';
                }
            },
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
              <a class="dropdown-item btn_ver_pagos_factura" data-id="${row.id}" type="button">Pagos</a>
             
              <a class="dropdown-item text-danger btn_eliminar_factura" href="#" data-id="${row.id}">Eliminar</a>
            </div>
          </div>
        `;
            },
        },
    ],
    language: { url: "/js/datatables/i18n/es-ES.json" },
    responsive: true,
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});

$("#pagos_factura_table").DataTable({
    ajax: "/pagos",
    dataSrc: function (json) {
        console.log(json);
        return json.data;
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
            data: "fecha",
        },
        {
            data: "monto",
            render: function (data) {
                return currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
        },
        {
            data: "metodo",
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
                          
                            <a class="dropdown-item btn_editar_pago_factura" data-id="${row.id}">Editar</a>
                            <a class="dropdown-item text-danger btn_eliminar_pago_factura" href="#" data-id="${row.id}">Eliminar</a>
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

document
    .getElementById("formAgregarPago")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        // limpiar errores previos
        [...form.elements].forEach((el) => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach((el) => el.remove());

        const formData = new FormData(form);
        formData.append("factura_id", $("#pago_factura_id").val());

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const resp = await fetch("/pagos", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await resp.json();

            if (resp.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Pago registrado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                // cerrar modal + reset
                const modalEl = document.getElementById("modalAgregarPago");
                const modal =
                    bootstrap.Modal.getInstance(modalEl) ||
                    bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
                form.reset();

                $("#pagos_factura_table").DataTable().ajax.reload(null, false);
                $("#cobros_table").DataTable().ajax.reload(null, false);
            } else {
                Swal.close();

                // Manejo de validaciones (422)
                if (resp.status === 422 && data?.errors) {
                    for (const name in data.errors) {
                        const input = form.querySelector(`[name="${name}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            const fb = document.createElement("div");
                            fb.className = "invalid-feedback";
                            fb.textContent = data.errors[name].join(", ");
                            // Si el input ya tiene feedback previo, lo reemplazamos
                            const next = input.nextElementSibling;
                            if (
                                next &&
                                next.classList.contains("invalid-feedback")
                            )
                                next.remove();
                            input.insertAdjacentElement("afterend", fb);
                        }
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data?.message || "No se pudo registrar el pago.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#dc3545",
                    });
                }
            }
        } catch (err) {
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });

$(document).on("click", "#btn_agregar_pago", function () {
    let id = $(this).data("id");

    // Mostrar el modal
    $("#modalAgregarPago").modal("show");
});

$(document).on("click", ".btn_ver_pagos_factura", function () {
    const id = $(this).data("id");

    // seteo hidden del form de "Agregar pago"
    $("#pago_factura_id").val(id);

    // armo la nueva URL con el filtro
    const nuevaUrl = `/facturas/${id}/pagos`;
    // obtengo la instancia del DataTable y cambio la url de ajax
    const tabla = $("#pagos_factura_table").DataTable();
    tabla.ajax.url(nuevaUrl).load(null, false); // false = no resetea paginación

    // muestro el modal
    $("#verPagosFactura").modal("show");
});
// Eliminar pago (soft delete). Si el botón trae data-force="1", borra definitivo (?force=1)
$(document).on("click", ".btn_eliminar_pago_factura", function (e) {
    e.preventDefault();

    const id = $(this).data("id");
    const force = $(this).data("force") ? true : false; // opcional
    if (!id) return;

    Swal.fire({
        title: "¿Estás seguro?",
        text: force
            ? "Esto eliminará el pago de forma DEFINITIVA."
            : "Esta acción anulará el pago (podés restaurarlo si implementaste esa opción).",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: force ? "Sí, eliminar definitivo" : "Sí, anular",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        const url = force ? `/pagos/${id}?force=1` : `/pagos/${id}`;

        fetch(url, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
            },
        })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(
                        data?.message || "No se pudo eliminar el pago."
                    );
                }
                Swal.fire(
                    "Listo",
                    data?.message ||
                        (force
                            ? "Pago eliminado definitivamente."
                            : "Pago anulado."),
                    "success"
                );

                $("#pagos_factura_table").DataTable().ajax.reload(null, false);
                $("#cobros_table").DataTable().ajax.reload(null, false);
            })
            .catch((err) => {
                Swal.fire(
                    "Error",
                    err.message || "No se pudo eliminar el pago.",
                    "error"
                );
                console.error(err);
            });
    });
});

// Abrir modal de edición (desde botón: <a class="dropdown-item btn_editar_pago_factura" data-id="...">Editar</a>)
$(document).on("click", ".btn_editar_pago_factura", function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    if (!id) return;

    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.get(`/pagos/${id}?_=${Date.now()}`)
        .done(function (pago) {
            // Rellenar campos
            $("#edit_pago_id").val(pago.id);
            $("#edit_pago_factura_id").val(pago.factura_id);

            // Fecha en formato YYYY-MM-DD (si viene con hora, cortamos)
            const fecha = (pago.fecha || "").toString().substring(0, 10);
            $("#edit_pago_fecha").val(fecha);

            $("#edit_pago_monto").val(pago.monto);
            $("#edit_pago_metodo").val(pago.metodo ?? "");
            $("#edit_pago_referencia").val(pago.referencia ?? "");
            $("#edit_pago_notas").val(pago.notas ?? "");
            const miSelect = new SlimSelect({
                select: "#select_cobrador_pago_edit",
            });
            // Empleado (si usás SlimSelect, sincronizalo)
            if (pago.cobrador_id) {
                // Después en cualquier parte:
                miSelect.setSelected(pago.cobrador_id);
            } else {
                miSelect.setSelected("");
            }
            // limpiar errores previos
            const form = document.getElementById("formEditarPago");
            [...form.elements].forEach((el) =>
                el.classList.remove("is-invalid")
            );
            $("#formEditarPago .invalid-feedback").remove();

            Swal.close();
            const modal = bootstrap.Modal.getOrCreateInstance(
                document.getElementById("modalEditarPago")
            );
            modal.show();
        })
        .fail(function () {
            Swal.close();
            Swal.fire({
                title: "Error",
                text: "No se pudo obtener la información del pago.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        });
});

// Guardar cambios (PUT /pagos/{id})
document
    .getElementById("formEditarPago")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;

        // limpiar errores previos
        [...form.elements].forEach((el) => el.classList.remove("is-invalid"));
        $("#formEditarPago .invalid-feedback").remove();

        const id = document.getElementById("edit_pago_id").value;
        if (!id) return;

        const formData = new FormData(form);

        try {
            Swal.fire({
                title: "Guardando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const resp = await fetch(`/pagos/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await resp.json().catch(() => ({}));

            if (resp.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Pago actualizado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                const modalEl = document.getElementById("modalEditarPago");
                (
                    bootstrap.Modal.getInstance(modalEl) ||
                    bootstrap.Modal.getOrCreateInstance(modalEl)
                ).hide();

                // Recargar tablas si existen
                $("#pagos_factura_table").DataTable().ajax.reload(null, false);
                $("#cobros_table").DataTable().ajax.reload(null, false);
            } else {
                Swal.close();

                // Validaciones 422
                if (resp.status === 422 && data?.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            const fb = document.createElement("div");
                            fb.className = "invalid-feedback";
                            fb.textContent = data.errors[key].join(", ");
                            input.insertAdjacentElement("afterend", fb);
                        }
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data?.message || "No se pudo actualizar el pago.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#dc3545",
                    });
                }
            }
        } catch (err) {
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });
