<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
 protected $fillable = [
    'nombre',
    'apellido1',
    'apellido2',
    'tipo_cliente',
    'nif',
    'direccion',
    'codigo_postal',
    'localidad',
    'provincia',
    'pais',
    'email',
    'telefono',
];
public function facturas()
{
    return $this->hasMany(Factura::class, 'id_cliente');
}


}
