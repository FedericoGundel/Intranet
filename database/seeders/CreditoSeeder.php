<?php

namespace Database\Seeders;

use App\Models\ConfiguracionIntereses;
use App\Models\TipoCredito;
use Illuminate\Database\Seeder;

class CreditoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear tipos de crédito
        $tiposCredito = [
            [
                'nombre' => 'diario',
                'descripcion' => 'Créditos con pagos diarios',
                'duracion_maxima_dias' => 52,
                'interes_semanal' => 10.0,
                'interes_maximo' => 80.0,
                'requiere_garante' => false,
                'activo' => true
            ],
            [
                'nombre' => 'semanal',
                'descripcion' => 'Créditos con pagos semanales',
                'duracion_maxima_dias' => 56,
                'interes_semanal' => 10.0,
                'interes_maximo' => 80.0,
                'requiere_garante' => true,
                'activo' => true
            ],
            [
                'nombre' => 'quincenal',
                'descripcion' => 'Créditos con pagos quincenales',
                'duracion_maxima_dias' => 56,
                'interes_semanal' => 10.0,
                'interes_maximo' => 80.0,
                'requiere_garante' => true,
                'activo' => true
            ],
            [
                'nombre' => 'contado',
                'descripcion' => 'Créditos de pago único',
                'duracion_maxima_dias' => 60,
                'interes_semanal' => 10.0,
                'interes_maximo' => 80.0,
                'requiere_garante' => true,
                'activo' => true
            ]
        ];

        foreach ($tiposCredito as $tipo) {
            TipoCredito::create($tipo);
        }

        // Crear configuraciones de intereses para créditos diarios
        $configuracionesDiarios = [
            ['tipo_credito' => 'diario', 'duracion_dias' => 26, 'interes_porcentaje' => 37.14],  // 26 días = ~3.7 semanas
            ['tipo_credito' => 'diario', 'duracion_dias' => 32, 'interes_porcentaje' => 45.71],  // 32 días = ~4.6 semanas
            ['tipo_credito' => 'diario', 'duracion_dias' => 38, 'interes_porcentaje' => 54.29],  // 38 días = ~5.4 semanas
            ['tipo_credito' => 'diario', 'duracion_dias' => 45, 'interes_porcentaje' => 64.29],  // 45 días = ~6.4 semanas
            ['tipo_credito' => 'diario', 'duracion_dias' => 52, 'interes_porcentaje' => 74.29],  // 52 días = ~7.4 semanas
        ];

        // Crear configuraciones de intereses para créditos semanales
        $configuracionesSemanales = [
            ['tipo_credito' => 'semanal', 'duracion_dias' => 7, 'interes_porcentaje' => 10.0],  // 1 semana
            ['tipo_credito' => 'semanal', 'duracion_dias' => 14, 'interes_porcentaje' => 20.0],  // 2 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 21, 'interes_porcentaje' => 30.0],  // 3 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 28, 'interes_porcentaje' => 40.0],  // 4 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 35, 'interes_porcentaje' => 50.0],  // 5 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 42, 'interes_porcentaje' => 60.0],  // 6 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 49, 'interes_porcentaje' => 70.0],  // 7 semanas
            ['tipo_credito' => 'semanal', 'duracion_dias' => 56, 'interes_porcentaje' => 80.0],  // 8 semanas
        ];

        // Crear configuraciones de intereses para créditos quincenales
        $configuracionesQuincenales = [
            ['tipo_credito' => 'quincenal', 'duracion_dias' => 14, 'interes_porcentaje' => 20.0],  // 1 quincena
            ['tipo_credito' => 'quincenal', 'duracion_dias' => 28, 'interes_porcentaje' => 40.0],  // 2 quincenas
            ['tipo_credito' => 'quincenal', 'duracion_dias' => 42, 'interes_porcentaje' => 60.0],  // 3 quincenas
            ['tipo_credito' => 'quincenal', 'duracion_dias' => 56, 'interes_porcentaje' => 80.0],  // 4 quincenas
        ];

        // Crear configuraciones de intereses para créditos de contado
        $configuracionesContado = [
            ['tipo_credito' => 'contado', 'duracion_dias' => 7, 'interes_porcentaje' => 10.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 14, 'interes_porcentaje' => 20.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 21, 'interes_porcentaje' => 30.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 28, 'interes_porcentaje' => 40.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 35, 'interes_porcentaje' => 50.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 42, 'interes_porcentaje' => 60.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 49, 'interes_porcentaje' => 70.0],
            ['tipo_credito' => 'contado', 'duracion_dias' => 56, 'interes_porcentaje' => 80.0],
        ];

        $todasConfiguraciones = array_merge(
            $configuracionesDiarios,
            $configuracionesSemanales,
            $configuracionesQuincenales,
            $configuracionesContado
        );

        foreach ($todasConfiguraciones as $config) {
            ConfiguracionIntereses::create(array_merge($config, ['activo' => true]));
        }
    }
}