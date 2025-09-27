<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaEmail extends Model
{
    protected $fillable = ['nombre', 'asunto', 'cuerpo'];
}

