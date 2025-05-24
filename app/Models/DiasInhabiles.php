<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiasInhabiles extends Model
{
    protected $table = 'dias_inhabiles';
    protected $fillable = ['fecha_inicio', 'fecha_fin', 'descripcion'];
}