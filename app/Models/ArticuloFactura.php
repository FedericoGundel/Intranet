<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticuloFactura extends Model
{
    use HasFactory;

    protected $table = 'articulo_factura';

    protected $fillable = [
        'factura_id',
        'id_articulo',
        'nombre',
        'cantidad',
        'precio',
        'impuesto',
        'descripcion',
        'descuento',
        'total'
    ];

    // Relaciones
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'id_articulo');
    }
}
