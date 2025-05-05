<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de servicios con sus precios
        $servicios = [
            [
                'nombre' => 'Baño Privado',
                'precio' => 500.00,
            ],
            [
                'nombre' => 'Servicio Parqueo',
                'precio' => 500.00,
            ],
            [
                'nombre' => 'Wifi',
                'precio' => 100.00,
            ],
        ];

        // Insertar los servicios en la base de datos
        foreach ($servicios as $servicio) {
            Servicio::create([
                'nombre' => $servicio['nombre'],
                'precio' => $servicio['precio'],
            ]);
        }
    }
}
