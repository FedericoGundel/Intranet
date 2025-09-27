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

const slimInstances = new Map();
function InicializarSelectClientes() {
    fetch("/clientes/data-tom")
        .then((response) => response.json())
        .then((clientes) => {
            const selects = document.querySelectorAll(".select_clientes");

            selects.forEach((select) => {
                // Convertimos los datos para Slim Select
                const options = clientes.map((cliente) => ({
                    text: cliente.text,
                    value: cliente.value,
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
                        placeholder: "Buscar cliente...",
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
            console.error("Error al cargar los clientes:", error);
        });
}

function actualizarSelectClientes(selectedId = null) {
    fetch("/clientes/data-tom")
        .then((response) => response.json())
        .then((clientes) => {
            console.log(clientes);

            const selects = document.querySelectorAll(".select_clientes");

            selects.forEach((select) => {
                let slim = slimInstances.get(select);

                const options = clientes.map((cliente) => ({
                    text: cliente.text,
                    value: cliente.value,
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
            console.error("Error al cargar los clientes:", error);
        });
}

InicializarSelectClientes();
document
    .getElementById("formAddCliente")
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
            const response = await fetch("/clientes", {
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
                    text: "Cliente agregado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });
                form.reset();
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("addClient")
                );
                modal.hide();
                $("#clientes_table").DataTable().ajax.reload(null, false);
                actualizarSelectClientes();
                // Aquí podrías recargar una tabla o lista
            } else {
                Swal.close();
                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
                }

                for (const key in data.errors) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add("is-invalid");
                        // Opcional: mostrás mensaje debajo del input
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
        }
    });

$("#clientes_table").DataTable({
    ajax: {
        url: "/clientes/data",
        dataSrc: function (json) {
            console.log(json);

            const rows = Array.isArray(json.data) ? json.data : [];

            const total = rows.length;
            document.querySelector("#clientes_totales").textContent = total;
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
        // Agregar clase al info (footer con texto "Mostrando...")
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },
    columns: [
        {
            data: null,
            render: function (data, type, row) {
                // Concatenar nombre + apellido1 + apellido2 sin espacios extra
                return `${row.nombre ?? ""} ${row.apellido1 ?? ""} ${
                    row.apellido2 ?? ""
                }`.trim();
            },
        },
        { data: "email" },
        { data: "nif" },
        { data: "telefono" },
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
                <a class="dropdown-item" href="/clientes/${row.id}">Ver</a>
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
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    responsive: true,
});

$(document).on("click", ".btn_editar_cliente", function () {
    var id = $(this).attr("data-id");

    if (id == "" || id == undefined) {
        return false;
    }
    Swal.fire({
        title: "Cargando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    // Hacemos el GET al endpoint correspondiente
    $.get(`/clientes/${id}?_=${Date.now()}`, function (cliente) {
        // Acá haces lo que necesites con los datos del cliente

        $("#edit_id_cliente").val(cliente.id);
        $("#editClient input[name='nombre']").val(cliente.nombre);
        $("#editClient input[name='apellido1']").val(cliente.apellido1);
        $("#editClient input[name='apellido2']").val(cliente.apellido2);
        $("#editClient input[name='tipo_cliente']").val(cliente.tipo_cliente);
        $("#editClient input[name='nif']").val(cliente.nif);
        $("#editClient input[name='direccion']").val(cliente.direccion);
        $("#editClient input[name='codigo_postal']").val(cliente.codigo_postal);
        $("#editClient input[name='localidad']").val(cliente.localidad);
        $("#editClient input[name='provincia']").val(cliente.provincia);
        $("#editClient input[name='pais']").val(cliente.pais);
        $("#editClient input[name='email']").val(cliente.email);
        $("#editClient input[name='telefono']").val(cliente.telefono);
        Swal.close();
        // Mostrar modal si usás uno para edición
        $("#editClient").modal("show");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener el cliente:", errorThrown);

        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información del cliente.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});

document
    .getElementById("formEditCliente")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = document.getElementById("edit_id_cliente").value;
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
            const response = await fetch(`/clientes/${id}`, {
                method: "POST", // Laravel no acepta PUT con FormData, así que usamos POST + método oculto
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: (() => {
                    formData.append("_method", "PUT"); // simulamos PUT
                    return formData;
                })(),
            });

            const data = await response.json();

            if (response.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Actualizado!",
                    text: "El cliente fue actualizado correctamente.",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editClient")
                );
                modal.hide();

                // Recargar DataTable
                $("#clientes_table").DataTable().ajax.reload(null, false);
                actualizarSelectClientes(id);
            } else {
                Swal.close();
                // Limpiar errores anteriores
                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
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
                text: "Hubo un problema al actualizar el cliente.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });

$(document).on("click", ".btn_eliminar_cliente", function () {
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
            fetch(`/clientes/${id}`, {
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

                    // Opcional: recargar tabla
                    $("#clientes_table").DataTable().ajax.reload();
                    actualizarSelectClientes();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar el cliente.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});
