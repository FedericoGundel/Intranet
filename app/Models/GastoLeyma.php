<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GastoLeyma extends Model
{
    use HasFactory;

    protected $table = 'gastos_leyma';

    protected $fillable = [
        'categoria',
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
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    public function scopeMauro($query)
    {
        return $query->porCategoria('mauro');
    }

    public function scopeGota($query)
    {
        return $query->porCategoria('gota');
    }

    public function scopeInversion($query)
    {
        return $query->porCategoria('inversion');
    }

    // Métodos estáticos
    public static function getTotalPorCategoria($categoria, $fechaInicio, $fechaFin)
    {
        return static::porCategoria($categoria)->porFecha($fechaInicio, $fechaFin)->sum('monto');
    }

    public static function getTotalPorPeriodo($fechaInicio, $fechaFin)
    {
        return static::porFecha($fechaInicio, $fechaFin)->sum('monto');
    }

    public static function getResumenPorCategoria($fechaInicio, $fechaFin)
    {
        return [
            'mauro' => static::getTotalPorCategoria('mauro', $fechaInicio, $fechaFin),
            'gota' => static::getTotalPorCategoria('gota', $fechaInicio, $fechaFin),
            'inversion' => static::getTotalPorCategoria('inversion', $fechaInicio, $fechaFin),
            'total' => static::getTotalPorPeriodo($fechaInicio, $fechaFin)
        ];
    }
}
