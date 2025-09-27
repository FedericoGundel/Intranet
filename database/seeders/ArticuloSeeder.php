<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Articulo;

class ArticuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articulos = [
            ["codigo" => 1, "nombre" => "ALACENAS (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 109125],
            ["codigo" => 2, "nombre" => "BAJO MESADAS (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 153000],
            ["codigo" => 3, "nombre" => "COMBO ALECENA Y BAJO MESADA (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 234000],
            ["codigo" => 4, "nombre" => "ALACENAS (1.20)", "descripcion" => "1.20", "precio" => 92250],
            ["codigo" => 5, "nombre" => "BAJO MESADAS (1.20)", "descripcion" => "1.20", "precio" => 144000],
            ["codigo" => 6, "nombre" => "COMBO ALECENA Y BAJO MESADA (1.20)", "descripcion" => "1.20", "precio" => 202500],
            ["codigo" => 7, "nombre" => "DESPENSERO (80 CM)", "descripcion" => "80 CM", "precio" => 164250],
            ["codigo" => 8, "nombre" => "DESPENSERO (60 CM)", "descripcion" => "60 CM", "precio" => 144000],
            ["codigo" => 9, "nombre" => "DESPENSERO (40 CM)", "descripcion" => "40 CM", "precio" => 114750],
            ["codigo" => 10, "nombre" => "MODULARES (1.20 CM)", "descripcion" => "1.20 CM", "precio" => 171000],
            ["codigo" => 11, "nombre" => "MODULARES (80 CM)", "descripcion" => "80 CM", "precio" => 114750],
            ["codigo" => 12, "nombre" => "ROPEROS MACIZOS (80 CM)", "descripcion" => "80 CM", "precio" => 153000],
            ["codigo" => 13, "nombre" => "ROPEROS MACIZOS (1.20 CM)", "descripcion" => "1.20 CM", "precio" => 191250],
            ["codigo" => 14, "nombre" => "ROPEROS MACIZOS (1.60 CM)", "descripcion" => "1.60 CM", "precio" => 315000],
            ["codigo" => 15, "nombre" => "ROPEROS CON ESPEJO DE ABRIR (80 CM)", "descripcion" => "80 CM", "precio" => 175500],
            ["codigo" => 16, "nombre" => "ROPEROS CON ESPEJO DE ABRIR (1.20 CM)", "descripcion" => "1.20 CM", "precio" => 213750],
            ["codigo" => 17, "nombre" => "ROPEROS CORREDIZOS C/ ESPEJO (80 CM)", "descripcion" => "80 CM", "precio" => 144000],
            ["codigo" => 18, "nombre" => "ROPEROS CORREDIZOS C/ ESPEJO (1.20 CM)", "descripcion" => "1.20 CM", "precio" => 183375],
            ["codigo" => 19, "nombre" => "BIBLIOTECA (1 MT X1.80 CM)", "descripcion" => "1 MT X1.80 CM", "precio" => 83250],
            ["codigo" => 20, "nombre" => "BIBLIOTECA (80 X 1.80 CM)", "descripcion" => "80 X 1.80 CM", "precio" => 75375],
            ["codigo" => 21, "nombre" => "BIBLIOTECA CUBO (80 X 1.10)", "descripcion" => "80 X 1.10", "precio" => 92250],
            ["codigo" => 22, "nombre" => "BIBLIOTECA CUBO (1MT X 1.10)", "descripcion" => "1MT X 1.10", "precio" => 99000],
            ["codigo" => 23, "nombre" => "BAHIUT (1.60 X 1MT)", "descripcion" => "1.60 X 1MT", "precio" => 229500],
            ["codigo" => 24, "nombre" => "BAHIUT (1.20 X 1MT)", "descripcion" => "1.20 X 1MT", "precio" => 180000],
            ["codigo" => 25, "nombre" => "BOTINEROS X 3 (SIMPLE)", "descripcion" => "SIMPLE", "precio" => 90000],
            ["codigo" => 26, "nombre" => "BOTINEROS X 4 (DOBLE)", "descripcion" => "DOBLE", "precio" => 101250],
            ["codigo" => 27, "nombre" => "CAJONERA DE 7 CAJONES (60 CM)", "descripcion" => "60 CM", "precio" => 105750],
            ["codigo" => 28, "nombre" => "CAJONERA DE 6 CAJONES (60 CM)", "descripcion" => "60 CM", "precio" => 103500],
            ["codigo" => 29, "nombre" => "CAJONERA DE 5 CAJONES (60 CM)", "descripcion" => "60 CM", "precio" => 96750],
            ["codigo" => 30, "nombre" => "CAJONERA DE 4 CAJONES (60 CM)", "descripcion" => "60 CM", "precio" => 87750],
            ["codigo" => 31, "nombre" => "CAMAS CUCHETA", "descripcion" => null, "precio" => 130500],
            ["codigo" => 32, "nombre" => "CAMAS (2Y 1/2)", "descripcion" => "2Y 1/2", "precio" => 99000],
            ["codigo" => 33, "nombre" => "CAMAS (1 PLAZA)", "descripcion" => "1 PLAZA", "precio" => 60750],
            ["codigo" => 34, "nombre" => "SILLAS (HINDU, SOL, FRANCESCA)", "descripcion" => "HINDU, SOL, FRANCESCA", "precio" => 37125],
            ["codigo" => 35, "nombre" => "SILLAS (SOFIA)", "descripcion" => "SOFIA", "precio" => 32625],
            ["codigo" => 36, "nombre" => "JUEGO DE 4 SILLAS HINDU (HINDU, SOL, FRANCESCA)", "descripcion" => "HINDU, SOL, FRANCESCA", "precio" => 148500],
            ["codigo" => 37, "nombre" => "JUEGO DE 6 SILLAS HINDU (HINDU, SOL, FRANCESCA)", "descripcion" => "HINDU, SOL, FRANCESCA", "precio" => 222750],
            ["codigo" => 38, "nombre" => "JUEGO DE 8 SILLAS HINDU (HINDU, SOL, FRANCESCA)", "descripcion" => "HINDU, SOL, FRANCESCA", "precio" => 297000],
            ["codigo" => 39, "nombre" => "JUEGO DE 4 SILLAS SOFIA (SOFIA)", "descripcion" => "SOFIA", "precio" => 130500],
            ["codigo" => 40, "nombre" => "JUEGO DE 6 SILLAS SOFIA (SOFIA)", "descripcion" => "SOFIA", "precio" => 195750],
            ["codigo" => 41, "nombre" => "JUEGO DE 8 SILLAS SOFIA (SOFIA)", "descripcion" => "SOFIA", "precio" => 261000],
            ["codigo" => 42, "nombre" => "KIT DE CUNA", "descripcion" => null, "precio" => 114750],
            ["codigo" => 43, "nombre" => "MESAS (1.80 CM)", "descripcion" => "1.80 CM", "precio" => 110000],
            ["codigo" => 44, "nombre" => "MESAS (1.60 CM)", "descripcion" => "1.60 CM", "precio" => 105000],
            ["codigo" => 45, "nombre" => "MESAS (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 66000],
            ["codigo" => 46, "nombre" => "COMBO MESA Y 4 SILLAS HINDU (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 221000],
            ["codigo" => 47, "nombre" => "COMBO MESA Y 6 SILLAS HINDU (1.60 CM)", "descripcion" => "1.60 CM", "precio" => 342500],
            ["codigo" => 48, "nombre" => "COMBO MESA Y 8 SILLAS HINDU (1.80 CM)", "descripcion" => "1.80 CM", "precio" => 430000],
            ["codigo" => 49, "nombre" => "COMBO MESA Y 4 SILLAS SOFIA (1.40 CM)", "descripcion" => "1.40 CM", "precio" => 201000],
            ["codigo" => 50, "nombre" => "COMBO MESA Y 6 SILLAS SOFIA (1.60 CM)", "descripcion" => "1.60 CM", "precio" => 312500],
            ["codigo" => 51, "nombre" => "COMBO MESA Y 8 SILLAS SOFIA (1.80 CM)", "descripcion" => "1.80 CM", "precio" => 390000],
            ["codigo" => 52, "nombre" => "VESTIDOR CON CAJONES (1.60 CM)", "descripcion" => "1.60 CM", "precio" => 320000],
            ["codigo" => 53, "nombre" => "VESTIDOR SIN CAJONES (1.60CM)", "descripcion" => "1.60CM", "precio" => 300000],
            ["codigo" => 54, "nombre" => "VESTIDOR (1.20 CM)", "descripcion" => "1.20 CM", "precio" => 175000],
            ["codigo" => 55, "nombre" => "VESTIDOR (90 CM)", "descripcion" => "90 CM", "precio" => 160000],
            ["codigo" => 56, "nombre" => "CANASTEROS (X4)", "descripcion" => "X4", "precio" => 90000],
            ["codigo" => 57, "nombre" => "CANASTEROS (X3)", "descripcion" => "X3", "precio" => 70875],
            ["codigo" => 58, "nombre" => "CANASTEROS CON PUERTAS (2 PUERTAS)", "descripcion" => "2 PUERTAS", "precio" => 165000],
            ["codigo" => 59, "nombre" => "PERCHEROS DE PIE", "descripcion" => null, "precio" => 49500],
            ["codigo" => 60, "nombre" => "MICROONDAS (ALTO)", "descripcion" => "ALTO", "precio" => 139500],
            ["codigo" => 61, "nombre" => "MICROONDAS (BAJO)", "descripcion" => "BAJO", "precio" => 128250],
            ["codigo" => 62, "nombre" => "BIBLIOTECA (60CM)", "descripcion" => "60CM", "precio" => 67500],
            ["codigo" => 63, "nombre" => "BIBLIOTECA (40CM)", "descripcion" => "40CM", "precio" => 57375],
            ["codigo" => 64, "nombre" => "COMODON (80CM X1MT)", "descripcion" => "80CM X1MT", "precio" => 121500],
            ["codigo" => 65, "nombre" => "COMODON (1X1)", "descripcion" => "1X1", "precio" => 121500],
            ["codigo" => 66, "nombre" => "ZAPATEROS", "descripcion" => null, "precio" => 105000],
            ["codigo" => 67, "nombre" => "MESA DE ARRIME", "descripcion" => null, "precio" => 57500],
            ["codigo" => 68, "nombre" => "MESA DE LUZ PUERTA Y CAJON", "descripcion" => null, "precio" => 54000],
            ["codigo" => 69, "nombre" => "MESA DE LUZ ECONOMICA", "descripcion" => null, "precio" => 33750],
        ];

        foreach ($articulos as $item) {
            Articulo::create(array_merge($item, [
                'stock' => 10,
                'descuento' => 0,
                'imagen' => null
            ]));
        }
    }
}
