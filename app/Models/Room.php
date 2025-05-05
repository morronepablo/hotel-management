<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_number', 'room_type_id', 'level_id', 'status'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function cleanings()
    {
        return $this->hasMany(Cleaning::class);
    }
}
