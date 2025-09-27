<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfiguracionCredito extends Model
{
    use HasFactory;

    protected $table = 'configuracion_creditos';

    protected $fillable = [
        'tipo_credito',
        'porcentaje_base',
        'dias_minimos',
        'monto_minimo_base',
        'activo'
    ];

    protected $casts = [
        'porcentaje_base' => 'decimal:2',
        'monto_minimo_base' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_credito', $tipo);
    }

    // Métodos estáticos para obtener configuraciones
    public static function getConfiguracion($tipo)
    {
        return static::activos()->porTipo($tipo)->first();
    }

    public static function getPorcentajeBase($tipo)
    {
        $config = static::getConfiguracion($tipo);
        return $config ? $config->porcentaje_base : 0;
    }

    public static function getDiasMinimos($tipo)
    {
        $config = static::getConfiguracion($tipo);
        return $config ? $config->dias_minimos : 0;
    }

    public static function getMontoMinimoBase($tipo)
    {
        $config = static::getConfiguracion($tipo);
        return $config ? $config->monto_minimo_base : 4000.00;
    }
}
