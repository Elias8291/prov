<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asentamiento extends Model
{
    protected $fillable = ['nombre', 'codigo_postal', 'localidad_id', 'tipo_asentamiento_id'];

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function tipoAsentamiento()
    {
        return $this->belongsTo(TipoAsentamiento::class);
    }
}
