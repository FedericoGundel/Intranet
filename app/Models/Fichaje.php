<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fichaje extends Model
{
    use HasFactory;

    protected $table = 'fichajes';

    protected $fillable = [
        'user_id',
        'fecha_entrada',
        'fecha_salida',
        'ultima_entrada',
        'ultima_salida',
        'tiempo_descanso',
        'localizacion',
    ];

    protected $casts = [
        'fecha_entrada'   => 'datetime',
        'fecha_salida'    => 'datetime',
        'ultima_entrada'  => 'datetime',
        'ultima_salida'   => 'datetime',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // app/Models/Fichaje.php
public function getEstadoAttribute(): string
{
    if ($this->fecha_salida) return 'finalizado';
    // sin fecha_salida => activo
    $pausado = $this->ultima_salida && (!$this->ultima_entrada || $this->ultima_salida->gt($this->ultima_entrada));
    return $pausado ? 'pausado' : 'trabajando';
}

// app/Models/Fichaje.php
public function getMinutosTrabajadosAttribute(): int
{
    if (!$this->fecha_entrada) return 0;

    // si estaba pausado al cerrar, ya sumaste el descanso pendiente
    $fin = $this->fecha_salida ?? now();
    return $this->fecha_entrada->diffInMinutes($fin) - ($this->tiempo_descanso ?? 0);
}


}
