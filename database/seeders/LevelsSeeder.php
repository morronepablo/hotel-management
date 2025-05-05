<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            ['name' => 'Piso 1'],
            ['name' => 'Piso 2'],
            ['name' => 'Piso 3'],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
