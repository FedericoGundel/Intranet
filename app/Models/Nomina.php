<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $fillable = [
        'empleado_id',
        'periodo',
        'desde',
        'hasta',
        'porcentaje_comision',
        'total_base',
        'total_comision',
        'ajustes',
        'total_calculado',
        'total_pagado',
        'saldo',
        'estado',
        'cerrada_at',
        'pagada_at',
        'meta',
    ];

    protected $casts = [
        'desde' => 'date',
        'hasta' => 'date',
        'meta'  => 'array',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function detalles()
    {
        return $this->hasMany(NominaDetalle::class);
    }
    public function pagos()
    {
        return $this->hasMany(PagoNomina::class);
    }
    // app/Models/Nomina.php
    public static function existeSolape(int $empleadoId, string $desde, string $hasta, ?int $excluirId = null): bool
    {
        // Rango solapa si: (desde_existente <= hasta_nueva) AND (hasta_existente >= desde_nueva)
        $q = static::query()
            ->where('empleado_id', $empleadoId)
            ->whereDate('desde', '<=', $hasta)
            ->whereDate('hasta', '>=', $desde);

        if ($excluirId) {
            $q->where('id', '!=', $excluirId);
        }

        // Si querés permitir solapar con “anuladas”, podrías filtrar por estado acá.
        // ->whereNotIn('estado', ['anulada'])

        return $q->exists();
    }
}
