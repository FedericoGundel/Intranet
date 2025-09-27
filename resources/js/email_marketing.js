import $ from "jquery";
window.$ = $;
window.jQuery = $;
import "bootstrap/dist/js/bootstrap.bundle.min.js";

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

import "summernote/dist/summernote-bs5.min.js";
import "summernote/dist/summernote-bs5.min.css";
import "summernote/dist/lang/summernote-es-ES.min.js";

$("#email_marketing_table").DataTable({
    ajax: "/clientes/data",
    dataSrc: function (json) {
        console.log(json);

        return json.data;
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
// ✅ Al cambiar el checkbox "Seleccionar todos"

// ✅ Si se marca/desmarca un checkbox individual
$(document).on("change", "table tbody .row-checkbox", function () {
    const table = $(this).closest("table");
    const all = table.find("tbody .row-checkbox").length;
    const checked = table.find("tbody .row-checkbox:checked").length;
    table
        .find('thead input[name="select_all"]')
        .prop("checked", all === checked);
});

$("#editor_email_marketing").summernote({
    height: 300,
    lang: "es-ES",
});

$("#btn_enviar_email_marketing").on("click", function () {
    const clienteIds = [];
    $("#email_marketing_table tbody .row-checkbox:checked").each(function () {
        clienteIds.push($(this).val());
    });

    if (clienteIds.length === 0) {
        Swal.fire({ icon: "warning", title: "Atención", text: "Por favor selecciona al menos un cliente." });
        return;
    }

    const asunto = $("#asunto_email_marketing").val().trim();
    const cuerpo = $("#editor_email_marketing").summernote("code");

    if (!asunto) {
        Swal.fire({ icon: "warning", title: "Atención", text: "El asunto es obligatorio." });
        return;
    }
    if (!cuerpo || cuerpo === "<p><br></p>") {
        Swal.fire({ icon: "warning", title: "Atención", text: "El cuerpo del email no puede estar vacío." });
        return;
    }

    // 📦 Preparar FormData
    const formData = new FormData();
    formData.append("asunto", asunto);
    formData.append("cuerpo", cuerpo);
    clienteIds.forEach((id, i) => {
        formData.append(`cliente_ids[${i}]`, id);
    });

    // 📎 Agregar archivos desde Uppy
    uppy.getFiles().forEach((file, i) => {
        formData.append(`archivos[]`, file.data, file.name);
    });

    // Enviar AJAX con archivos
    $.ajax({
        url: "/email-marketing/enviar",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            $("#btn_enviar_email_marketing").prop("disabled", true).text("Enviando...");
        },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "¡Listo!",
                text: response.message || "Emails enviados correctamente.",
            });
            $("#btn_enviar_email_marketing").prop("disabled", false).text("Comenzar envío");
            // opcional: uppy.reset();
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
            $("#btn_enviar_email_marketing").prop("disabled", false).text("Comenzar envío");
        },
    });
});


var uppy = new Uppy.Uppy({
  autoProceed: false // No sube automáticamente
})
.use(Uppy.Dashboard, {
  inline: true,
  target: '#files_email_marketing',
  locale: Uppy.locales.es_ES,
  hideUploadButton: true, // Oculta el botón "Subir"
  showProgressDetails: false, // Opcional: oculta detalles de progreso
  proudlyDisplayPoweredByUppy: false // Oculta la marca de agua de Uppy
});



$("#select_plantilla_email").change(function (e) {
    const plantillaId = $(this).val();

    if (!plantillaId) return;

    fetch(`/plantilla_email/${plantillaId}`)
        .then((res) => {
            if (!res.ok) throw new Error("Error al obtener plantilla");
            return res.json();
        })
        .then((plantilla) => {
            // Rellenar los campos
            $("#asunto_email_marketing").val(plantilla.asunto).trigger("change");

            // Usar Summernote para cargar el contenido HTML
            $("#editor_email_marketing").summernote("code", plantilla.cuerpo);
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                title: "Error",
                text: "Error al cargar la plantilla.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        });
});
