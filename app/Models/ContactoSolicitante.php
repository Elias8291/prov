<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactoSolicitante extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacto_solicitante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'puesto',
        'telefono',
        'email',
    ];

    /**
     * Get the detalle_tramite that owns this contact.
     */
    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class, 'contacto_id');
    }
}