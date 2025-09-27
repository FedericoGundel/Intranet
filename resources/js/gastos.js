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
$("#gastos_table").DataTable({
    ajax: {
        url: "/gastos/data",
        dataSrc: function (json) {
            console.log(json);
            let gastos_manuales = 0;
            let pago_nominas = 0;
            let total_gastado = 0;

            if (Array.isArray(json.data)) {
                json.data.forEach((gasto) => {
                    const monto = parseFloat(gasto.monto) || 0;

                    if (gasto.nomina_pago_id) {
                        pago_nominas += monto;
                    } else {
                        gastos_manuales += monto;
                    }
                    total_gastado += monto;
                });
            }

            // 🔹 Mostralo en el DOM (donde quieras)
            $("#gastos_manuales").text(
                currency(gastos_manuales, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format()
            );
            $("#pago_nominas").text(
                currency(pago_nominas, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format()
            );
            $("#total_gastado").text(
                currency(total_gastado, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format()
            );

            return json.data; // devolvés la data a la tabla
        },
    },
    buttons: [
        {
            extend: "colvis",
            text: "Visibilidad",
            className: "btn btn-sm btn-secondary",
        },
        "excelHtml5",
    ],
    dom: "<'row mb-2 g-0 justify-content-between'<'col-md-auto dt-length'f><'col-md-auto dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",
    initComplete: function () {
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },
    columns: [
        { data: "id" },
        { data: "descripcion" },
        {
            data: "fecha",
            render: (d) => (d ? String(d).slice(0, 10) : "-"),
        },
        {
            data: "monto",
            render: (data) =>
                currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
            className: "text-end",
        },
        {
            // 🔹 Ahora usamos nomina_pago_id en vez de relacionable
            data: "nomina_pago_id",
            render: (data, type, row) => {
                if (data) {
                    return `Pago Nómina #${data}`;
                }
                return "Manual";
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: (data, type, row) => `
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Acciones <i class="las la-angle-down ms-1"></i>
                    </button>
                    <div class="dropdown-menu">
                      
                        <a class="dropdown-item btn_editar_gasto"type="button" data-id="${row.id}">Editar</a>
                        <a class="dropdown-item text-danger btn_eliminar_gasto" type="button" data-id="${row.id}">Eliminar</a>
                    </div>
                </div>
            `,
        },
    ],
    language: { url: "/js/datatables/i18n/es-ES.json" },
    responsive: true,
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});

document
    .getElementById("formAddGasto")
    ?.addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const resp = await fetch("/gastos", {
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
            Swal.close();

            if (resp.ok) {
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: "Gasto agregado correctamente",
                });
                form.reset();

                // cerrar modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("addGasto")
                );
                modal?.hide();

                // recargar tabla
                $("#gastos_table").DataTable().ajax.reload(null, false);
            } else {
                // limpiar errores previos
                [...form.elements].forEach((el) => {
                    el.classList?.remove("is-invalid");
                    if (
                        el.nextSibling &&
                        el.nextSibling.classList?.contains("invalid-feedback")
                    ) {
                        el.nextSibling.remove();
                    }
                });

                if (data?.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            const fb = document.createElement("div");
                            fb.className = "invalid-feedback";
                            fb.innerText = data.errors[key].join(", ");
                            input.after(fb);
                        }
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data?.message || "No se pudo crear el gasto.",
                    });
                }
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un problema al procesar la solicitud.",
            });
        }
    });

// ======== ABRIR MODAL DE EDICIÓN ========
$(document).on("click", ".btn_editar_gasto", function () {
    const id = $(this).data("id");
    if (!id) return;

    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.get(`/gastos/${id}?_=${Date.now()}`, function (gasto) {
        const $form = $("#formEditGasto");
        $form[0].reset();
        $("#edit_id_gasto").val(gasto.id);

        // Seteo campos simples
        $form
            .find("[name='fecha']")
            .val(gasto.fecha ? String(gasto.fecha).slice(0, 10) : "");
        $form.find("[name='categoria']").val(gasto.categoria ?? "");
        $form.find("[name='subcategoria']").val(gasto.subcategoria ?? "");
        $form.find("[name='monto']").val(gasto.monto ?? "");
        $form.find("[name='metodo_pago']").val(gasto.metodo_pago ?? "");
        $form.find("[name='descripcion']").val(gasto.descripcion ?? "");
        const miSelect = new SlimSelect({
            select: "#select_empleados_gasto_editar",
        });
        // Empleado (si usás SlimSelect, sincronizalo)
        if (gasto.empleado_id) {
            // Después en cualquier parte:
            miSelect.setSelected(gasto.empleado_id);
        } else {
            miSelect.setSelected("");
        }

        // Comprobante actual
        if (gasto.comprobante_path) {
            $("#comprobante_actual_editar").show();
            $("#link_comprobante_actual").attr(
                "href",
                `/storage/${gasto.comprobante_path}`
            );
        } else {
            $("#comprobante_actual_editar").hide();
            $("#link_comprobante_actual").attr("href", "#");
        }

        Swal.close();

        const modalEl = document.getElementById("editGasto");
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }).fail(() => {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo obtener el gasto.",
        });
    });
});

// ======== ACTUALIZAR GASTO ========
document
    .getElementById("formEditGasto")
    ?.addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = document.getElementById("edit_id_gasto").value;
        const formData = new FormData(form);
        formData.append("_method", "PUT"); // simulamos PUT

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const resp = await fetch(`/gastos/${id}`, {
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
            Swal.close();

            if (resp.ok) {
                Swal.fire({
                    icon: "success",
                    title: "¡Actualizado!",
                    text: "Gasto actualizado correctamente.",
                });

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editGasto")
                );
                modal?.hide();

                $("#gastos_table").DataTable().ajax.reload(null, false);
            } else {
                // limpiar errores previos
                [...form.elements].forEach((el) => {
                    el.classList?.remove("is-invalid");
                    if (
                        el.nextSibling &&
                        el.nextSibling.classList?.contains("invalid-feedback")
                    ) {
                        el.nextSibling.remove();
                    }
                });

                if (data?.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            const fb = document.createElement("div");
                            fb.className = "invalid-feedback";
                            fb.innerText = data.errors[key].join(", ");
                            input.after(fb);
                        }
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            data?.message || "No se pudo actualizar el gasto.",
                    });
                }
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un problema al actualizar el gasto.",
            });
        }
    });
$(document).on("click", ".btn_eliminar_gasto", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Este gasto será eliminado y no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/gastos/${id}`, {
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
                    Swal.fire("Eliminado", data.message, "success");

                    // Si tenés DataTable para listar gastos:
                    $("#gastos_table").DataTable().ajax.reload(null, false);

                    // Opcional: refrescar otros listados si corresponde
                    // actualizarResumenCaja();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar el gasto.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});
