<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    public function run()
    {
        Staff::create([
            'nombre' => 'PERSONAL DE LIMPIEZA',
            'telefono' => '9999999999',
        ]);
    }
}
