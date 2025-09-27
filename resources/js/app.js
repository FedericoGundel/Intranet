import $ from "jquery";
window.$ = $;
window.jQuery = $;

// Evento para mostrar preview al seleccionar archivo (igual que el tuyo)
document.querySelectorAll(".input-imagen").forEach((input) => {
    input.addEventListener("change", function () {
        const previewId = this.dataset.previewTarget;
        const previewBox = document.getElementById(previewId);
        const file = this.files[0];

        if (file && previewBox) {
            const reader = new FileReader();
            reader.onload = function () {
                previewBox.innerHTML = `<img class="preview-content" src="${reader.result}" style="max-width: 100%; height: 100%;" />`;
            };
            reader.readAsDataURL(file);

            // Si había marcado eliminar, desmarcarlo (porque ahora hay archivo nuevo)
            const container = input.closest(".d-grid");
            if (container) {
                const eliminarInput = container.querySelector(
                    'input[name="eliminar_imagen"]'
                );
                if (eliminarInput) eliminarInput.value = "0";
            }
        }
    });
});
$(document).on("change", 'table thead input[name="select_all"]', function () {
    const table = $(this).closest("table");
    const isChecked = this.checked;
    table.find("tbody .row-checkbox").prop("checked", isChecked);
});

// Evento para eliminar imagen (funciona para cualquier botón con clase btn-eliminar-imagen)
document.querySelectorAll(".btn-eliminar-imagen").forEach((btn) => {
    btn.addEventListener("click", function () {
        // Buscar contenedor padre (d-grid)
        const container = this.closest(".d-grid");
        if (!container) return;

        // Limpiar preview
        const previewBox = container.querySelector(".preview-box");
        if (previewBox) previewBox.innerHTML = "";

        // Limpiar input file
        const inputFile = container.querySelector(
            'input.input-imagen[type="file"]'
        );
        if (inputFile) inputFile.value = "";

        // Marcar que la imagen se elimina
        const eliminarInput = container.querySelector(
            'input[name="eliminar_imagen"]'
        );
        if (eliminarInput) eliminarInput.value = "1";
    });
});
/*
function formatearPrecios() {
    const formatter = new Intl.NumberFormat("es-AR", {
        style: "decimal",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    document.querySelectorAll(".precio").forEach((el) => {
        const raw = el.textContent.replace(/[^0-9.-]/g, "");
        const number = parseFloat(raw);
        if (!isNaN(number)) {
            el.textContent = formatter.format(number);
        }
    });

    document.querySelectorAll("input.precio").forEach((el) => {
        const raw = el.value.replace(/[^0-9.-]/g, "");
        const number = parseFloat(raw);
        if (!isNaN(number)) {
            el.value = formatter.format(number);
        }
    });
}

*/
document.querySelectorAll(".config-input").forEach((input) => {
    input.addEventListener("change", function () {
        const key = this.dataset.key;
        if (!key) return;

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        if (this.type === "file") {
            // Para input tipo file enviamos FormData con imagen
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append("key", key);
            formData.append("imagen", file);

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
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                    // No poner Content-Type, fetch lo maneja para FormData
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.close();

                        const reader = new FileReader();
                        reader.onload = function () {
                            console.log($("." + key).get(0));
                            console.log(reader.result);
                            $("." + key).attr("src", reader.result);
                        };
                        reader.readAsDataURL(file);

                        Swal.fire({
                            icon: "success",
                            title: "Guardado",
                            text: `Configuración "${key}" actualizada correctamente.`,
                            timer: 1500,
                            showConfirmButton: false,
                        });
                        // Actualizar preview si devuelve URL
                        if (data.url) {
                            const previewId = this.dataset.previewTarget;
                            const previewBox =
                                document.getElementById(previewId);
                            if (previewBox) {
                                previewBox.innerHTML = `<img src="${data.url}" style="max-width: 100%; height: 100%;" />`;
                            }
                        }
                    } else {
                        Swal.close();
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text:
                                data.message ||
                                "No se pudo actualizar la configuración.",
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
        } else {
            // Para inputs texto u otros enviamos JSON
            const value = this.value;

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
                    "X-CSRF-TOKEN": csrfToken,
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
        }
    });
});

