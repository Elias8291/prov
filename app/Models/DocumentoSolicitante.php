<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoSolicitante extends Model
{
    use HasFactory;

    protected $table = 'documento_solicitante'; 

    protected $fillable = [
        'tramite_id',
        'documento_id',
        'fecha_entrega',
        'estado',
        'version_documento',
        'ruta_archivo',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}