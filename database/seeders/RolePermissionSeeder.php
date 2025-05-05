<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'ver-dashboard',
            'ver-clientes',
            'ver-habitaciones',
            'ver-reservas',
            'ver-facturas',
            'ver-roles',
            'ver-permisos',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear rol Administrador y asignar permisos
        $adminRole = Role::create(['name' => 'Administrador']);
        $adminRole->givePermissionTo($permissions);

        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('12345678'),
        ]);

        // Asignar rol al usuario
        $admin->assignRole('Administrador');
    }
}
