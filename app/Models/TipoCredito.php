<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCredito extends Model
{
    protected $table = 'tipos_credito';

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_maxima_dias',
        'interes_semanal',
        'interes_maximo',
        'requiere_garante',
        'activo'
    ];

    protected $casts = [
        'requiere_garante' => 'boolean',
        'activo' => 'boolean',
        'interes_semanal' => 'decimal:2',
        'interes_maximo' => 'decimal:2'
    ];

    // Relaciones
    public function creditos()
    {
        return $this->hasMany(Credito::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Métodos estáticos para obtener tipos específicos
    public static function diario()
    {
        return self::where('nombre', 'diario')->first();
    }

    public static function semanal()
    {
        return self::where('nombre', 'semanal')->first();
    }

    public static function quincenal()
    {
        return self::where('nombre', 'quincenal')->first();
    }

    public static function contado()
    {
        return self::where('nombre', 'contado')->first();
    }
}
