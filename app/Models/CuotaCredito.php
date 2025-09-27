<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CuotaCredito extends Model
{
    protected $table = 'cuotas_credito';

    protected $fillable = [
        'credito_id',
        'numero_cuota',
        'monto_cuota',
        'fecha_vencimiento',
        'fecha_pago',
        'monto_pagado',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'monto_cuota' => 'decimal:2',
        'monto_pagado' => 'decimal:2'
    ];

    // Relaciones
    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoCredito::class);
    }

    // Accessors
    public function getSaldoPendienteAttribute()
    {
        return $this->monto_cuota - $this->monto_pagado;
    }

    public function getEstaVencidaAttribute()
    {
        return Carbon::now()->isAfter($this->fecha_vencimiento) && $this->estado !== 'pagada';
    }

    public function getDiasVencidaAttribute()
    {
        if (!$this->esta_vencida)
            return 0;
        return Carbon::now()->diffInDays($this->fecha_vencimiento);
    }

    public function getEstaParcialAttribute()
    {
        return $this->monto_pagado > 0 && $this->monto_pagado < $this->monto_cuota;
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVencidas($query)
    {
        return $query
            ->where('estado', '!=', 'pagada')
            ->where('fecha_vencimiento', '<', Carbon::now());
    }

    public function scopePorVencer($query, $dias = 7)
    {
        return $query
            ->where('estado', 'pendiente')
            ->whereBetween('fecha_vencimiento', [Carbon::now(), Carbon::now()->addDays($dias)]);
    }

    // Métodos
    public function registrarPago($monto, $fechaPago = null, $metodoPago = null, $observaciones = null)
    {
        $fechaPago = $fechaPago ?: Carbon::now();

        // Crear el pago
        $pago = $this->pagos()->create([
            'credito_id' => $this->credito_id,
            'monto' => $monto,
            'fecha_pago' => $fechaPago,
            'metodo_pago' => $metodoPago,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id()
        ]);

        // Actualizar la cuota
        $this->monto_pagado += $monto;

        if ($this->monto_pagado >= $this->monto_cuota) {
            $this->estado = 'pagada';
            $this->fecha_pago = $fechaPago;
        } elseif ($this->monto_pagado > 0) {
            $this->estado = 'parcial';
        }

        $this->save();

        // Actualizar el saldo del crédito
        $this->credito->actualizarSaldo();

        return $pago;
    }

    public function actualizarEstado()
    {
        if ($this->monto_pagado >= $this->monto_cuota) {
            $this->estado = 'pagada';
        } elseif ($this->monto_pagado > 0) {
            $this->estado = 'parcial';
        } elseif ($this->esta_vencida) {
            $this->estado = 'vencida';
        } else {
            $this->estado = 'pendiente';
        }

        $this->save();
    }
}
