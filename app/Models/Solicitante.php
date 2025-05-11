<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'solicitante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'tipo_persona',
        'curp',
        'rfc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tipo_persona' => 'string',
    ];

    /**
     * Get the user that owns the solicitante.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'solicitante_id');
    }
}