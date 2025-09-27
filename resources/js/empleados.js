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
$(function () {
    $.fn.offcanvas.Constructor.prototype._initializeFocusTrap = () => ({
        activate: () => {},
        deactivate: () => {},
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const addSelect = document.getElementById("add_rol");
    const addBlock = document.getElementById("add_bloque_vendedor");
    const editSelect = document.getElementById("edit_rol");
    const editBlock = document.getElementById("edit_bloque_vendedor");

    function toggleBlock(select, block) {
        if (select.value === "vendedor" || select.value === "cobrador") {
            block.classList.add("show");
        } else {
            block.classList.remove("show");
        }
    }

    if (addSelect) {
        toggleBlock(addSelect, addBlock);
        addSelect.addEventListener("change", () =>
            toggleBlock(addSelect, addBlock)
        );
    }

    if (editSelect) {
        toggleBlock(editSelect, editBlock);
        editSelect.addEventListener("change", () =>
            toggleBlock(editSelect, editBlock)
        );
    }
});

function InicializarSelectEmpleados() {
    const selects = document.querySelectorAll(".select_empleados");

    selects.forEach((select) => {
        const tipo = (select.dataset.tipo || "").toLowerCase(); // ej: "vendedor"
        const url = `/empleados/data-tom${
            tipo !== "otro" ? "?tipo=" + tipo : ""
        }`;

        fetch(url)
            .then((response) => response.json())
            .then((empleados) => {
                const options = empleados.map((e) => ({
                    text: e.text,
                    value: e.value,
                }));
                options.unshift({ text: "Ninguno", value: "" });

                const slim = new SlimSelect({
                    select: select,
                    data: options,
                    settings: {
                        showSearch: true,
                        focusSearch: false,
                        searchHighlight: true,
                        placeholder: "Buscar empleado...",
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

                // Sincronizo botones relacionados con el valor inicial
                const initialValue = slim.getSelected();
                const selectId = select.id;
                document
                    .querySelectorAll(`[data-source="${selectId}"]`)
                    .forEach((boton) => {
                        boton.setAttribute(
                            "data-id",
                            Array.isArray(initialValue)
                                ? initialValue[0]
                                : initialValue
                        );
                    });

                slimInstances.set(select, slim);
            })
            .catch((err) =>
                console.error("Error al cargar los empleados:", err)
            );
    });
}

function actualizarSelectEmpleados(selectedId = null) {
    const selects = document.querySelectorAll(".select_empleados");

    selects.forEach((select) => {
        const tipo = (select.dataset.tipo || "").toLowerCase(); // ej: "vendedor"
        const url = `/empleados/data-tom${
            tipo !== "otro" ? "?tipo=" + tipo : ""
        }`;

        fetch(url)
            .then((response) => response.json())
            .then((empleados) => {
                const options = empleados.map((e) => ({
                    text: e.text,
                    value: e.value,
                }));
                options.unshift({ text: "Ninguno", value: "" });

                const slim = slimInstances.get(select);
                if (slim) {
                    slim.setData(options);

                    if (selectedId !== null) {
                        slim.setSelected(selectedId);

                        const selectId = select.id;
                        document
                            .querySelectorAll(`[data-source="${selectId}"]`)
                            .forEach((boton) => {
                                boton.setAttribute("data-id", selectedId);
                            });
                    }
                }
            })
            .catch((err) =>
                console.error("Error al cargar los empleados:", err)
            );
    });
}

InicializarSelectEmpleados();

document
    .getElementById("formAddEmpleado")
    ?.addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // limpiar feedbacks previos
        for (const el of form.querySelectorAll(".invalid-feedback"))
            el.remove();
        for (const input of form.elements)
            input.classList?.remove("is-invalid");

        // mandar si el switch está prendido (true/false) para que el backend lo sepa

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            const response = await fetch("/empleados", {
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

            if (response.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Empleado agregado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                form.reset();
                // cerrar modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("addEmpleado")
                );
                modal?.hide();

                // recargar DataTable si lo usás
                if (window.$ && $("#empleados_table").length) {
                    $("#empleados_table").DataTable().ajax.reload(null, false);
                    $("#empleados_nomina_table")
                        .DataTable()
                        .ajax.reload(null, false);
                }

                // refrescar selects si tenés alguno
                if (typeof actualizarSelectEmpleados === "function") {
                    actualizarSelectEmpleados();
                }
            } else {
                Swal.close();
                // pintar errores de validación
                if (data?.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            let feedback = document.createElement("div");
                            feedback.classList.add("invalid-feedback");
                            feedback.innerText = Array.isArray(data.errors[key])
                                ? data.errors[key].join(", ")
                                : String(data.errors[key]);
                            input.after(feedback);
                        }
                    }
                    // si hay errores dentro del subarray vendedor[...]
                    Object.keys(data.errors).forEach((fullKey) => {
                        if (fullKey.startsWith("vendedor.")) {
                            const nameAttr =
                                fullKey.replace("vendedor.", "vendedor[") + "]";
                            const input = form.querySelector(
                                `[name="${nameAttr}"]`
                            );
                            if (input) {
                                input.classList.add("is-invalid");
                                let feedback = document.createElement("div");
                                feedback.classList.add("invalid-feedback");
                                feedback.innerText =
                                    data.errors[fullKey].join(", ");
                                input.after(feedback);
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text:
                            data?.message || "No se pudo procesar la solicitud",
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

// EDITAR EMPLEADO
$(document).on("click", ".btn_editar_empleado", function () {
    const id = $(this).attr("data-id");
    if (!id) return false;

    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.get(`/empleados/${id}?_=${Date.now()}`, function (empleado) {
        const $form = $("#formEditEmpleado");

        // Limpio y seteo el hidden id
        $form[0].reset();
        $("#edit_empleado_id").val(empleado.id);

        // Campos "simples" por name
        $form.find("[name='nombre']").val(empleado?.nombre ?? "");
        $form.find("[name='apellido']").val(empleado?.apellido ?? "");
        $form.find("[name='dni']").val(empleado?.dni ?? "");
        $form.find("[name='email']").val(empleado?.email ?? "");
        $form.find("[name='telefono']").val(empleado?.telefono ?? "");
        $form.find("[name='estado']").val(empleado?.estado ?? "activo");

        // Fecha (input type="date") -> asegurar formato YYYY-MM-DD
        const fechaIngreso = empleado?.fecha_ingreso
            ? new Date(empleado.fecha_ingreso)
            : null;
        if (fechaIngreso && !isNaN(fechaIngreso)) {
            const yyyy = fechaIngreso.getFullYear();
            const mm = String(fechaIngreso.getMonth() + 1).padStart(2, "0");
            const dd = String(fechaIngreso.getDate()).padStart(2, "0");
            $form.find("[name='fecha_ingreso']").val(`${yyyy}-${mm}-${dd}`);
        } else {
            $form.find("[name='fecha_ingreso']").val("");
        }

        // ----- Rol -----
        $form.find("[name='rol']").val(empleado?.rol ?? "otro");

        // Mostrar/ocultar el bloque colapsable según el rol
        if (empleado.rol === "vendedor" || empleado.rol === "cobrador") {
            $("#edit_bloque_vendedor").addClass("show");
        } else {
            $("#edit_bloque_vendedor").removeClass("show");
        }

        // Completar datos si es vendedor
        if (empleado.rol === "vendedor") {
            const vend = empleado?.vendedor ?? {};
            $form
                .find("[name='vendedor[meta_mensual]']")
                .val(vend?.meta_mensual ?? "");
            $form
                .find("[name='vendedor[comision_porcentaje]']")
                .val(vend?.comision_porcentaje ?? "");
            $form.find("[name='vendedor[zona]']").val(vend?.zona ?? "");
        } else if (empleado.rol === "cobrador") {
            const cob = empleado?.cobrador ?? {};
            // Solo usa comision_porcentaje
            $form.find("[name='vendedor[meta_mensual]']").val("");
            $form.find("[name='vendedor[zona]']").val("");
            $form
                .find("[name='vendedor[comision_porcentaje]']")
                .val(cob?.comision_porcentaje ?? "");
        } else {
            // Rol "otro": limpiar
            $form.find("[name='vendedor[meta_mensual]']").val("");
            $form.find("[name='vendedor[comision_porcentaje]']").val("");
            $form.find("[name='vendedor[zona]']").val("");
        }

        Swal.close();

        // Mostrar modal (Bootstrap 5)
        const modalEl = document.getElementById("editEmpleado");
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener el empleado:", errorThrown);
        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información del empleado.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});

// SUBMIT EDITAR EMPLEADO
document
    .getElementById("formEditEmpleado")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = document.getElementById("edit_empleado_id").value;
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

            const response = await fetch(`/empleados/${id}`, {
                method: "POST",
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
                    text: "El empleado fue actualizado correctamente.",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editEmpleado")
                );
                modal.hide();

                // Recargar DataTable
                $("#empleados_table").DataTable().ajax.reload(null, false);
                $("#empleados_nomina_table")
                    .DataTable()
                    .ajax.reload(null, false);

                // refrescar selects si corresponde
                if (typeof actualizarSelectEmpleados === "function") {
                    actualizarSelectEmpleados(id);
                }
            } else {
                Swal.close();

                // limpiar errores previos
                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
                }

                // mostrar errores de validación
                if (data?.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            let feedback = document.createElement("div");
                            feedback.classList.add("invalid-feedback");
                            feedback.innerText = Array.isArray(data.errors[key])
                                ? data.errors[key].join(", ")
                                : String(data.errors[key]);
                            input.after(feedback);
                        }
                    }
                    // errores dentro de vendedor[...]
                    Object.keys(data.errors).forEach((fullKey) => {
                        if (fullKey.startsWith("vendedor.")) {
                            const nameAttr =
                                fullKey.replace("vendedor.", "vendedor[") + "]";
                            const input = form.querySelector(
                                `[name="${nameAttr}"]`
                            );
                            if (input) {
                                input.classList.add("is-invalid");
                                let feedback = document.createElement("div");
                                feedback.classList.add("invalid-feedback");
                                feedback.innerText =
                                    data.errors[fullKey].join(", ");
                                input.after(feedback);
                            }
                        }
                    });
                }
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al actualizar el empleado.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });

// ELIMINAR EMPLEADO
$(document).on("click", ".btn_eliminar_empleado", function () {
    var id = $(this).attr("data-id");
    if (!id) return false;

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
            fetch(`/empleados/${id}`, {
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

                    $("#empleados_table").DataTable().ajax.reload(null, false);
                    $("#empleados_nomina_table")
                        .DataTable()
                        .ajax.reload(null, false);
                    actualizarSelectEmpleados();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar el empleado.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});

$("#empleados_table").DataTable({
    ajax: "/empleados/data",
    dataSrc: function (json) {
        console.log(json);
        return json.data; // ajustá si tu endpoint devuelve el array plano
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
        // ID

        // Nombre (con badge si es vendedor)
        {
            data: null,
            render: function (data, type, row) {
                const nombre = row.nombre ?? "";

                return `${nombre}`;
            },
        },

        // Apellido
        { data: "apellido", defaultContent: "" },
        {
            data: null,
            render: function (data, type, row) {
                let badge = "";
                switch (row.rol) {
                    case "vendedor":
                        badge =
                            '<span class="badge bg-success">Vendedor</span>';
                        break;
                    case "cobrador":
                        badge =
                            '<span class="badge bg-primary">Cobrador</span>';
                        break;
                    default:
                        badge = '<span class="badge bg-secondary">Otro</span>';
                        break;
                }
                return badge;
            },
        },
        // DNI
        { data: "dni", defaultContent: "" },

        // Email
        { data: "email", defaultContent: "" },

        // Teléfono
        { data: "telefono", defaultContent: "" },

        // Estado (chipcito)
        {
            data: "estado",
            render: function (data) {
                const estado = (data || "activo").toLowerCase();
                const cls = estado === "activo" ? "bg-primary" : "bg-secondary";
                return `<span class="badge ${cls}">${estado}</span>`;
            },
        },

        // Fecha de ingreso (formato local dd/mm/aaaa)
        {
            data: "fecha_ingreso",
            render: function (value) {
                if (!value) return "";
                const d = new Date(value);
                if (isNaN(d)) return value; // por si ya viene formateada
                return d.toLocaleDateString();
            },
        },

        // Acciones
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: function (data, type, row) {
                // Botones comunes
                let html = `
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Acciones <i class="las la-angle-down ms-1"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item btn_editar_empleado" type="button" data-id="${row.id}">Editar</a>
        `;

                // Solo si es vendedor
                if (row.rol === "vendedor") {
                    html += `
        <a class="dropdown-item btn_ver_facturas_empleado" type="button" data-id="${row.id}">Ventas</a>
    `;
                    html += `
        <a class="dropdown-item btn_ver_nominas_empleado" type="button" data-id="${row.id}">Nóminas</a>
    `;
                }
                if (row.rol === "cobrador") {
                    html += `
        <a class="dropdown-item btn_ver_cobros_empleado" type="button" data-id="${row.id}">Cobros</a>
    `;
                    html += `
        <a class="dropdown-item btn_ver_nominas_empleado" type="button" data-id="${row.id}">Nóminas</a>
    `;
                }
                // Divider + eliminar
                html += `
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger btn_eliminar_empleado" type="button" data-id="${row.id}">Eliminar</a>
                </div>
            </div>
        `;

                return html;
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

$(document).on("click", ".btn_ver_facturas_empleado", function () {
    const id = $(this).data("id");

    // armo la nueva URL con el filtro
    const nuevaUrl = `/empleados/${id}/facturas`;
    // obtengo la instancia del DataTable y cambio la url de ajax
    const tabla = $("#facturas_vendedor_table").DataTable();
    tabla.ajax.url(nuevaUrl).load(null, false); // false = no resetea paginación

    // muestro el modal
    $("#verFacturasVendedor").modal("show");
});

$("#facturas_vendedor_table").DataTable({
    ajax: "/cobros/data",
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
                     <a class="dropdown-item btn_ver_factura" data-id="${row.id}" type="button">Ver</a>
                  <a class="dropdown-item" href="/facturas/${row.id}/editar">Editar</a>
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
const inicioInput = document.getElementById("nomina_inicio");
const finInput = document.getElementById("nomina_fin");

if (inicioInput && finInput) {
    const hoy = new Date();

    // Paso 1: encontrar el lunes de esta semana
    const day = hoy.getDay(); // 0 = domingo, 1 = lunes...
    const diffToMonday = (day === 0 ? -6 : 1) - day;
    const lunesEstaSemana = new Date(hoy);
    lunesEstaSemana.setDate(hoy.getDate() + diffToMonday);

    // Paso 2: retroceder 7 días para ir a la semana pasada
    const lunesPasado = new Date(lunesEstaSemana);
    lunesPasado.setDate(lunesEstaSemana.getDate() - 7);

    // Paso 3: domingo de esa semana
    const domingoPasado = new Date(lunesPasado);
    domingoPasado.setDate(lunesPasado.getDate() + 6);

    // Formatear YYYY-MM-DD
    const format = (d) => d.toISOString().slice(0, 10);

    inicioInput.value = format(lunesPasado);
    finInput.value = format(domingoPasado);
}
$("#empleados_nomina_table").DataTable({
    ajax: {
        url: "/empleados/data",
        dataSrc: function (json) {
            console.log(json);

            // contar solo los empleados con rol vendedor
            let vendedores = json.data.filter(
                (emp) => emp.rol === "vendedor"
            ).length;

            // sumar deuda total
            let totalDeuda = json.data.reduce((acc, e) => acc + e.deuda, 0);

            // mostrar saldo pendiente en formato moneda
            $("#saldo_pendiente").text(
                currency(totalDeuda, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format()
            );

            // mostrar cantidad de vendedores
            $("#vendedores_totales").text(vendedores);

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
        // O con jQuery puro:
        $(this.api().table().container())
            .find(".row.dt-layout-table")
            .addClass("g-0");
    },

    columns: [
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                return `<div class="form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="${row.id}">
                        </div>`;
            },
        },

        // Nombre (con badge si es vendedor)
        {
            data: null,
            render: function (data, type, row) {
                const nombre = row.nombre ?? "";

                return `${nombre}`;
            },
        },

        // Apellido
        { data: "apellido", defaultContent: "" },

        // DNI
        { data: "dni", defaultContent: "" },

        // Acciones
    ],

    language: { url: "/js/datatables/i18n/es-ES.json" },
    responsive: true,
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});

$("#btn_calcular_nomina").on("click", async function () {
    // 1) Empleados seleccionados (id + nombre)
    const seleccion = [];
    $("#empleados_nomina_table tbody .row-checkbox:checked").each(function () {
        const $row = $(this).closest("tr");
        const id = $(this).val();
        // intenta tomar data-nombre del checkbox, si no existe toma la celda de Nombre (ajusta índice si hace falta)
        const nombre =
            $(this).data("nombre") ||
            $row.find("td").eq(1).text().trim() ||
            `Empleado #${id}`;
        seleccion.push({ id, nombre });
    });

    if (seleccion.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Selecciona al menos un empleado.",
        });
        return;
    }

    // 2) Fechas
    const desde = $("#nomina_inicio").val();
    const hasta = $("#nomina_fin").val();

    if (!desde || !hasta) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Debes elegir el inicio y fin de la semana.",
        });
        return;
    }
    if (new Date(desde) > new Date(hasta)) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "La fecha de inicio no puede ser mayor que la fecha de fin.",
        });
        return;
    }

    // 3) Preparar llamadas (una por empleado) a /nominas/upsert
    const csrf = $('meta[name="csrf-token"]').attr("content");
    const btn = $("#btn_calcular_nomina");

    // UI: deshabilitar botón
    btn.prop("disabled", true).text("Calculando...");

    Swal.fire({
        title: "Generando nóminas",
        html: `Procesando ${seleccion.length} empleado(s).`,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    // ---- Helper: extraer mensaje de error detallado ----
    const extractError = (xhr) => {
        try {
            // Preferir JSON si viene
            if (xhr?.responseJSON) {
                const j = xhr.responseJSON;
                let parts = [];
                if (j.message) parts.push(j.message);
                if (j.error && j.error !== j.message) parts.push(j.error);
                if (j.errors && typeof j.errors === "object") {
                    const fieldMsgs = [];
                    for (const [field, arr] of Object.entries(j.errors)) {
                        const arrTxt = Array.isArray(arr)
                            ? arr.join(" | ")
                            : String(arr);
                        fieldMsgs.push(
                            `<li><code>${field}</code>: ${arrTxt}</li>`
                        );
                    }
                    if (fieldMsgs.length) {
                        parts.push(
                            `<ul class="mb-0">${fieldMsgs.join("")}</ul>`
                        );
                    }
                }
                if (parts.length) return parts.join("<br>");
            }
            // Si vino texto plano/HTML
            if (xhr?.responseText) {
                try {
                    const parsed = JSON.parse(xhr.responseText);
                    if (parsed?.message) return parsed.message;
                } catch (_) {
                    return xhr.responseText;
                }
            }
            // Mensajes por status comunes
            if (xhr?.status === 419)
                return "Sesión expirada o CSRF inválido (419). Refresca la página.";
            if (xhr?.status === 401)
                return "No autorizado (401). Vuelve a iniciar sesión.";
            if (xhr?.status === 403)
                return "Prohibido (403). No tienes permisos.";
            if (xhr?.status === 404) return "No encontrado (404).";
            if (xhr?.status === 422) return "Datos inválidos (422).";
            if (xhr?.status === 500) return "Error interno del servidor (500).";
            if (xhr?.status === 0) return "Error de red o petición cancelada.";
            return "Error al generar nómina.";
        } catch (e) {
            return "Error al procesar la respuesta.";
        }
    };

    // ---- Helper: enviar una nómina ----
    const enviarNomina = ({ id, nombre }) => {
        const fd = new FormData();
        fd.append("empleado_id", id);
        fd.append("desde", desde);
        fd.append("hasta", hasta);

        return $.ajax({
            url: "/nominas/upsert",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrf },
        })
            .then((res) => ({
                ok: true,
                data: res,
                empleado_id: id,
                empleado_nombre: nombre,
            }))
            .catch((xhr) => {
                const detalle = extractError(xhr);
                const codigo = xhr?.status ?? "—";
                return {
                    ok: false,
                    error: detalle,
                    status: codigo,
                    empleado_id: id,
                    empleado_nombre: nombre,
                };
            });
    };

    // 4) Ejecutar en paralelo
    const resultados = await Promise.all(seleccion.map(enviarNomina));

    Swal.close();
    btn.prop("disabled", false).text("Calcular nómina");

    const ok = resultados.filter((r) => r.ok);
    const fail = resultados.filter((r) => !r.ok);

    // 5) Feedback enriquecido
    if (fail.length === 0) {
        Swal.fire({
            icon: "success",
            title: "¡Listo!",
            text: `Se generaron/actualizaron ${ok.length} nómina(s) en borrador.`,
        });
    } else {
        // Armar lista HTML de errores por empleado con código
        const items = fail
            .map((f) => {
                // Si el backend mandó HTML (lista de fields), lo respetamos
                const msg = f.error || "Error";
                return `
        <li class="mb-2">
          <b>${f.empleado_nombre}</b> (ID ${f.empleado_id}) — <span class="badge bg-secondary">HTTP ${f.status}</span><br>
          <div>${msg}</div>
        </li>`;
            })
            .join("");

        Swal.fire({
            icon: ok.length ? "warning" : "error",
            title: ok.length ? "Parcialmente completado" : "Sin éxito",
            html: `
        <p class="mb-2">Éxitos: <b>${ok.length}</b> &nbsp;&nbsp; Fallos: <b>${fail.length}</b></p>
        <ul class="text-start" style="max-height: 300px; overflow:auto; margin:0; padding-left: 1rem;">
          ${items}
        </ul>
      `,
            width: 700,
        });
    }

    // Resumen visible en el modal + previews de las exitosas
    $("#nomina_resumen_texto").text(
        `Nóminas OK: ${ok.length}. Fallidas: ${fail.length}.`
    );
    $("#nomina_resultado").show();
    $("#nomina_preview_contenedor").html("");

    ok.forEach((item) => {
        if (item?.data?.nomina) {
            renderPreviewNomina(item.data.nomina); // tu función existente
        }
    });
});

function formatearMoneda(n) {
    if (n === null || n === undefined) return "-";
    return new Intl.NumberFormat("es-AR", {
        style: "currency",
        currency: "ARS",
    }).format(Number(n));
}

function renderPreviewNomina(nomina) {
    // nomina: objeto devuelto por /nominas/upsert (response.nomina)
    const cont = document.getElementById("nomina_preview_contenedor");
    if (!cont) return;

    const empName = nomina?.empleado?.nombre
        ? `${nomina.empleado.nombre} ${nomina.empleado.apellido ?? ""}`.trim()
        : `#${nomina.empleado_id}`;
    const periodo = `${(nomina.desde || "").slice(0, 10)} al ${(
        nomina.hasta || ""
    ).slice(0, 10)}`;

    // Tabla de detalles (facturas)
    const filas = (nomina.detalles || [])
        .map(
            (d, i) => `
    <tr>
      <td>${i + 1}</td>
      <td>${d.factura_id ?? "-"}</td>
      <td class="text-end">${formatearMoneda(d.monto_base)}</td>
      <td class="text-end">${(Number(d.porcentaje) || 0).toFixed(2)}%</td>
      <td class="text-end">${formatearMoneda(d.comision_calculada)}</td>
    </tr>
  `
        )
        .join("");

    const html = `
    <div id="borrador_nomina_${nomina.id}" class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-start">
        <div>
          <strong>${
              nomina.rol == "cobrador" ? "Cobrador" : "Vendedor"
          }:</strong> ${empName} <br>
          <span class=""><strong>Período:</strong> ${periodo}</span> <br>
          <span class=""><span class="badge bg-secondary text-uppercase">${
              nomina.estado
          }</span></span>
        </div>
        
      </div>
      <div class="card-body">
        <div class="row g-2 mb-2">
          <div class="col-md-3">
            <div class="border rounded p-2">
              <div class="text-muted small">Base</div>
              <div class="fw-bold">${formatearMoneda(nomina.total_base)}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-2">
              <div class="text-muted small">Comisión (${Number(
                  nomina.porcentaje_comision || 0
              ).toFixed(2)}%)</div>
              <div class="fw-bold">${formatearMoneda(
                  nomina.total_comision
              )}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-2">
              <div class="text-muted small">Ajustes</div>
              <div class="fw-bold">${formatearMoneda(nomina.ajustes)}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-2">
              <div class="text-muted small">Total a pagar</div>
              <div class="fw-bold">${formatearMoneda(
                  nomina.total_calculado
              )}</div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Factura</th>
                <th class="text-end">Monto base</th>
                <th class="text-end">%</th>
                <th class="text-end">Comisión</th>
              </tr>
            </thead>
            <tbody>
              ${
                  filas ||
                  `<tr><td colspan="5" class="text-center text-muted">Sin facturas en el período.</td></tr>`
              }
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-end">
        <span class="me-3"><strong>Pagado:</strong> ${formatearMoneda(
            nomina.total_pagado
        )}</span>
        <span><strong>Saldo:</strong> ${formatearMoneda(nomina.saldo)}</span>
      </div>
    </div>
  `;

    cont.innerHTML = html;
}
// Cerrar nómina
$(document).on("click", ".btn_cerrar_nomina", function () {
    const id = $(this).data("id");
    const csrf = $('meta[name="csrf-token"]').attr("content");

    Swal.fire({
        title: "¿Cerrar nómina?",
        icon: "question",
        showCancelButton: true,
    })
        .then((res) => {
            if (!res.isConfirmed) return;
            return $.ajax({
                url: `/nominas/${id}/cerrar`,
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
            });
        })
        .then((resp) => {
            if (!resp) return;
            Swal.fire("Listo", "Nómina cerrada.", "success");
            // podés refrescar el preview: limpiar y volver a renderizar con resp.nomina
            $("#nominas_empleado_table").DataTable().ajax.reload(null, false);
            $("#borrador_nomina_" + resp.nomina.id).remove();
            renderPreviewNomina(resp.nomina);
        })
        .catch(() =>
            Swal.fire("Error", "No se pudo cerrar la nómina.", "error")
        );
});

// Registrar pago (ejemplo simple)
$(document).on("click", ".btn_pagar_nomina", async function () {
    const id = $(this).data("id");
    const saldo = Number($(this).data("saldo") ?? 0); // 👈 saldo disponible en el botón
    const csrf = $('meta[name="csrf-token"]').attr("content");

    const hoy = new Date().toISOString().slice(0, 10);

    const { value: formValues } = await Swal.fire({
        title: "Registrar pago",
        html: `
      <input id="pago_nomina_fecha" type="date" class="mb-2 form-control" value="${hoy}">
      <div class="input-group">
        <div class="input-group-text">
          <div class="form-check m-0">
            <input class="form-check-input" type="checkbox" id="pagar_todo">
            <label class="form-check-label ms-2" for="pagar_todo">Pagar todo</label>
          </div>
        </div>
        <input id="pago_nomina_importe"
               type="number"
               step="0.01"
               min="0.01"
               ${saldo > 0 ? `max="${saldo.toFixed(2)}"` : ""}
               class="form-control"
               placeholder="Importe (máx: ${saldo.toFixed(2)})">
      </div>
    `,
        showCancelButton: true,
        confirmButtonText: "Pagar",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
            htmlContainer: "mx-0 mb-0",
            popup: "p-2",
            title: "py-0",
            actions: "mt-2 w-100 justify-content-end gap-2",
            confirmButton: "btn btn-sm btn-success",
            cancelButton: "btn btn-sm btn-danger",
        },
        focusConfirm: false,
        didOpen: () => {
            const $imp = document.getElementById("pago_nomina_importe");
            const $chk = document.getElementById("pagar_todo");
            // foco al importe
            $imp?.focus();
            // toggle de “pagar todo”
            $chk?.addEventListener("change", () => {
                if ($chk.checked) {
                    $imp.value = saldo.toFixed(2);
                } else {
                    $imp.value = "";
                    $imp.focus();
                }
            });
        },
        preConfirm: () => {
            const fecha = document.getElementById("pago_nomina_fecha").value;
            const importeStr = document.getElementById(
                "pago_nomina_importe"
            ).value;
            const importe = Number(parseFloat(importeStr).toFixed(2));

            if (!fecha) {
                Swal.showValidationMessage("La fecha es obligatoria.");
                return false;
            }
            if (!importe || importe <= 0) {
                Swal.showValidationMessage("El importe debe ser mayor a 0.");
                return false;
            }
            if (saldo > 0 && importe > saldo) {
                Swal.showValidationMessage(
                    `El importe no puede superar ${saldo.toFixed(2)}.`
                );
                return false;
            }
            return { fecha, importe };
        },
    });

    if (!formValues) return;

    $.ajax({
        url: `/nominas/${id}/pagar`,
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf },
        data: formValues,
    })
        .then((resp) => {
            Swal.fire("Ok", "Pago registrado.", "success");
            // si estabas listando “borradores”, lo removés:
            $("#borrador_nomina_" + resp.nomina.id).remove();
            // re-render del preview (actualiza saldo y estado)
            renderPreviewNomina(resp.nomina);
            $("#nominas_empleado_table").DataTable().ajax.reload(null, false);
        })
        .catch((xhr) => {
            const msg =
                xhr?.responseJSON?.message || "No se pudo registrar el pago.";
            // si back devolvió saldo_disponible, mostrarlo
            const extra = xhr?.responseJSON?.saldo_disponible;
            Swal.fire(
                "Error",
                extra
                    ? `${msg} Saldo disponible: ${Number(extra).toFixed(2)}.`
                    : msg,
                "error"
            );
        });
});
// Abrir modal y cargar nóminas del empleado
$(document).on("click", ".btn_ver_nominas_empleado", function () {
    const id = $(this).data("id");
    const nuevaUrl = `/empleados/${id}/nominas`; // <- endpoint por empleado
    const tabla = $("#nominas_empleado_table").DataTable();
    tabla.ajax.url(nuevaUrl).load(null, false);
    $("#verNominasEmpleado").modal("show");
});

// Init DataTable (una sola vez)
$("#nominas_empleado_table").DataTable({
    ajax: "/nominas/data", // valor por defecto; se sobreescribe al abrir el modal
    dataSrc: function (json) {
        return json.data || json; // admite array simple o {data:[]}
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
            title: "Período",
            render: function (row) {
                const desde = row.desde ? String(row.desde).slice(0, 10) : "-";
                const hasta = row.hasta ? String(row.hasta).slice(0, 10) : "-";
                return `${desde} → ${hasta}`;
            },
        },
        {
            data: "porcentaje_comision",
            className: "text-end",
            render: (v) => `${(parseFloat(v) || 0).toFixed(2)}%`,
        },
        {
            data: "total_base",
            className: "text-end",
            render: (v) =>
                currency(v, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
        },
        {
            data: "total_comision",
            className: "text-end",
            render: (v) =>
                currency(v, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
        },
        {
            data: "total_calculado",
            className: "text-end",
            render: (v) =>
                currency(v, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
        },
        {
            data: "total_pagado",
            className: "text-end",
            render: (v) =>
                currency(v, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
        },
        {
            data: "saldo",
            className: "text-end",
            render: (v) =>
                currency(v, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format(),
        },
        {
            data: "estado",
            className: "text-start",
            render: function (estado) {
                const map = {
                    borrador: "badge bg-secondary",
                    parcial: "badge bg-warning text-dark",
                    cerrada: "badge bg-info text-dark",
                    pagada: "badge bg-success",
                };
                const cls =
                    map[(estado || "").toLowerCase()] ||
                    "badge bg-light text-dark";
                return `<span class="${cls} text-uppercase">${estado}</span>`;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: function (row) {
                const saldo = parseFloat(row.saldo) || 0;
                const estado = (row.estado || "").toLowerCase();

                const btnVer = `<a class="dropdown-item btn_ver_nomina" data-id="${row.id}" type="button">Ver</a>`;
                const btnCerrar =
                    estado === "borrador" || estado === "parcial"
                        ? `<a class="dropdown-item btn_cerrar_nomina" data-id="${row.id}" type="button">Cerrar</a>`
                        : "";

                const btnBorrar = `<a class="dropdown-item btn_eliminar_nomina" data-id="${row.id}" type="button">Eliminar</a>`;

                const btnPagar =
                    saldo > 0
                        ? `<a class="dropdown-item btn_pagar_nomina" data-id="${
                              row.id
                          }" data-saldo="${saldo.toFixed(
                              2
                          )}" type="button">Pagar</a>`
                        : "";

                return `
          <div class="dropdown">
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              Acciones <i class="las la-angle-down ms-1"></i>
            </button>
            <div class="dropdown-menu">
              ${btnVer}
            
              ${btnPagar}
                ${btnBorrar}
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
// Ver nómina
$(document).on("click", ".btn_ver_nomina", function () {
    const id = $(this).attr("data-id");
    if (!id) return false;

    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.get(`/nominas/${id}?_=${Date.now()}`, function (resp) {
        Swal.close();

        // Aseguramos limpiar el contenedor
        const $cont = $("#modal_nomina_contenido");
        $cont.html("");

        // Renderizamos la nómina en ese contenedor
        if (resp) {
            renderPreviewNomina(resp);
        } else {
            $cont.html(
                `<div class="alert alert-warning">No se encontró la nómina solicitada.</div>`
            );
        }

        // Mostrar modal (Bootstrap 5)
        const modalEl = document.getElementById("modalNomina");
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener la nómina:", errorThrown);
        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información de la nómina.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});
$(document).on("click", ".btn_eliminar_nomina", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará la nómina y no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/nominas/${id}`, {
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

                    // Si usás DataTable para listar nóminas:
                    $("#empleados_table").DataTable().ajax.reload(null, false);
                    $("#empleados_nomina_table")
                        .DataTable()
                        .ajax.reload(null, false);
                    $("#nominas_empleado_table")
                        .DataTable()
                        .ajax.reload(null, false);
                    // O cualquier refresco adicional que necesites:
                    // actualizarSelectNominas();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar la nómina.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});
