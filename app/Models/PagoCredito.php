<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PagoCredito extends Model
{
    use HasFactory;

    protected $table = 'pagos_credito';

    protected $fillable = [
        'credito_id',
        'monto',
        'monto_original',
        'descuento',
        'recargo',
        'cuotas_afectadas',
        'fecha_pago',
        'metodo_pago',
        'tipo_pago',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date'
    ];

    // Relaciones
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'credito_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
    }

    public function scopePorCredito($query, $creditoId)
    {
        return $query->where('credito_id', $creditoId);
    }

    // Métodos
    public function actualizarCredito(): void
    {
        $this->credito?->actualizarSaldo();
    }
}
