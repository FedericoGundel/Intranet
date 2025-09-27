<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaArticulo extends Model
{
    use HasFactory;

    protected $table = 'carga_articulo';

    protected $fillable = [
        'id_articulo',
        'cantidad',
        'fecha',
    ];

    // Relación con Articulo (si tenés esa tabla)
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'id_articulo');
    }
}
