<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DetalleTramite extends Model
{
    use HasFactory;
    protected $table = 'detalle_tramite';
    protected $fillable = [
        'tramite_id',
        'razon_social',
        'email',
        'telefono',
        'direccion_id',
        'contacto_id',
        'representante_legal_id',
        'dato_constitutivo_id',
        'sitio_web',
    ];

   public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'tramite_id', 'id');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }
    
}