<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Cobrador extends Model
{
    // Laravel pluraliza en inglés, así que lo fijamos manualmente
    protected $table = 'cobradores';

    // PK = FK
    protected $primaryKey = 'empleado_id';

    public $incrementing = false;  // no autoincremental

    protected $keyType = 'int';  // unsignedBigInteger en DB

    protected $fillable = [
        'empleado_id',
        'comision_porcentaje',
    ];

    protected $casts = [
        'comision_porcentaje' => 'decimal:2',  // devuelve string con 2 decimales
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
