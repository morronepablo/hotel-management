<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'lastname',
        'tipo_id',
        'nro_documento',
        'nro_matricula',
        'email',
        'phone',
        'address',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_id');
    }
}
