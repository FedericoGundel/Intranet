<?php

namespace App\Observers;

use App\Models\ArticuloFactura;
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;

class ArticuloFacturaObserver
{
    public function created(ArticuloFactura $item): void
    {
        if (!$item->id_articulo || !$item->cantidad) return;

        // Bloqueo de fila para evitar carreras
        $art = Articulo::whereKey($item->id_articulo)->lockForUpdate()->first();
        if ($art) {
            $art->disminuirStock($item->cantidad);
        }
    }

    public function updated(ArticuloFactura $item): void
    {
        // Si cambió el artículo o la cantidad, ajustar la diferencia
        $oldArticuloId = $item->getOriginal('id_articulo');
        $oldCantidad   = (int) $item->getOriginal('cantidad');
        $newArticuloId = $item->id_articulo;
        $newCantidad   = (int) $item->cantidad;

        if ($oldArticuloId !== $newArticuloId) {
            // Devolver stock al viejo artículo
            if ($oldArticuloId) {
                $old = Articulo::whereKey($oldArticuloId)->lockForUpdate()->first();
                if ($old) $old->aumentarStock($oldCantidad);
            }
            // Descontar del nuevo
            if ($newArticuloId) {
                $new = Articulo::whereKey($newArticuloId)->lockForUpdate()->first();
                if ($new) $new->disminuirStock($newCantidad);
            }
            return;
        }

        // Mismo artículo, cambió cantidad -> aplicar delta
        $delta = $newCantidad - $oldCantidad;
        if ($delta === 0 || !$newArticuloId) return;

        $art = Articulo::whereKey($newArticuloId)->lockForUpdate()->first();
        if (!$art) return;

        if ($delta > 0) {
            $art->disminuirStock($delta);
        } else {
            $art->aumentarStock(abs($delta));
        }
    }

    public function deleted(ArticuloFactura $item): void
    {
        if (!$item->id_articulo || !$item->cantidad) return;

        $art = Articulo::whereKey($item->id_articulo)->lockForUpdate()->first();
        if ($art) {
            $art->aumentarStock($item->cantidad);
        }
    }
}
