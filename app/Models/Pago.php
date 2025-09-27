<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'factura_id', 'numero', 'fecha', 'monto', 'metodo', 'referencia', 'notas', 'cobrador_id'
    ];

    public function factura()
    {
        return $this->belongsTo(\App\Models\Factura::class);
    }
}
