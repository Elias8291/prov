<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModificacionEstatuto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modificacion_estatuto';

    protected $fillable = [
        'dato_constitutivo_id',
        'instrumento_notarial_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con DatoConstitutivo (muchos a uno)
    public function datoConstitutivo()
    {
        return $this->belongsTo(DatoConstitutivo::class, 'dato_constitutivo_id');
    }

    // Relación con InstrumentoNotarial (muchos a uno)
    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class, 'instrumento_notarial_id');
    }
}