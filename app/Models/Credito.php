<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    protected $table = 'creditos';

    protected $fillable = [
        'cliente_id',
        'numero_credito',
        'monto_principal',
        'tipo_pago',
        'porcentaje_base',
        'cantidad_cuotas',
        'fecha_inicio',
        'fecha_vencimiento',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'monto_principal' => 'decimal:2',
        'porcentaje_base' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(ClienteCredito::class, 'cliente_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoCredito::class, 'credito_id');
    }

    public function recargos()
    {
        return $this->hasMany(Recargo::class, 'credito_id');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'credito_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes - Simplificados para evitar problemas con monto_total dinámico
    public function scopeActivos($query)
    {
        return $query->whereRaw('
            (SELECT COALESCE(SUM(monto), 0) FROM pagos_credito WHERE credito_id = creditos.id) < 
            (monto_principal + (monto_principal * porcentaje_base / 100))
        ');
    }

    public function scopePagados($query)
    {
        return $query->whereRaw('
            (SELECT COALESCE(SUM(monto), 0) FROM pagos_credito WHERE credito_id = creditos.id) >= 
            (monto_principal + (monto_principal * porcentaje_base / 100))
        ');
    }

    public function scopeVencidos($query)
    {
        return $query
            ->where('fecha_vencimiento', '<', now())
            ->whereRaw('
                (SELECT COALESCE(SUM(monto), 0) FROM pagos_credito WHERE credito_id = creditos.id) < 
                (monto_principal + (monto_principal * porcentaje_base / 100))
            ');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    // Accessors
    public function getMontoPagadoAttribute()
    {
        return $this->pagos()->sum('monto') ?? 0;
    }

    public function getMontoTotalAttribute()
    {
        // Base del crédito (capital + interés según porcentaje_base)
        $baseTotal = $this->base_total;

        // Ajustes (recargos - descuentos) — aplicables siempre
        // Los descuentos reducen el monto total, los recargos lo aumentan
        $ajustes = ($this->total_recargos ?? 0) - ($this->total_descuentos ?? 0);

        return $baseTotal + $ajustes;
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->monto_total - $this->monto_pagado;
    }

    // --- Nuevos accessors de apoyo ---
    public function getBaseTotalAttribute()
    {
        if (!$this->monto_principal) {
            return 0;
        }
        $porcentajeBase = $this->porcentaje_base ?? 0;
        $montoInteres = $this->monto_principal * ($porcentajeBase / 100);
        return $this->monto_principal + $montoInteres;
    }

    public function getFechaVencimientoAttribute()
    {
        // Si ya existe fecha_vencimiento almacenada, usarla (para créditos contado)
        if ($this->attributes['fecha_vencimiento'] ?? null) {
            return Carbon::parse($this->attributes['fecha_vencimiento']);
        }

        // Si no existe, calcularla dinámicamente (para otros tipos)
        if (!$this->fecha_inicio || !$this->cantidad_cuotas) {
            return null;
        }

        $diasCredito = $this->calcularDiasCredito();
        return Carbon::parse($this->fecha_inicio)->addDays($diasCredito);
    }

    public function getEstadoAttribute()
    {
        if ($this->saldo_pendiente <= 0) {
            return 'pagado';
        } elseif ($this->esta_vencido) {
            return 'vencido';
        } else {
            return 'activo';
        }
    }

    public function getMontoInteresAttribute()
    {
        return $this->monto_total - $this->monto_principal;
    }

    public function getProgresoPagoAttribute()
    {
        if ($this->monto_total == 0) {
            return 0;
        }
        return round(($this->monto_pagado / $this->monto_total) * 100, 2);
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getPorcentajeFinalAttribute()
    {
        // Usar porcentaje_base directamente (ya no se calcula automáticamente)
        return $this->porcentaje_base ?? 0;
    }

    public function getDiasCreditoAttribute()
    {
        return $this->calcularDiasCredito();
    }

    public function getEstaVencidoAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        return Carbon::now()->isAfter($this->fecha_vencimiento) && $this->saldo_pendiente > 0;
    }

    public function getTotalRecargosAttribute()
    {
        return $this->recargos()->sum('monto');
    }

    public function getTotalDescuentosAttribute()
    {
        return $this->descuentos()->sum('monto');
    }

    // Métodos
    public function actualizarSaldo()
    {
        // Ya no necesitamos actualizar campos en la base de datos
        // Los valores se calculan dinámicamente con los accessors
        // Este método se mantiene por compatibilidad pero no hace nada
        return true;
    }

    public function calcularDiasCredito()
    {
        if (!$this->cantidad_cuotas) {
            return 0;  // Sin cuotas, sin días
        }

        switch ($this->tipo_pago) {
            case 'diario':
                return $this->cantidad_cuotas;
            case 'semanal':
                return $this->cantidad_cuotas * 7;
            case 'quincenal':
                return $this->cantidad_cuotas * 15;
            case 'contado':
                // Calcular días basado en fechas para contado
                if ($this->fecha_inicio && $this->fecha_vencimiento) {
                    return $this->fecha_inicio->diffInDays($this->fecha_vencimiento);
                }
                return 7;  // Por defecto 7 días si no hay fechas
            default:
                return $this->cantidad_cuotas;
        }
    }

    public function calcularMontoTotal()
    {
        $porcentajeBase = $this->porcentaje_base ?? 0;
        $montoInteres = $this->monto_principal * ($porcentajeBase / 100);

        return $this->monto_principal + $montoInteres;
    }

    public function generarNumeroCredito()
    {
        $prefijo = 'CR';
        $año = date('Y');
        $ultimoCredito = static::whereYear('created_at', $año)
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimoCredito ? $ultimoCredito->id + 1 : 1;

        return $prefijo . $año . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
