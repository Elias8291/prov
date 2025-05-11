<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $fillable = ['nombre', 'sector_id'];

    // Relación muchos a uno con Sector
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }
}