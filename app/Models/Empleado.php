<?php
// app/Models/Empleado.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    protected $table = 'empleados';
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'email',
        'telefono'
    ];

    public function vendedor(): HasOne
    {
        return $this->hasOne(Vendedor::class, 'empleado_id');
    }
public function cobrador(): HasOne
{
    return $this->hasOne(Cobrador::class, 'empleado_id');
}
    // Si una factura “guarda” directamente el vendedor_id = empleado_id del vendedor:
    public function facturasComoVendedor(): HasMany
    {
        return $this->hasMany(Factura::class, 'vendedor_id', 'id');
    }
    public function nominas()
    {
        return $this->hasMany(Nomina::class);
    }
    // helper rápido:
    public function getEsVendedorAttribute(): bool
    {
        return $this->vendedor()->exists();
    }
}