document
    .getElementById("btn_cambiar_imagen_fondo")
    .addEventListener("click", function (event) {
        event.preventDefault(); // Evita que el enlace haga un redireccionamiento por defecto

        // Obtener el ID de la URL si está presente
        const urlParams = new URLSearchParams(window.location.search);
        const id = $("#id_usuario_perfil").val() // Si no hay id en la URL, usamos el ID del usuario autenticado

        // Hacer la solicitud GET al perfil con el ID
        fetch(`/perfil/get/${id}`)
            .then((response) => response.json())
            .then((data) => {
                // Aquí puedes trabajar con los datos del usuario
                const previewBox = document.getElementById(
                    "preview_fondo_perfil"
                );
                if (data.imagen_fondo) {
                    // Si la imagen de fondo es null o vacía, usar una imagen predeterminada de la carpeta 'defaults'
                    const imagenFondo = data.imagen_fondo
                        ? data.imagen_fondo
                        : "/storage/defaults/default_user.png";

                    previewBox.innerHTML = `<img class="preview-content" src="/storage/${imagenFondo}" style="max-width: 100%; height: 100%;" />`;
                } else {
                    previewBox.innerHTML = "";
                }
                // Mostrar el modal
                $("#edit_id_user").val( id);
                $("#cambiarFondo").modal("show");
            })
            .catch((error) => {
                console.error("Error al obtener el perfil:", error);
            });
    });

document
    .getElementById("formCambiarFondo")
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
            const response = await fetch("/perfil", {
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
                    text: "Fondo cambiado correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                form.reset();

                // Reiniciar preview
                const previews = form.querySelectorAll(".preview-box");
                previews.forEach((preview) => (preview.innerHTML = ""));

                // Actualizamos la imagen en el preview
                const previewBox = document.getElementById(
                    "preview_fondo_perfil"
                );
                previewBox.innerHTML = `<img class="preview-content" src="/storage/${data.imagen_fondo}" style="max-width: 100%; height: 100%;" />`;
                const fondoPortadaUser =
                    document.getElementById("fondo_portada_user");
                fondoPortadaUser.style.backgroundImage = `url('${
                    data.imagen_fondo ??
                    "/storage/defaults/default_user_background.png"
                }')`;

                // Ocultamos el modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("cambiarFondo")
                );
                modal.hide();
                /*
                // Recargar DataTable si existe
                if ($("#articulos_table").length) {
                    $("#articulos_table").DataTable().ajax.reload(null, false);
                }
*/
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

document
    .getElementById("btn_cambiar_imagen_foto")
    .addEventListener("click", function (event) {
        event.preventDefault(); // Evita que el enlace haga un redireccionamiento por defecto

        // Obtener el ID de la URL si está presente
        const urlParams = new URLSearchParams(window.location.search);
        const id = $("#id_usuario_perfil").val() // Si no hay id en la URL, usamos el ID del usuario autenticado

        // Hacer la solicitud GET al perfil con el ID
        fetch(`/perfil/get/${id}`)
            .then((response) => response.json())
            .then((data) => {
                // Aquí puedes trabajar con los datos del usuario
                const previewBox = document.getElementById(
                    "preview_foto_perfil"
                );
                if (data.imagen) {
                    // Si la imagen de fondo es null o vacía, usar una imagen predeterminada de la carpeta 'defaults'
                    const imagenFondo = data.imagen;

                    previewBox.innerHTML = `<img class="preview-content" src="/storage/${imagenFondo}" style="max-width: 100%; height: 100%;" />`;
                } else {
                    previewBox.innerHTML = "";
                }
                // Mostrar el modal
                $("#edit_id_user_foto").val(id);
                $("#cambiarFoto").modal("show");
            })
            .catch((error) => {
                console.error("Error al obtener el perfil:", error);
            });
    });



    document
    .getElementById("formCambiarFoto")
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
            const response = await fetch("/perfil", {
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
                    text: "Foto cambiada correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                });

                form.reset();

                // Reiniciar preview
                const previews = form.querySelectorAll(".preview-box");
                previews.forEach((preview) => (preview.innerHTML = ""));

                const fondoPortadaUser =
                    document.getElementById("foto_portada_user");
                fondoPortadaUser.src= data.imagen ??
                    "/storage/defaults/default_user.png"
                    if($("#id_usuario_perfil").data("auth") == true){
    $(".foto_user").attr("src",(data.imagen) ?  data.imagen :
                    "/storage/defaults/default_user.png")

                    }
            

                // Ocultamos el modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("cambiarFoto")
                );
                modal.hide();
                /*
                // Recargar DataTable si existe
                if ($("#articulos_table").length) {
                    $("#articulos_table").DataTable().ajax.reload(null, false);
                }
*/
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
