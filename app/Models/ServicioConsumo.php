<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioConsumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'total',
        'estado',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    // public function detalles()
    // {
    //     return $this->hasMany(ServicioConsumoDetalle::class, 'servicio_consumo_id');
    // }
    public function detalles()
    {
        return $this->hasMany(ServicioConsumoDetalle::class);
    }


    // Método para recalcular el total
    public function recalculateTotal()
    {
        $this->total = $this->detalles()->sum('subtotal');
        $this->save();
    }
}
