<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Unidad', 'valor_unidad' => 1],
            ['nombre' => 'Docena', 'valor_unidad' => 12],
            ['nombre' => 'Decena', 'valor_unidad' => 10],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate(['nombre' => $unidad['nombre']], $unidad);
        }
    }
}
