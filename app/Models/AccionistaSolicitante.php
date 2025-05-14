<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionistaSolicitante extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accionista_solicitante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'accionista_id',
        'tramite_id',
        'porcentaje_participacion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'porcentaje_participacion' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the accionista that owns the record.
     */
    public function accionista()
    {
        return $this->belongsTo(Accionista::class, 'accionista_id');
    }

    /**
     * Get the tramite that owns the record.
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }
}