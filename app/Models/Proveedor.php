<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    
    protected $primaryKey = 'pv';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    protected $fillable = [
        'pv',
        'solicitante_id',
        'fecha_registro',
        'fecha_vencimiento',
        'estado',
        'observaciones',
    ];
    
    protected $casts = [
        'fecha_registro' => 'date',
        'fecha_vencimiento' => 'date',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Relación con el modelo Solicitante
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Solicitante::class, 'solicitante_id', 'id');
    }
    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'tramite_id', 'id');
    }
}