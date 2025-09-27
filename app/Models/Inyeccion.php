<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inyeccion extends Model
{
    use HasFactory;

    protected $table = 'inyecciones';

    protected $fillable = [
        'monto',
        'concepto',
        'fecha',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // Métodos estáticos
    public static function getTotalPorPeriodo($fechaInicio, $fechaFin)
    {
        return static::porFecha($fechaInicio, $fechaFin)->sum('monto');
    }
}
