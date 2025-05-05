<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'fecha',
        'descripcion',
        'clase',
        'room_id', // Agregamos room_id
        'monto',
        'efectivo',
        'mercadopago',
        'tarjeta',
        'transferencia',
        'usuario_id',
        'arqueo_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
        'efectivo' => 'decimal:2',
        'mercadopago' => 'decimal:2',
        'tarjeta' => 'decimal:2',
        'transferencia' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function arqueo()
    {
        return $this->belongsTo(Arqueo::class);
    }

    // Relación con Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
