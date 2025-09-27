<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    public function run()
    {
        Configuration::setValue('nombre_app', 'Dev Studio');
        Configuration::setValue('razon_social', 'Dev Studio S.R.L.');

        Configuration::setValue('direccion', 'Calle Falsa 123, Ciudad, País');
        Configuration::setValue('telefono', '+54 11 1234-5678');
        Configuration::setValue('email', 'contacto@devstudio.com');

        Configuration::setValue('pagina_web', 'https://devstudio.com');

        Configuration::setValue('nif', '12345678X');           // NIF o CUIT
        Configuration::setValue('pais', 'Argentina');
        Configuration::setValue('provincia', 'Buenos Aires');
        Configuration::setValue('ciudad', 'Ciudad Autónoma de Buenos Aires');
        Configuration::setValue('login_image', '/storage/config_images/sgSR72P0cQ9pqhDdZsVkmnfRm3sHMXQYNYG6sRut.png');
        Configuration::setValue('login_background', '/storage/config_images/sgSR72P0cQ9pqhDdZsVkmnfRm3sHMXQYNYG6sRut.png');
        Configuration::setValue('codigo_postal', '7400');
        Configuration::setValue('logo_menu_mini', '/storage/config_images/sgSR72P0cQ9pqhDdZsVkmnfRm3sHMXQYNYG6sRut.png');
        Configuration::setValue('logo_menu_expanded', '/storage/config_images/sgSR72P0cQ9pqhDdZsVkmnfRm3sHMXQYNYG6sRut.png');

        Configuration::setValue('color_fondo_menu', '#000000');
        Configuration::setValue('color_enlaces_menu', '#000000');
        Configuration::setValue('color_enlaces_menu_hover', '#000000');
        Configuration::setValue('color_principal', '#000000');
        Configuration::setValue('color_secundario', '#000000');
        Configuration::setValue('rol_defecto', 1);
    }
}
