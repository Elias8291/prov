<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ELIMINAR esta línea:
// use Illuminate\Database\Eloquent\SoftDeletes;

class DatoConstitutivo extends Model
{
    // MODIFICAR esta línea (quitar SoftDeletes):
    use HasFactory;

    protected $table = 'datos_constitutivo';

    protected $fillable = [
        'instrumento_notarial_id',
        'objeto_social',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con InstrumentoNotarial (uno a uno)
    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class, 'instrumento_notarial_id');
    }

    // Relación con DetalleTramite (uno a uno, inversa)
    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class, 'dato_constitutivo_id');
    }

    // Relación con ModificacionEstatuto (uno a muchos)
    public function modificacionesEstatuto()
    {
        return $this->hasMany(ModificacionEstatuto::class, 'dato_constitutivo_id');
    }
}