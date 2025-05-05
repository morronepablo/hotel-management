<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'total',
        'estado',
    ];

    // Relación con Entry
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    // Relación con ConsumoDetalles
    public function detalles()
    {
        return $this->hasMany(ConsumoDetalle::class);
    }
}
