<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sector';
    protected $fillable = ['nombre'];

    // Relación uno a muchos con Actividad
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'sector_id');
    }
}