<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionIntereses extends Model
{
    protected $table = 'configuracion_intereses';

    protected $fillable = [
        'tipo_credito',
        'duracion_dias',
        'interes_porcentaje',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'interes_porcentaje' => 'decimal:2'
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
