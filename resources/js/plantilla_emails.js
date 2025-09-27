import $ from "jquery";
window.$ = $;
window.jQuery = $;
import "bootstrap/dist/js/bootstrap.bundle.min.js";


import "bootstrap-icons/font/bootstrap-icons.css";

import SlimSelect from "slim-select";

import "summernote/dist/summernote-bs5.min.js";
import "summernote/dist/summernote-bs5.min.css";
import "summernote/dist/lang/summernote-es-ES.min.js";



const slimInstances = new Map();
function InicializarSelectPlantillaEmail() {
    fetch("/plantilla_email/data-tom")
        .then((response) => response.json())
        .then((plantilla_email) => {
            const selects = document.querySelectorAll(".select_plantilla_email");

            selects.forEach((select) => {
                // Convertimos los datos para Slim Select
                const options = plantilla_email.map((plantilla_email) => ({
                    text: plantilla_email.text,
                    value: plantilla_email.value,
                }));
                options.unshift({ text: "Ninguna", value: "" });

                // Creamos instancia de Slim Select
                const slim = new SlimSelect({
                    select: select,
                    data: options,
                    settings: {
                        showSearch: true, // used in example
                        focusSearch: false, // used in example

                        searchHighlight: true, // used in example
                        placeholder: "Buscar plantilla...",
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
            console.error("Error al cargar las plantillas:", error);
        });
}

function actualizarSelectPlantillaEmail(selectedId = null) {
    fetch("/plantilla_email/data-tom")
        .then((response) => response.json())
        .then((plantilla_email) => {
            console.log(plantilla_email);

            const selects = document.querySelectorAll(".select_plantilla_email");

            selects.forEach((select) => {
                let slim = slimInstances.get(select);

                const options = plantilla_email.map((plantilla_email) => ({
                    text: plantilla_email.text,
                    value: plantilla_email.value,
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
            console.error("Error al cargar las plantillas:", error);
        });
}

InicializarSelectPlantillaEmail();


$("#cuerpo_plantilla_email_editar").summernote({
    height: 300,
    lang: "es-ES",
});

$("#cuerpo_plantilla_email").summernote({
    height: 300,
    lang: "es-ES",
});


document
    .getElementById("formAddPlantillaEmail")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Si estás usando Summernote, necesitas agregar su contenido manualmente
        const cuerpoHtml = $('#cuerpo_plantilla_email').summernote('code');
        formData.set('cuerpo', cuerpoHtml);

        try {
            const response = await fetch("/plantilla_email", {
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
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Plantilla agregada correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                // Resetear formulario
                form.reset();
                $('#cuerpo_plantilla_email').summernote('reset');

               $("#addPlantillaEmail").modal("hide")

              actualizarSelectPlantillaEmail();

            } else {
                console.error(data);

                // Limpiar errores anteriores
                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
                    if (input.nextElementSibling?.classList.contains('invalid-feedback')) {
                        input.nextElementSibling.remove();
                    }
                }

                // Mostrar errores nuevos
                if (data.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            const feedback = document.createElement("div");
                            feedback.classList.add("invalid-feedback");
                            feedback.innerText = data.errors[key].join(", ");
                            input.after(feedback);
                        }
                    }
                }
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });
$(document).on("click", ".btn_editar_plantilla_email", function () {
    var id = $(this).attr("data-id");

    if (!id) {
        return false;
    }

    // GET al backend para obtener los datos de la plantilla
    $.get(`/plantilla_email/${id}?_=${Date.now()}`, function (plantilla) {
        // Setear valores en el formulario de edición
        $("#edit_id_plantilla_email").val(plantilla.id); // útil para el submit

        $("#editPlantillaEmail input[name='nombre']").val(plantilla.nombre);
        $("#editPlantillaEmail input[name='asunto']").val(plantilla.asunto);
        $('#cuerpo_plantilla_email_editar').summernote('code', plantilla.cuerpo);

        // Mostrar el modal
        $("#editPlantillaEmail").modal("show");

    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener la plantilla:", errorThrown);

        Swal.fire({
            title: "Error",
            text: "No se pudo obtener la información de la plantilla.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
    });
});

document
    .getElementById("formEditPlantillaEmail")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = $("#edit_id_plantilla_email").val(); // El ID lo guardamos en el form con .data("id", ...) al abrir el modal
        const formData = new FormData(form);

        try {
            const response = await fetch(`/plantilla_email/${id}`, {
                method: "POST", // simulamos PUT
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
                Swal.fire({
                    title: "¡Actualizado!",
                    text: "La plantilla fue actualizada correctamente.",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

               $("editPlantillaEmail").modal("hide")

               actualizarSelectPlantillaEmail(id);
            } else {
                console.error(data);
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
                text: "Hubo un problema al actualizar la plantilla.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        }
    });
$(document).on("click", ".btn_eliminar_plantilla_email", function () {
    var id = $(this).attr("data-id");

    if (!id) return;

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
            fetch(`/plantilla_email/${id}`, {
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

                  actualizarSelectPlantillaEmail();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        "No se pudo eliminar la plantilla.",
                        "error"
                    );
                    console.error(error);
                });
        }
    });
});
