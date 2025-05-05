<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cleaning extends Model
{
    protected $fillable = ['room_id', 'staff_id', 'cleaning_type', 'start_time', 'end_time', 'status'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
