<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    protected $fillable = [
        'entry_id',
        'room_id',
        'room_type_id',
        'client_id',
        'entry_type',
        'check_in',
        'check_out',
        'quantity',
        'discount',
        'efectivo',
        'mercadopago',
        'tarjeta',
        'transferencia',
        'total',
        'debt',
        'pago',
        'observations',
        'status',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }

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


    public function roomTypeTariff()
    {
        return $this->belongsTo(RoomTypeTariff::class, 'room_type_tariff_id');
    }
}
