<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        //Cliente::factory()->count(5000)->create();
        $clientes = [
            ["cliente_nombre" => "Hilda Gudi", "direccion_cliente" => "Calle 191 #2251, Padre Voltor", "dni_cliente" => "66161134"],
            ["cliente_nombre" => "Anabella Quinteros", "direccion_cliente" => "13 bis 2260", "dni_cliente" => null],
            ["cliente_nombre" => "Gladys Voquetz", "direccion_cliente" => "Grimaldi 1745", "dni_cliente" => null],
            ["cliente_nombre" => "Karen Castro", "direccion_cliente" => "Eucaliptos Calle 112 #3631", "dni_cliente" => "40856785"],
            ["cliente_nombre" => "Jonathan Salguero", "direccion_cliente" => "Necochea 1942", "dni_cliente" => "47308695"],
            ["cliente_nombre" => "Liliana Movendres", "direccion_cliente" => "Manuel Leal 1908", "dni_cliente" => null],
            ["cliente_nombre" => "Adriany Garcia", "direccion_cliente" => "Pueyrredon 5077", "dni_cliente" => null],
            ["cliente_nombre" => "Riviela Pereyra", "direccion_cliente" => "Casa 64 B° Federal", "dni_cliente" => null],
            ["cliente_nombre" => "Marcela Inciarte", "direccion_cliente" => "9 de Julio 4990", "dni_cliente" => "16271247"],
            ["cliente_nombre" => "Guadalupe Díaz", "direccion_cliente" => "Aguilar 1936", "dni_cliente" => null],
            ["cliente_nombre" => "Debora Awazado", "direccion_cliente" => "Relezini 1617", "dni_cliente" => null],
            ["cliente_nombre" => "Jenni Spoccia", "direccion_cliente" => "Pellegrini Paz", "dni_cliente" => null],
            ["cliente_nombre" => "Lucila Bianco", "direccion_cliente" => "100 bis #901", "dni_cliente" => "35796273"],
            ["cliente_nombre" => "Mayra Giuliana", "direccion_cliente" => "Manuel Mcal 1324", "dni_cliente" => "91701114"],
            ["cliente_nombre" => "Lorena Vargas", "direccion_cliente" => "Bacavedica (ilegible)", "dni_cliente" => "27539672"],
            ["cliente_nombre" => "Nieve de la Cruz", "direccion_cliente" => "Misiones 7211", "dni_cliente" => null],
            ["cliente_nombre" => "Yenina Paola Rivero", "direccion_cliente" => "Rufino Falbo 778", "dni_cliente" => "27604932"],
            ["cliente_nombre" => "Belen Gonzales", "direccion_cliente" => "Raymundo Res 2899", "dni_cliente" => "47.923.455"],
            ["cliente_nombre" => "Laura Amalia Moyano", "direccion_cliente" => "Barrio Obrero casa 11", "dni_cliente" => "25723976"],
            ["cliente_nombre" => "Agostina Zárate", "direccion_cliente" => "San Lorenzo 1268", "dni_cliente" => "44339663"],
            ["cliente_nombre" => "Paula Peralta", "direccion_cliente" => "Las Rocas 2930 entre Belgrano y Domingo", "dni_cliente" => "29533622"],
            ["cliente_nombre" => "Luciana Ayalef", "direccion_cliente" => "La prida 3139", "dni_cliente" => "43732613"],
            ["cliente_nombre" => "Milena Martínez", "direccion_cliente" => "Urquiza 4488 dpto 7", "dni_cliente" => "349579208"],
            ["cliente_nombre" => "Lucco Meloto Ebel", "direccion_cliente" => "Sargento Cabral 1380", "dni_cliente" => "30834523"],
            ["cliente_nombre" => "Josefina Bracamonte", "direccion_cliente" => "Barrio Jardín Nombach M9", "dni_cliente" => "40373551"],
            ["cliente_nombre" => "Analia Celien", "direccion_cliente" => "Córdoba 3104", "dni_cliente" => "14501337"],
            ["cliente_nombre" => "Rocío Belén Zurcite", "direccion_cliente" => "Grimaldi 1745", "dni_cliente" => null],
            ["cliente_nombre" => "Fernanda Bitumo", "direccion_cliente" => "Necochea 817", "dni_cliente" => "37847272"],
            ["cliente_nombre" => "Adriana García", "direccion_cliente" => "Barrio Jardín Nombach 11", "dni_cliente" => null],
            ["cliente_nombre" => "Soledad Maldonado", "direccion_cliente" => "Leal 1405", "dni_cliente" => "23958600"],
            ["cliente_nombre" => "Lucila Acosta", "direccion_cliente" => "Pedrazas 3781", "dni_cliente" => null],
            ["cliente_nombre" => "Gladys Vázquez", "direccion_cliente" => "Cerrito 4295", "dni_cliente" => null],
            ["cliente_nombre" => "Soledad Belén", "direccion_cliente" => "Grimaldi 2915", "dni_cliente" => null],
            ["cliente_nombre" => "Débora Alvarado", "direccion_cliente" => "Pellegrini 1616", "dni_cliente" => null],
        ];

        foreach ($clientes as $c) {
            $partes = preg_split('/\s+/', trim($c['cliente_nombre']));

            if (count($partes) === 1) {
                $nombre = $partes[0];
                $apellido1 = null;
                $apellido2 = null;
            } elseif (count($partes) === 2) {
                $nombre = $partes[0];
                $apellido1 = $partes[1];
                $apellido2 = null;
            } else {
                $nombre = $partes[0];
                $apellido1 = $partes[1];
                $apellido2 = implode(' ', array_slice($partes, 2));
            }

            Cliente::create([
                'nombre'         => $nombre,
                'apellido1'      => $apellido1,
                'apellido2'      => $apellido2,
                'tipo_cliente'   => "particular",
                'nif'            => $c['dni_cliente'] ?: null,
                'direccion'      => $c['direccion_cliente'],
                'codigo_postal'  => null,
                'localidad'      => "Olavarría",
                'provincia'      => "Buenos Aires",
                'pais'           => "Argentina",
                'email'          => null,
                'telefono'       => null,
            ]);
        }
    }
}
