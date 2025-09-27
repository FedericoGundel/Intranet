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
function InicializarSelectRoles() {
    fetch("/roles/data-tom")
        .then((response) => response.json())
        .then((roles) => {
            const selects = document.querySelectorAll(".select_roles");

            selects.forEach((select) => {
                // Convertimos los datos para Slim Select
                const options = roles.map((rol) => ({
                    text: rol.text,
                    value: rol.value,
                }));

                // Creamos instancia de Slim Select
                const slim = new SlimSelect({
                    select: select,
                    data: options,
                    settings: {
                        showSearch: true, // used in example
                        focusSearch: false, // used in example

                        searchHighlight: true, // used in example
                        placeholder: "Buscar rol...",
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
            console.error("Error al cargar los roles:", error);
        });
}

function actualizarSelectRoles(selectedId = null) {
    fetch("/roles/data-tom")
        .then((response) => response.json())
        .then((roles) => {
            console.log(roles);

            const selects = document.querySelectorAll(".select_roles");

            selects.forEach((select) => {
                let slim = slimInstances.get(select);

                const options = roles.map((rol) => ({
                    text: rol.text,
                    value: rol.value,
                }));

                // Agregar opción "Ninguno"

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
            console.error("Error al cargar los roles:", error);
        });
}

InicializarSelectRoles();

// Limpiar formularios cuando se cierren los modales
document.addEventListener("DOMContentLoaded", function () {
    // Modal de agregar rol
    const addRolModal = document.getElementById("addRol");
    if (addRolModal) {
        addRolModal.addEventListener("hidden.bs.modal", function () {
            document.getElementById("nombre_rol").value = "";
            // Desmarcar todos los checkboxes
            const checkboxes = addRolModal.querySelectorAll(".row-checkbox");
            checkboxes.forEach((checkbox) => (checkbox.checked = false));
        });
    }

    // Modal de editar rol
    const editRolModal = document.getElementById("editRol");
    if (editRolModal) {
        editRolModal.addEventListener("hidden.bs.modal", function () {
            document.getElementById("nombre_rol_editar").value = "";
            document.getElementById("edit_id_rol").value = "";
            // Desmarcar todos los checkboxes
            const checkboxes = editRolModal.querySelectorAll(".permiso_edit");
            checkboxes.forEach((checkbox) => (checkbox.checked = false));
        });
    }

    // Modal de rol por defecto
    const rolDefectoModal = document.getElementById("RolDefecto");
    if (rolDefectoModal) {
        rolDefectoModal.addEventListener("hidden.bs.modal", function () {
            // Resetear el select si es necesario
            const select = document.getElementById("select_roles");
            if (select) {
                select.value = "";
            }
        });
    }

    // Modal de editar rol de usuario
    const editarRolUsuarioModal = document.getElementById("editarRolUsuario");
    if (editarRolUsuarioModal) {
        editarRolUsuarioModal.addEventListener("hidden.bs.modal", function () {
            document.getElementById("rol_usuario").value = "";
            document.getElementById("edit_id_rol").value = "";
        });
    }
});

$("#permisos_table").DataTable({
    ajax: {
        url: "/roles/permisos?scope=views",
        dataSrc: "", // <— el JSON es un array plano, no { data: [] }
    },

    dom: "<'row g-0 justify-content-between'<'col-md-auto dt-length mb-2'f><'col-md-auto mb-2 dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",

    initComplete: function () {
        // Agregar clase al info (footer con texto "Mostrando...")
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
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
        {
            data: "name",
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

$("#permisos_table_editar").DataTable({
    ajax: {
        url: "/roles/permisos?scope=views",
        dataSrc: "", // <— el JSON es un array plano, no { data: [] }
    },

    dom: "<'row g-0 justify-content-between'<'col-md-auto dt-length mb-2'f><'col-md-auto mb-2 dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",

    initComplete: function () {
        // Agregar clase al info (footer con texto "Mostrando...")
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },
    columns: [
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                return `<div class="form-check">
                            <input class="form-check-input row-checkbox permiso_edit" type="checkbox" value="${row.id}">
                        </div>`;
            },
        },
        {
            data: "name",
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

$("#roles_table").DataTable({
    ajax: {
        url: "/roles/all",
        dataSrc: "", // <— el JSON es un array plano, no { data: [] }
    },

    dom: "<'row g-0 justify-content-between'<'col-md-auto dt-length mb-2'f><'col-md-auto mb-2 dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",

    initComplete: function () {
        // Agregar clase al info (footer con texto "Mostrando...")
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },
    columns: [
        {
            data: "name",
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
                <a class="dropdown-item btn_editar_rol" type="button" data-id="${row.id}">Editar</a>
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger btn_eliminar_rol" type="button" data-id="${row.id}">Eliminar</a>
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

$(document).on("click", ".btn_editar_rol", function () {
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
    $.get(`/roles/${id}?_=${Date.now()}`, function (cliente) {
        // Acá haces lo que necesites con los datos del cliente

        $("#edit_id_rol").val(cliente.id);
        $("#nombre_rol_editar").val(cliente.name);
        $(".permiso_edit").prop("checked", false);

        // Marcar los permisos que vienen del servidor
        cliente.permisos.forEach(function (permiso) {
            $(`.permiso_edit[value="${permiso.id}"]`).prop("checked", true);
        });

        Swal.close();
        // Mostrar modal
        $("#editRol").modal("show");
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

$("#btn_add_rol").on("click", function () {
    const permisosId = [];
    $("#permisos_table tbody .row-checkbox:checked").each(function () {
        permisosId.push($(this).val());
    });

    if (permisosId.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Por favor selecciona al menos un permiso.",
        });
        return;
    }

    const nombre = $("#nombre_rol").val().trim();

    if (!nombre) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "El nombre es obligatorio.",
        });
        return;
    }

    // 📦 Preparar FormData
    const formData = new FormData();
    formData.append("nombre", nombre);

    permisosId.forEach((id, i) => {
        formData.append(`permisos_id[${i}]`, id);
    });

    Swal.fire({
        title: "Guardando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    // Enviar AJAX con archivos
    $.ajax({
        url: "/roles",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            Swal.close();
            Swal.fire({
                icon: "success",
                title: "¡Listo!",
                text: response.message || "Emails enviados correctamente.",
            });
            $("#addRol").modal("hide");
            $("#roles_table").DataTable().ajax.reload(null, false);
            actualizarSelectRoles();
        },
        error: function (xhr) {
            let msg = "Ocurrió un error. Intente nuevamente.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: "error",
                title: "Error",
                text: msg,
            });
        },
    });
});
// Guardar edición de rol
$(document).on("click", "#btn_edit_rol", function (e) {
    e.preventDefault();

    const rolId = $("#edit_id_rol").val();
    const nombre = ($("#nombre_rol_editar").val() || "").trim();

    // Recolectar permisos chequeados en la tabla de EDITAR
    const permisosId = [];
    $(
        "#permisos_table_editar tbody .permiso_edit:checked, #permisos_table_editar tbody .row-checkbox:checked"
    ).each(function () {
        permisosId.push($(this).val());
    });

    // Validaciones básicas
    if (!rolId) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se encontró el ID del rol.",
        });
        return;
    }
    if (!nombre) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "El nombre es obligatorio.",
        });
        return;
    }
    if (permisosId.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Selecciona al menos un permiso.",
        });
        return;
    }

    // Armar FormData (con _method=PUT para Laravel)
    const formData = new FormData();

    formData.append("nombre", nombre);
    permisosId.forEach((id, i) => formData.append(`permisos_id[${i}]`, id));

    // Bloquear botón para evitar doble submit
    const $btn = $("#btn_edit_rol").prop("disabled", true);

    Swal.fire({
        title: "Guardando...",
        html: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.ajax({
        url: `/roles/${rolId}`,
        method: "POST", // usamos POST + _method=PUT
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            Swal.close();
            Swal.fire({
                icon: "success",
                title: "¡Listo!",
                text: response.message || "Rol actualizado correctamente.",
            });
            $("#editRol").modal("hide");
            $("#roles_table").DataTable().ajax.reload(null, false);
            actualizarSelectRoles();
        },
        error: function (xhr) {
            let msg = "Ocurrió un error. Intente nuevamente.";
            // Si vienen errores de validación (422) de Laravel
            if (
                xhr.status === 422 &&
                xhr.responseJSON &&
                xhr.responseJSON.errors
            ) {
                const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                msg = xhr.responseJSON.errors[firstKey][0];
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({ icon: "error", title: "Error", text: msg });
        },
        complete: function () {
            $btn.prop("disabled", false);
        },
    });
});
$(document).on("click", ".btn_eliminar_rol", function () {
    var id = $(this).attr("data-id");
    if (!id) return false;
    if (id == $("#btn_rol_defecto").attr("data-id")) {
        Swal.fire("Error", "No se puede eliminar el rol por defecto.", "error");
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
            fetch(`/roles/${id}`, {
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
                    if (data.success) {
                        Swal.fire("Eliminado", data.message, "success");
                        $("#roles_table").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire(
                            "Error",
                            data.message || "No se pudo eliminar el rol.",
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    Swal.fire("Error", "No se pudo eliminar el rol.", "error");
                    console.error(error);
                });
        }
    });
});

document
    .getElementById("btn_edit_rol_defecto")
    .addEventListener("click", function () {
        const key = document.getElementById("select_roles").dataset.key;
        if (!key) return;

        const value = document.getElementById("select_roles").value;

        Swal.fire({
            title: "Cargando...",
            html: "Por favor espere",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
        fetch("/configurations/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
            },
            body: JSON.stringify({ key: key, value: value }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.close();
                    $("." + key).text(value);
                    Swal.fire({
                        icon: "success",
                        title: "Guardado",
                        text: `Configuración "${key}" actualizada correctamente.`,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                    console.log(value);

                    $("#btn_rol_defecto").attr("data-id", value);
                } else {
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo actualizar la configuración.",
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: "Error en la petición",
                    text: "Hubo un problema al comunicarse con el servidor.",
                });
                console.error("Error en la petición:", error);
            });
    });

$(document).on("click", "#btn_rol_defecto", function () {
    const id = $(this).attr("data-id");
    actualizarSelectRoles(id);

    $("#RolDefecto").modal("show");
});

$(document).on("click", ".btn_rol_usuario", function () {
    var id = $(this).data("id");

    $.get(`/usuarios/${id}/roles?_=${Date.now()}`, function (cliente) {
        // Acá haces lo que necesites con los datos del cliente
        $("#btn_edit_rol_usuario").attr("data-id", id);
        $("#user_id_rol").val(id);
        if (cliente.roles.length > 0) {
            actualizarSelectRoles(cliente.roles[0].id);
        }
        Swal.close();
        // Mostrar modal
        $("#editarRolUsuario").modal("show");
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener el cliente:", errorThrown);

        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información del usuario.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});

document
    .getElementById("btn_edit_rol_usuario")
    .addEventListener("click", function (e) {
        e.preventDefault();

        const roleId = document.getElementById("rol_usuario").value; // value = id del rol
        const userId = $("#btn_edit_rol_usuario").attr("data-id"); // id = id del usuario
        const btn = this;

        if (!userId) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Falta el ID de usuario.",
            });
            return;
        }
        if (!roleId) {
            Swal.fire({
                icon: "warning",
                title: "Atención",
                text: "Seleccioná un rol.",
            });
            return;
        }

        btn.disabled = true;
        Swal.fire({
            title: "Guardando...",
            html: "Por favor espere",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        fetch(`/usuarios/${userId}/rol`, {
            method: "POST", // usa POST + JSON
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
            },
            body: JSON.stringify({ role_id: roleId }),
        })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    // Mensajes de validación (422) o errores del server
                    let msg = data?.message || "No se pudo actualizar el rol.";
                    if (res.status === 422 && data?.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        msg = data.errors[firstKey][0];
                    }
                    throw new Error(msg);
                }
                return data;
            })
            .then((data) => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Guardado",
                    text: data.message || "Rol asignado correctamente.",
                    timer: 1500,
                    showConfirmButton: false,
                });

                // Opcional: recargar una tabla si la tenés
                // $("#usuarios_table").DataTable().ajax.reload(null, false);

                // Opcional: actualizar un texto en la UI con el rol seleccionado
                // const rolTexto = $("#rol_usuario option:selected").text();
                // $(".rol-usuario-" + userId).text(rolTexto);
            })
            .catch((err) => {
                Swal.close();
                Swal.fire({ icon: "error", title: "Error", text: err.message });
            })
            .finally(() => {
                btn.disabled = false;
            });
    });
