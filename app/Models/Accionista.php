<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accionista extends Model
{
    use HasFactory;

    protected $table = 'accionista';
    
    protected $fillable = [
        'apellido_paterno',
        'apellido_materno',
        'nombre',
    ];

    /**
     * Relación con AccionistaSolicitante (uno a muchos)
     */
    public function accionistasSolicitante()
    {
        return $this->hasMany(AccionistaSolicitante::class, 'accionista_id');
    }
    
    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }
}