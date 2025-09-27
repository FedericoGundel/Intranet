<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recargo extends Model
{
    use HasFactory;

    protected $table = 'recargos';

    protected $fillable = [
        'credito_id',
        'monto',
        'concepto',
        'fecha_aplicacion',
        'observaciones',
        'usuario_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_aplicacion' => 'date'
    ];

    // Relaciones
    public function credito()
    {
        return $this->belongsTo(Credito::class, 'credito_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin]);
    }

    public function scopePorCredito($query, $creditoId)
    {
        return $query->where('credito_id', $creditoId);
    }

    // Métodos
    public function actualizarCredito()
    {
        // Los totales se calculan dinámicamente en el modelo Credito (accessors)
        // Solo refrescamos la instancia para reflejar nuevos ajustes
        $this->credito?->refresh();
        return $this->credito;
    }
}
