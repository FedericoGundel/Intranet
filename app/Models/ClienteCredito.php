<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteCredito extends Model
{
    use HasFactory;

    protected $table = 'clientes_credito';

    protected $fillable = [
        'nombre',
        'dni',
        'domicilio',
        'comercio_negocio',
        'telefono',
        'garante_nombre',
        'garante_dni',
        'garante_telefono',
        'garante_domicilio',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'estado' => 'string'
    ];

    // Relaciones
    public function creditos()
    {
        return $this->hasMany(Credito::class, 'cliente_id');
    }

    public function saldosFavor()
    {
        return $this->hasMany(SaldoFavor::class, 'cliente_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }

    public function getTieneGaranteAttribute()
    {
        return !empty($this->garante_nombre);
    }

    public function getGaranteCompletoAttribute()
    {
        if (!$this->tiene_garante) {
            return null;
        }

        return [
            'nombre' => $this->garante_nombre,
            'dni' => $this->garante_dni,
            'telefono' => $this->garante_telefono,
            'domicilio' => $this->garante_domicilio
        ];
    }

    // Métodos
    public function tieneCreditoActivo()
    {
        return $this->creditos()->activos()->exists();
    }

    public function getCreditoActivo()
    {
        return $this->creditos()->activos()->first();
    }

    public function getTotalPrestado()
    {
        return $this->creditos()->sum('monto_principal');
    }

    public function getTotalPagado()
    {
        return $this->creditos()->get()->sum(function ($credito) {
            return $credito->monto_pagado;
        });
    }

    public function getSaldoTotal()
    {
        return $this->getTotalPrestado() - $this->getTotalPagado();
    }
}
