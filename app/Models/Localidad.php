<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{

protected $table = 'localidad';
    protected $fillable = ['municipio_id', 'nombre'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function asentamientos()
    {
        return $this->hasMany(Asentamiento::class);
    }
}
