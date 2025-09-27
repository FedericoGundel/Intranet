<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'id_cliente',
        'nombre',
        'apellido1',
        'apellido2',
        'telefono',
        'email',
        'tipo_cliente',
        'nif',
        'codigo_postal',
        'pais',
        'provincia',
        'localidad',
        'numero',
        'fecha',
        'fecha_vencimiento',
        'condiciones',
        'vendedor_id',
    ];

    // Relaciones
    public function articuloFacturas()
    {
        return $this->hasMany(ArticuloFactura::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    // Relación
    public function pagos()
    {
        // excluye soft-deleted por defecto
        return $this->hasMany(\App\Models\Pago::class);
    }

    // Suma de pagos (no anulados)
    public function getPagadoAttribute(): float
    {
        return (float) $this->pagos()->sum('monto');
    }

    // Saldo calculado
    public function getSaldoAttribute(): float
    {
        $saldo = ((float)$this->total) - $this->pagado;
        return $saldo > 0 ? $saldo : 0.0;
    }

    // Estado virtual
    public function getEstadoAttribute(): string
    {
        if ($this->saldo <= 0) return 'pagada';
        if ($this->pagado > 0) return 'parcial';
        return 'emitida';
    }
    public function vendedor(): BelongsTo
    {
        // belongsTo Vendedor vía vendedor_id -> vendedores.empleado_id
        return $this->belongsTo(Vendedor::class, 'vendedor_id', 'empleado_id');
    }

    public function empleadoVendedor(): BelongsTo
    {
        // acceso directo al Empleado asociado al vendedor
        return $this->belongsTo(Empleado::class, 'vendedor_id', 'id');
    }
    // Para que salgan en JSON automáticamente:
    protected $appends = ['pagado', 'saldo', 'estado'];
}