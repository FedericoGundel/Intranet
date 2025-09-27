<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominaDetalle extends Model
{
    protected $fillable = [
        'nomina_id',
        'factura_id',
        'tipo',
        'descripcion',
        'monto_base',
        'porcentaje',
        'comision_calculada'
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }
}