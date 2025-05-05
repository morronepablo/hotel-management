<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'room_id',
        'client_id',
        'room_type_id',
        'room_type_id',
        'room_type_tariff_id',
        'tariff_id',
        'check_in',
        'check_out',
        'quantity',
        'payment_method',
        'discount',
        'payment_received',
        'efectivo',
        'mercadopago',
        'tarjeta',
        'transferencia',
        'total',
        'debt',
        'pago',
        'observations',
        'status',
        'salida',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function companions()
    {
        return $this->belongsToMany(Companion::class, 'entry_companion')->withTimestamps();
    }

    public function consumo()
    {
        return $this->hasOne(Consumo::class);
    }

    // Nueva relación: servicioConsumo
    // public function servicioConsumo()
    // {
    //     return $this->hasOne(ServicioConsumo::class, 'entry_id');
    // }
    public function servicioConsumo()
    {
        return $this->hasOne(ServicioConsumo::class);
    }

    public function renovations()
    {
        return $this->hasMany(Renewal::class, 'entry_id');
    }

    // This might be the current tariff relationship (which isn't working)
    public function roomTypeTariff()
    {
        return $this->belongsTo(RoomTypeTariff::class, 'room_type_tariff_id');
    }
}
