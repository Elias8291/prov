<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;
    protected $table = 'direccion';
    protected $fillable = [
        'codigo_postal',
        'asentamiento_id',
        'calle',
        'numero_exterior',
        'numero_interior',
        'entre_calle_1',
        'entre_calle_2',
    ];

    public function asentamiento()
    {
        return $this->belongsTo(Asentamiento::class);
    }
}