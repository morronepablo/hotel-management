<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Llamar a los seeders de permisos y roles
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // Crear un usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('12345678'),
            ]
        );
        $admin->assignRole('Administrador');

        // Opcional: Crear un usuario recepcionista
        $recepcionista = User::firstOrCreate(
            ['email' => 'recepcion@hotel.com'],
            [
                'name' => 'Recepcionista',
                'password' => bcrypt('12345678'),
            ]
        );
        $recepcionista->assignRole('Recepcionista');

        // Llamar a los seeders de niveles y tipos de habitación primero
        $this->call(LevelsSeeder::class);
        $this->call(RoomTypesSeeder::class);

        // Llamar a otros seeders (clientes, habitaciones, reservas, etc.)
        $this->call([
            TipoDocumentoSeeder::class,
            ClientSeeder::class,
            RoomSeeder::class,
            ReservationSeeder::class,
            StaffSeeder::class,
            HotelSettingsSeeder::class,
            UnidadMedidaSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            ServicioSeeder::class,
        ]);

        // Ejecutar los comandos para migrar datos de las columnas antiguas (si existen)
        Artisan::call('migrate:levels');
        $this->command->info(Artisan::output());

        Artisan::call('migrate:room-types');
        $this->command->info(Artisan::output());
    }
}
