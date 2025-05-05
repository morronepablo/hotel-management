<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypeTariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'name',
        'type',
        'duration',
        'hour_checkout',
        'price',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
