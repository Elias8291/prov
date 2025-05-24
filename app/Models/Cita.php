<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'cita';
    protected $fillable = ['solicitante_id', 'tramite_id', 'fecha_cita', 'hora_cita', 'estado', 'observaciones'];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    protected $casts = [
        'fecha_cita' => 'date',
    ];
}