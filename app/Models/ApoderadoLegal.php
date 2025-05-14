<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoderadoLegal extends Model
{
    use HasFactory;

    protected $table = 'apoderados_legales';

    protected $fillable = [
        'nombre', 
        'instrumento_notarial_id'
    ];

    /**
     * Obtiene el instrumento notarial asociado al apoderado legal
     */
    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class, 'instrumento_notarial_id');
    }

    /**
     * Obtiene los detalles de trámite asociados a este apoderado legal
     */
    public function detallesTramites()
    {
        return $this->hasMany(DetalleTramite::class, 'apoderado_legal_id');
    }
}