<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Console\Command;

class MigrateRoomTypes extends Command
{
    protected $signature = 'migrate:room-types';
    protected $description = 'Migrate room types from rooms.type to room_types table';

    public function handle()
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            if ($room->type) {
                $roomType = RoomType::where('name', $room->type)->first();
                if ($roomType) {
                    $room->room_type_id = $roomType->id;
                    $room->save();
                }
            }
        }

        $this->info('Room types migrated successfully!');
    }
}
