<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name'];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'level_id');
    }
}
