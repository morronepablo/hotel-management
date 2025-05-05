<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoDetalle extends Model
{
    protected $fillable = [
        'consumo_id',
        'producto_id',
        'cantidad',
        'precio',
        'subtotal',
        'estado',
        'forma_pago',
    ];

    public function consumo()
    {
        return $this->belongsTo(Consumo::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
