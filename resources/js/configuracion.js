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

$("#logs_table").DataTable({
    ajax: "/logs/data", // La ruta que devolverá los datos en formato JSON
    dataSrc: function (json) {
        console.log(json);
        return json.data; // Suponiendo que el JSON devuelto tiene una propiedad 'data' con los registros
    },
    buttons: [
        {
            extend: "colvis",
            text: "Visibilidad",
            className: "btn btn-sm",
        },
        "excelHtml5",
    ], // Opcional: para exportar a Excel y visibilidad de columnas
    dom: "<'row mb-2 g-0 justify-content-between'<'col-md-auto dt-length'f><'col-md-auto dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",
    initComplete: function () {
        // Agregar clase al info (footer con texto "Mostrando...")
        $(this.api().table().container()).find(".dt-info").addClass("my-2");
    },
    columns: [
        {
            data: "user",
            render: function (data, type, row) {
                // Suponiendo que tienes el nombre del usuario en un campo relacionado
                // Aquí solo ponemos el ID, pero puedes hacerlo más complejo si tienes un objeto con el usuario
                return `${data.name}`;
            },
        },
        { data: "user_agent" }, // Para el navegador
        {
            data: "created_at",
            render: function (data) {
                // Formatear la fecha de creación (si es necesario)
                return new moment(data).format("YYYY-MM-DD HH:mm");
            },
        },
        { data: "ip_address" },
    ],
    language: {
        url: "/js/datatables/i18n/es-ES.json", // Asegúrate de tener el archivo de lenguaje para español
    },
    responsive: true,
    processing: true,
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});
