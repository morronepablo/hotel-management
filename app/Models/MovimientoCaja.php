<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $fillable = [
        'arqueo_id',
        'tipo',
        'clase',
        'monto',
        'efectivo',
        'mercadopago',
        'tarjeta',
        'transferencia',
        'descripcion',
        'usuario_id',
        'created_at',
        'updated_at',
    ];

    public function arqueo()
    {
        return $this->belongsTo(Arqueo::class, 'arqueo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
