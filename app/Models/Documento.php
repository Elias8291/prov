<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;
    protected $table = 'documento';
  protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'fecha_expiracion',
        'es_visible',
        'tipo_persona',
    ];
    public function puntosValidacion()
    {
        return $this->hasMany(PuntoValidacion::class);
    }
}