<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\Level;
use Illuminate\Console\Command;

class MigrateLevels extends Command
{
    protected $signature = 'migrate:levels';
    protected $description = 'Migrate levels from rooms.level to levels table';

    public function handle()
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            if ($room->level) {
                $level = Level::where('name', $room->level)->first();
                if ($level) {
                    $room->level_id = $level->id;
                    $room->save();
                }
            }
        }

        $this->info('Levels migrated successfully!');
    }
}
