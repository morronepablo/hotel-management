<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Level;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            ['room_number' => '100', 'type' => 'Suite Summum', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '101', 'type' => 'Apart', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '102', 'type' => 'Apart', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '103', 'type' => 'Apart', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '104', 'type' => 'Apart', 'level' => 'Piso 1', 'status' => 'Para la Limpieza'],
            ['room_number' => '105', 'type' => 'Duplex', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '106', 'type' => 'Duplex', 'level' => 'Piso 1', 'status' => 'Para la Limpieza'],
            ['room_number' => '107', 'type' => 'Duplex Suite', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '108', 'type' => 'Duplex Suite', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '109', 'type' => 'Duplex Summum', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '110', 'type' => 'Duplex Summum', 'level' => 'Piso 1', 'status' => 'Disponible'],
            ['room_number' => '111', 'type' => 'Apart', 'level' => 'Piso 1', 'status' => 'Disponible'],
        ];

        foreach ($rooms as $roomData) {
            $level = Level::where('name', $roomData['level'])->first();
            $roomType = RoomType::where('name', $roomData['type'])->first();

            // Validar que el tipo de habitación exista
            if (!$roomType) {
                throw new \Exception("Tipo de habitación '{$roomData['type']}' no encontrado para la habitación {$roomData['room_number']}.");
            }

            // Validar que el nivel exista
            if (!$level) {
                throw new \Exception("Nivel '{$roomData['level']}' no encontrado para la habitación {$roomData['room_number']}.");
            }

            Room::create([
                'room_number' => $roomData['room_number'],
                'level_id' => $level->id,
                'room_type_id' => $roomType->id,
                'status' => $roomData['status'],
            ]);
        }
    }
}
