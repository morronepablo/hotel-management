<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arqueo extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'fecha_apertura',
        'monto_inicial',
        'fecha_cierre',
        'monto_final',
        'ventas_efectivo',
        'ventas_tarjeta',
        'ventas_mercadopago',
        'descripcion',
        'usuario_id',
    ];

    protected $appends = ['total_ingresos', 'total_egresos'];

    public function getTotalIngresosAttribute()
    {
        return $this->movimientos->where('tipo', 'Ingreso')->sum('monto');
    }

    public function getTotalEgresosAttribute()
    {
        return $this->movimientos->where('tipo', 'Egreso')->sum('monto');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
