<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ELIMINAR esta línea:
// use Illuminate\Database\Eloquent\SoftDeletes;

class InstrumentoNotarial extends Model
{
    // MODIFICAR esta línea (quitar SoftDeletes):
    use HasFactory;

    protected $table = 'instrumento_notarial';

    protected $fillable = [
        'numero_escritura',
        'fecha',
        'nombre_notario',
        'numero_notario',
        'estado_id',
        'registro_mercantil',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_registro' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con Estado (muchos a uno)
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // Relación con DatoConstitutivo (uno a uno, inversa)
    public function datoConstitutivo()
    {
        return $this->hasOne(DatoConstitutivo::class, 'instrumento_notarial_id');
    }

    // Relación con RepresentanteLegal (uno a muchos)
    public function representantesLegales()
    {
        return $this->hasMany(RepresentanteLegal::class, 'instrumento_notarial_id');
    }

    // Relación con ModificacionEstatuto (uno a muchos)
    public function modificacionesEstatuto()
    {
        return $this->hasMany(ModificacionEstatuto::class, 'instrumento_notarial_id');
    }
}