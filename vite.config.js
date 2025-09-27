import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/clientes.js",
                "resources/js/cobros.js",
                "resources/js/articulos.js",
                "resources/js/configuracion.js",
                "resources/js/crear_factura.js",
                "resources/js/email_marketing.js",
                "resources/js/facturas.js",
                "resources/js/plantilla_emails.js",
                "resources/js/users.js",
                "resources/js/empleados.js",
                "resources/js/estadisticas.js",
                "resources/js/perfil.js",
                "resources/js/plantilla_emails.js",
                "resources/js/roles.js",
                "resources/js/gastos.js",
                "resources/js/leyma_creditos.js",
            ],
            refresh: true,
        }),
    ],
});
