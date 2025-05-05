<?php

namespace Database\Seeders;

use App\Models\HotelSetting;
use Illuminate\Database\Seeder;

class HotelSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un registro inicial si no existe
        if (!HotelSetting::exists()) {
            HotelSetting::create([
                'nombre' => 'HOTEL Management',
                'direccion' => 'Poner direccion',
                'telefono' => 'poner telefono',
                'cuit' => 'poner cuit',
                'simbolo_monetario' => '$',
                'logo' => 'logo.jpg', // El logo se subirá desde la interfaz
            ]);
        }
    }
}
