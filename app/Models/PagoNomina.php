<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoNomina extends Model
{
    protected $fillable = ['nomina_id', 'fecha', 'importe', 'metodo', 'nota'];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }
}