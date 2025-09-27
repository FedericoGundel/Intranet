<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'stock',
        'descuento',
        'descripcion',
        'codigo',
        'precio',
        'imagen',
    ];
    public function articuloFacturas()
{
    return $this->hasMany(ArticuloFactura::class, 'id_articulo');
}

public function disminuirStock($cantidad)
{
    if ($this->stock < $cantidad) {
        throw new \Exception("Stock insuficiente para {$this->nombre}");
    }
    $this->stock -= $cantidad;
    $this->save();
}

public function aumentarStock($cantidad)
{
    $this->stock += $cantidad;
    $this->save();
}


}
