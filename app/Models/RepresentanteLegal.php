<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepresentanteLegal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'representante_legal';

    protected $fillable = [
        'instrumento_notarial_id',
        'nombre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con InstrumentoNotarial (muchos a uno)
    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class, 'instrumento_notarial_id');
    }
}