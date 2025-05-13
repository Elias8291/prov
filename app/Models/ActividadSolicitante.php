<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadSolicitante extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'actividad_solicitante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tramite_id',
        'actividad_id',
    ];

    /**
     * Get the tramite that owns this record.
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Get the actividad associated with this record.
     */
    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}