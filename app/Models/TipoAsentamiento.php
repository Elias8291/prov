<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAsentamiento extends Model
{
    protected $table = 'tipo_asentamiento';
        protected $fillable = ['nombre'];

    public function asentamientos()
    {
        return $this->hasMany(Asentamiento::class);
    }
}
