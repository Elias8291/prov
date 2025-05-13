<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Tramite extends Model
{
    use HasFactory;

    protected $table = 'tramite'; // Especifica el nombre correcto de la tabla

    protected $fillable = [
        'solicitante_id',
        'tipo_tramite',
        'estado',
        'progreso_tramite',
        'revisado_por',
        'fecha_revision',
        'fecha_inicio',
        'fecha_finalizacion',
        'observaciones',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

 public function detalleTramite(): HasOne
    {
        return $this->hasOne(DetalleTramite::class, 'tramite_id', 'id');
    }

    public function documentosSolicitantes()
    {
        return $this->hasMany(DocumentoSolicitante::class);
    }
}