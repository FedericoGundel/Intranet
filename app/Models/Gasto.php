<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;

    protected $table = 'gastos';

    protected $fillable = [
        'fecha',
        'categoria',
        'subcategoria',
        'monto',
        'metodo_pago',
        'descripcion',
        'empleado_id',
        'nomina_pago_id',
        'comprobante_path',
    ];

    // Relaciones
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function pagoNomina()
    {
        // FK: gastos.nomina_pago_id -> pago_nominas.id
        return $this->belongsTo(PagoNomina::class, 'nomina_pago_id');
    }
}
