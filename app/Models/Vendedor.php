<?php
// app/Models/Vendedor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendedor extends Model
{
    protected $table = 'vendedores';
    protected $primaryKey = 'empleado_id';
    public $incrementing = false; // PK no autoincremental
    protected $keyType = 'int';

    protected $fillable = [
        'empleado_id',
        'meta_mensual',
        'comision_porcentaje'
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function facturas(): HasMany
    {
        // facturas.vendedor_id referencia a vendedores.empleado_id
        return $this->hasMany(Factura::class, 'vendedor_id', 'empleado_id');
    }
}