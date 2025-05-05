<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'DNI', 'longitud' => 8],
            ['nombre' => 'PASAPORTE', 'longitud' => 15],
        ];

        foreach ($tipos as $tipo) {
            TipoDocumento::firstOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
