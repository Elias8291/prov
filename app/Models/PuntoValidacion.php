<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoValidacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'documento_id',
        'descripcion',
        'cumplido',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}