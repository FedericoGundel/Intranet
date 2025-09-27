<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\CargaArticulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CargaArticuloService
{
    /**
     * Crea una carga_articulo y DESCUESTA stock del artículo.
     */
    public function crear(array $payload): CargaArticulo
    {
        $payload['fecha'] = $payload['fecha'] ?? now()->toDateString();

        return DB::transaction(function () use ($payload) {
            // Bloquea el artículo para evitar condiciones de carrera
            $articulo = Articulo::whereKey($payload['id_articulo'])
                ->lockForUpdate()
                ->firstOrFail();

            $cantidad = (int) $payload['cantidad'];
            if ($cantidad <= 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad debe ser mayor a 0.',
                ]);
            }

            try {
                // Usa tu método del modelo
                $articulo->aumentarStock($cantidad);
            } catch (\Exception $e) {
                // Convertimos a error de validación (422)
                throw ValidationException::withMessages([
                    'stock' => $e->getMessage(),
                ]);
            }

            // Registramos la carga (egreso)
            $carga = CargaArticulo::create([
                'id_articulo' => $articulo->id,
                'cantidad'    => $cantidad,
                'fecha'       => $payload['fecha'],
            ]);

            return $carga->load('articulo');
        });
    }

    /**
     * (Opcional) Revertir una carga: elimina y DEVUELVE stock.
     */
    public function revertir(CargaArticulo $carga): void
    {
        DB::transaction(function () use ($carga) {
            $articulo = Articulo::whereKey($carga->id_articulo)
                ->lockForUpdate()
                ->firstOrFail();

            // Devuelve stock con tu método
            $articulo->disminuirStock($carga->cantidad);

            $carga->delete();
        });
    }

    /**
     * (Opcional) Actualizar cantidad de una carga ajustando stock por diferencia.
     */
}
