<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function roomTypeTariffs()
    {
        return $this->hasMany(RoomTypeTariff::class, 'room_type_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
