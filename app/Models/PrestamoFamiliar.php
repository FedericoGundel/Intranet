<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrestamoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'prestamos_familiares';

    protected $fillable = [
        'familiar_nombre',
        'familiar_dni',
        'familiar_telefono',
        'monto',
        'fecha_prestamo',
        'fecha_vencimiento',
        'estado',
        'monto_pagado',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_prestamo' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_pagado' => 'decimal:2'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin]);
    }

    // Accessors
    public function getSaldoPendienteAttribute()
    {
        return $this->monto - $this->monto_pagado;
    }

    public function getEstaVencidoAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        return now()->isAfter($this->fecha_vencimiento) && $this->saldo_pendiente > 0;
    }

    // Métodos
    public function actualizarEstado()
    {
        if ($this->saldo_pendiente <= 0) {
            $this->estado = 'pagado';
        } elseif ($this->esta_vencido) {
            $this->estado = 'vencido';
        } else {
            $this->estado = 'activo';
        }
        
        $this->save();
    }

    // Métodos estáticos
    public static function getTotalPorPeriodo($fechaInicio, $fechaFin)
    {
        return static::porFecha($fechaInicio, $fechaFin)->sum('monto');
    }
}
