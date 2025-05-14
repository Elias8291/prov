<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTramite extends Model
{
    use HasFactory;

    protected $table = 'detalle_tramite';

    protected $fillable = [
        'tramite_id',
        'contacto_id',
        'direccion_id',
        'dato_constitutivo_id',
        'apoderado_legal_id',  // Añadir este campo
        'razon_social',
        'telefono',
        'sitio_web',
        'email'
    ];

    /**
     * Obtiene el trámite asociado al detalle
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    /**
     * Obtiene el contacto asociado al detalle del trámite
     */
    public function contacto()
    {
        return $this->belongsTo(ContactoSolicitante::class, 'contacto_id');
    }

    /**
     * Obtiene la dirección asociada al detalle del trámite
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    /**
     * Obtiene el dato constitutivo asociado al detalle del trámite
     */
    public function datoConstitutivo()
    {
        return $this->belongsTo(DatoConstitutivo::class, 'dato_constitutivo_id');
    }

    /**
     * Obtiene el apoderado legal asociado al detalle del trámite
     */
    public function apoderadoLegal()
    {
        return $this->belongsTo(ApoderadoLegal::class, 'apoderado_legal_id');
    }
}