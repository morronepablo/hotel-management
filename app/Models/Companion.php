<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lastname',
        'dni',
        'phone',
        'email',
    ];

    // Relación con entradas
    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_companion')->withTimestamps();
    }
}
