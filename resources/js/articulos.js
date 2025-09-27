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
const slimInstances = new Map();
function InicializarSelectArticulos() {
    fetch("/articulos/data-tom")
        .then((response) => response.json())
        .then((articulos) => {
            const selects = document.querySelectorAll(".select_articulos");

            selects.forEach((select) => {
                // Convertimos los datos para Slim Select
                const options = articulos.map((articulo) => ({
                    text: articulo.text,
                    value: articulo.value,
                }));
                options.unshift({ text: "Ninguno", value: "" });

                // Creamos instancia de Slim Select
                const slim = new SlimSelect({
                    select: select,
                    data: options,
                    settings: {
                        showSearch: true, // used in example
                        focusSearch: false, // used in example

                        searchHighlight: true, // used in example
                        placeholder: "Buscar articulo...",
                        searchText: "No se encontraron resultados",
                        searchPlaceholder: "Buscar",
                    },

                    dropdownParent: select.closest(".modal") || document.body,
                    events: {
                        afterChange: (newVal) => {
                            const selectId = select.id;
                            const botones = document.querySelectorAll(
                                `[data-source="${selectId}"]`
                            );
                            botones.forEach((boton) => {
                                boton.setAttribute(
                                    "data-id",
                                    newVal[0]?.value || ""
                                );
                            });
                        },
                    },
                });

                const initialValue = slim.getSelected();
                const selectId = select.id;

                const botones = document.querySelectorAll(
                    `[data-source="${selectId}"]`
                );
                botones.forEach((boton) => {
                    boton.setAttribute("data-id", initialValue[0]);
                    console.log(boton.getAttribute("data-id"));
                });
                slimInstances.set(select, slim);
            });
        })
        .catch((error) => {
            console.error("Error al cargar los articulos:", error);
        });
}

function actualizarSelectArticulos(selectedId = null) {
    fetch("/articulos/data-tom")
        .then((response) => response.json())
        .then((articulos) => {
            console.log(articulos);

            const selects = document.querySelectorAll(".select_articulos");

            selects.forEach((select) => {
                let slim = slimInstances.get(select);

                const options = articulos.map((articulo) => ({
                    text: articulo.text,
                    value: articulo.value,
                }));

                // Agregar opción "Ninguno"
                options.unshift({ text: "Ninguno", value: "" });

                if (slim) {
                    slim.setData(options);

                    // Si se pasa un ID para seleccionar, lo seteamos
                    if (selectedId !== null) {
                        slim.setSelected(selectedId);

                        // Actualizamos también los botones relacionados si existen
                        const selectId = select.id;
                        const botones = document.querySelectorAll(
                            `[data-source="${selectId}"]`
                        );
                        botones.forEach((boton) => {
                            boton.setAttribute("data-id", selectedId);
                        });
                    }
                }
            });
        })
        .catch((error) => {
            console.error("Error al cargar los articulos:", error);
        });
}

InicializarSelectArticulos();

document
    .getElementById("formAddArticulo")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            const response = await fetch("/articulos", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            // Limpiar errores previos
            for (const input of form.elements) {
                input.classList.remove("is-invalid");
                const next = input.nextElementSibling;
                if (next && next.classList.contains("invalid-feedback")) {
                    next.remove();
                }
            }

            if (response.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Artículo agregado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                form.reset();

                // Reiniciar preview
                const previews = form.querySelectorAll(".preview-box");
                previews.forEach((preview) => (preview.innerHTML = ""));

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("addArticulo")
                );
                modal.hide();

                // Recargar DataTable si existe
                if ($("#articulos_table").length) {
                    $("#articulos_table").DataTable().ajax.reload(null, false);
                }

                // Si usás select dinámico para artículos
                if (typeof actualizarSelectArticulos === "function") {
                    actualizarSelectArticulos();
                }
            } else {
                Swal.close();
                // Mostrar errores de validación
                for (const key in data.errors) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add("is-invalid");
                        let feedback = document.createElement("div");
                        feedback.classList.add("invalid-feedback");
                        feedback.innerText = data.errors[key].join(", ");
                        input.after(feedback);
                    }
                }
            }
        } catch (error) {
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
            console.error(error);
        }
    });

$("#articulos_table").DataTable({
    ajax: {
        url: "/articulos/data",
        dataSrc: function (json) {
            console.log(json);

            const rows = Array.isArray(json.data) ? json.data : [];

            const total = rows.length;
            document.querySelector("#articulos_totales").textContent = total;
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
            data: null,
            render: function (data, type, row) {
                const imagen = row.imagen
                    ? `/storage/${row.imagen}`
                    : `/storage/defaults/default_articulo.png`;

                const html = `
                <div class="d-flex align-items-center">
                    <img src="${imagen}" class="me-2 thumb-md align-self-center rounded" alt="..." style="object-fit:cover; width: 48px; height: 48px;">
                    <div class="flex-grow-1 text-truncate">
                        <h6 class="m-0">${row.nombre}</h6>
                        <p class="fs-12 text-muted mb-0">${
                            row.categoria ?? ""
                        }</p>
                    </div>
                </div>
                `;
                return html;
            },
        },
        {
            data: "stock",
        },
        {
            data: "descripcion",
        },
        {
            data: "precio",
            className: "precio",
            render: function (data, type, row) {
                return currency(data, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                    fromCents: true,
                }).format();
            },
        },

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
                            <a class="dropdown-item btn_editar_articulo" type="button" data-id="${row.id}">Editar</a>
                            <a class="dropdown-item btn_ver_ingresos_articulo"type="button" data-id="${row.id}">Ingresos</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger btn_eliminar_articulo" type="button" data-id="${row.id}">Eliminar</a>
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
$(document).on("click", ".btn_editar_articulo", function () {
    const id = $(this).attr("data-id");

    if (!id) return;
    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    $.get(`/articulos/${id}?_=${Date.now()}`, function (articulo) {
        // Seteamos campos del modal
        $("#edit_id_articulo").val(articulo.id);
        const campos = [
            "nombre",
            "codigo",
            "stock",
            "precio",
            "descripcion",
            "descuento",
        ];

        campos.forEach((campo) => {
            $(`#editArticulo [name='${campo}']`).val(articulo[campo]);
        });

        const previewBox = document.getElementById("preview_articulo_edit");
        if (previewBox) {
            previewBox.innerHTML = ""; // Borrar contenido anterior

            if (articulo.imagen != null) {
                // Asegurarse de que haya una imagen para mostrar (real o default)
                let imagenSrc = "/storage/default.png"; // valor por defecto
                if (articulo.imagen && articulo.imagen.trim() !== "") {
                    imagenSrc = `/storage/${articulo.imagen}`;
                }

                previewBox.innerHTML = `
        <img class="preview-content" src="${imagenSrc}" style="max-width: 100%; height: auto;" />
    `;
            }
        }
        Swal.close();

        $("#editArticulo").modal("show");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener el artículo:", errorThrown);

        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información del artículo.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});
document
    .getElementById("formEditArticulo")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = document.getElementById("edit_id_articulo").value;
        const formData = new FormData(form);

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            const response = await fetch(`/articulos/${id}`, {
                method: "POST", // Simulamos PUT
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: (() => {
                    formData.append("_method", "PUT");
                    return formData;
                })(),
            });

            const data = await response.json();

            if (response.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Actualizado!",
                    text: "El artículo fue actualizado correctamente.",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });
                actualizarSelectArticulos(id);
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editArticulo")
                );
                modal.hide();

                // Recargar DataTable de artículos
                $("#articulos_table").DataTable().ajax.reload(null, false);
            } else {
                Swal.close();

                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
                    const feedback =
                        input.parentElement.querySelector(".invalid-feedback");
                    if (feedback) feedback.remove();
                }

                for (const key in data.errors) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add("is-invalid");
                        let feedback = document.createElement("div");
                        feedback.classList.add("invalid-feedback");
                        feedback.innerText = data.errors[key].join(", ");
                        input.after(feedback);
                    }
                }
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al actualizar el artículo.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });
$(document).on("click", ".btn_eliminar_articulo", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/articulos/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
            })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(
                            data.message || "No se pudo eliminar el artículo."
                        );
                    }
                    return data;
                })
                .then((data) => {
                    Swal.fire(
                        "Eliminado",
                        data.message || "Artículo eliminado correctamente.",
                        "success"
                    );
                    actualizarSelectArticulos();
                    // Opcional: recargar tabla
                    $("#articulos_table").DataTable().ajax.reload();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        error.message || "No se pudo eliminar el artículo.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});

$("#ingresos_articulo_table").DataTable({
    ajax: {
        url: "/carga-articulos/data",
        dataSrc: function (json) {
            console.log(json);
            return json; // tu endpoint devuelve array plano
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        },
    },
    error: function (xhr) {
        console.error(xhr.responseText);
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
            data: "cantidad",
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
                          
                           
                            <a class="dropdown-item text-danger btn_eliminar_ingreso_factura" href="#" data-id="${row.id}">Eliminar</a>
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

$(document).on("click", ".btn_ver_ingresos_articulo", function () {
    const id = $(this).data("id");

    // seteo hidden del form de "Agregar pago"
    $("#ingreso_articulo_id").val(id);

    // armo la nueva URL con el filtro
    const nuevaUrl = `/carga-articulos/articulo/${id}`;
    // obtengo la instancia del DataTable y cambio la url de ajax
    const tabla = $("#ingresos_articulo_table").DataTable();
    tabla.ajax.url(nuevaUrl).load(null, false); // false = no resetea paginación

    // muestro el modal
    $("#verIngresosArticulo").modal("show");
});
document
    .getElementById("formAgregarIngreso")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;

        // limpiar errores previos
        [...form.elements].forEach((el) => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach((el) => el.remove());

        const formData = new FormData(form);

        // Mapeo: el backend espera id_articulo (no articulo_id)
        const articuloId = document.getElementById(
            "ingreso_articulo_id"
        )?.value;
        if (articuloId) {
            formData.delete("articulo_id"); // por si vino del form
            formData.append("id_articulo", articuloId);
        }

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            // Usá "/api/carga-articulos" si definiste la ruta en routes/api.php
            const endpoint = "/carga-articulos";

            const resp = await fetch(endpoint, {
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
                    text: data?.message || "Ingreso registrado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                });

                // cerrar modal + reset
                const modalEl = document.getElementById("modalAgregarIngreso");
                const modal =
                    bootstrap.Modal.getInstance(modalEl) ||
                    bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
                form.reset();

                // Si usás DataTables, recargá lo necesario:
                if (window.$ && $.fn.DataTable) {
                    // Ajustá los IDs a tus tablas reales
                    $("#ingresos_articulo_table")
                        .DataTable()
                        .ajax?.reload(null, false);
                    $("#articulos_table").DataTable().ajax?.reload(null, false);
                }
            } else {
                Swal.close();

                // Validaciones (422)
                if (resp.status === 422 && data?.errors) {
                    const fallbackMsgs = [];
                    for (const name in data.errors) {
                        // Mapear id_articulo -> articulo_id (hidden)
                        const mapped =
                            name === "id_articulo" ? "articulo_id" : name;
                        const input = form.querySelector(`[name="${mapped}"]`);

                        if (input && input.type !== "hidden") {
                            input.classList.add("is-invalid");
                            const fb = document.createElement("div");
                            fb.className = "invalid-feedback";
                            fb.textContent = data.errors[name].join(", ");
                            const next = input.nextElementSibling;
                            if (
                                next &&
                                next.classList.contains("invalid-feedback")
                            )
                                next.remove();
                            input.insertAdjacentElement("afterend", fb);
                        } else {
                            // Si el input es hidden o no existe, juntamos para mostrar en un alert
                            fallbackMsgs.push(data.errors[name].join(", "));
                        }
                    }

                    if (fallbackMsgs.length) {
                        Swal.fire({
                            title: "Revisá los datos",
                            html: fallbackMsgs.join("<br>"),
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text:
                            data?.message || "No se pudo registrar el ingreso.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            }
        } catch (err) {
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
            });
        }
    });

// Eliminar ingreso (carga_articulo) y devolver stock
$(document).on("click", ".btn_eliminar_ingreso_factura", function (e) {
    e.preventDefault();

    const id = $(this).data("id");
    if (!id) return;

    // Si algún día querés un borrado "definitivo", podés pasar data-force="1" en el botón
    const force = $(this).data("force") ? true : false;

    Swal.fire({
        title: "¿Estás seguro?",
        text: force
            ? "Esto eliminará el ingreso de forma DEFINITIVA."
            : "Esto revertirá el ingreso y devolverá el stock al artículo.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: force ? "Sí, eliminar definitivo" : "Sí, revertir",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        // Ajustá a /api/carga-articulos si tu ruta está en routes/api.php
        const base = "/carga-articulos";
        const url = force ? `${base}/${id}?force=1` : `${base}/${id}`;

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
                        data?.message || "No se pudo eliminar el ingreso."
                    );
                }

                Swal.fire(
                    "Listo",
                    data?.message ||
                        (force
                            ? "Ingreso eliminado definitivamente."
                            : "Ingreso revertido y stock restaurado."),
                    "success"
                );

                // Recargá las tablas que correspondan en tu vista
                if (window.$ && $.fn.DataTable) {
                    // Cambiá estos IDs por los de tus tablas
                    $("#ingresos_articulo_table")
                        .DataTable()
                        .ajax?.reload(null, false);
                    $("#articulos_table").DataTable().ajax?.reload(null, false);
                }
            })
            .catch((err) => {
                Swal.fire(
                    "Error",
                    err.message || "No se pudo eliminar el ingreso.",
                    "error"
                );
                console.error(err);
            });
    });
});
