<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioConsumoDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'servicio_consumo_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'estado',
        'forma_pago',
        'vendido',
    ];

    public function servicioConsumo()
    {
        return $this->belongsTo(ServicioConsumo::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
