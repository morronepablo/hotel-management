<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\RoomTypeTariff;
use Illuminate\Database\Seeder;

class RoomTypesSeeder extends Seeder
{
    public function run()
    {
        // Lista de tipos de habitación con sus descripciones y tarifas
        $roomTypes = [
            [
                'name' => 'Apart',
                'description' => 'Habitación básica con comodidades estándar.',
                'tariffs' => [
                    ['name' => 'P. HORA (2)', 'type' => 'HORA', 'duration' => 2, 'hour_checkout' => null, 'price' => 15000.00],
                    ['name' => 'P. HORA (4)', 'type' => 'HORA', 'duration' => 4, 'hour_checkout' => null, 'price' => 25000.00],
                    ['name' => 'NOCHE', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 30000.00],
                    ['name' => 'MES', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 15000.00],
                ],
            ],
            [
                'name' => 'Suite Summum',
                'description' => 'Habitación de lujo con servicios premium.',
                'tariffs' => [
                    ['name' => 'P. HORA (2)', 'type' => 'HORA', 'duration' => 2, 'hour_checkout' => null, 'price' => 25000.00],
                    ['name' => 'P. HORA (4)', 'type' => 'HORA', 'duration' => 4, 'hour_checkout' => null, 'price' => 45000.00],
                    ['name' => 'NOCHE', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 55000.00],
                    ['name' => 'MES', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 16000.00],
                ],
            ],
            [
                'name' => 'Duplex',
                'description' => 'Habitación de dos niveles con diseño moderno.',
                'tariffs' => [
                    ['name' => 'P. HORA (2)', 'type' => 'HORA', 'duration' => 2, 'hour_checkout' => null, 'price' => 30000.00],
                    ['name' => 'P. HORA (4)', 'type' => 'HORA', 'duration' => 4, 'hour_checkout' => null, 'price' => 50000.00],
                    ['name' => 'NOCHE', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 60000.00],
                    ['name' => 'MES', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 20000.00],
                ],
            ],
            [
                'name' => 'Duplex Suite',
                'description' => 'Suite de dos niveles con comodidades de lujo.',
                'tariffs' => [
                    ['name' => 'P. HORA (2)', 'type' => 'HORA', 'duration' => 2, 'hour_checkout' => null, 'price' => 35000.00],
                    ['name' => 'P. HORA (4)', 'type' => 'HORA', 'duration' => 4, 'hour_checkout' => null, 'price' => 60000.00],
                    ['name' => 'NOCHE', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 70000.00],
                    ['name' => 'MES', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 25000.00],
                ],
            ],
            [
                'name' => 'Duplex Summum',
                'description' => 'Habitación de dos niveles con diseño exclusivo y servicios premium.',
                'tariffs' => [
                    ['name' => 'P. HORA (2)', 'type' => 'HORA', 'duration' => 2, 'hour_checkout' => null, 'price' => 40000.00],
                    ['name' => 'P. HORA (4)', 'type' => 'HORA', 'duration' => 4, 'hour_checkout' => null, 'price' => 75000.00],
                    ['name' => 'NOCHE', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 95000.00],
                    ['name' => 'MES', 'type' => 'DIA', 'duration' => null, 'hour_checkout' => '12:00:00', 'price' => 30000.00],
                ],
            ],
        ];

        foreach ($roomTypes as $roomTypeData) {
            // Crear el tipo de habitación
            $roomType = RoomType::create([
                'name' => $roomTypeData['name'],
                'description' => $roomTypeData['description'],
            ]);

            // Crear las tarifas asociadas
            foreach ($roomTypeData['tariffs'] as $tariffData) {
                RoomTypeTariff::create([
                    'room_type_id' => $roomType->id,
                    'name' => $tariffData['name'],
                    'type' => $tariffData['type'],
                    'duration' => $tariffData['duration'],
                    'hour_checkout' => $tariffData['hour_checkout'],
                    'price' => $tariffData['price'],
                ]);
            }
        }
    }
}
