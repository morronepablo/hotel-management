<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear el rol Administrador
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all()); // Asignar todos los permisos al Administrador

        // Crear el rol Recepcionista
        $recepcionista = Role::firstOrCreate(['name' => 'Recepcionista']);
        $recepcionista->syncPermissions([
            'ver-dashboard',
            'ver-reservas',
            'crear-reservas',
            'editar-reservas',
            'eliminar-reservas',
            'ver-panel-control',
            'ver-consumo-servicio',
            'crear-consumos-servicios',
            'editar-consumos-servicios',
            'eliminar-consumos-servicios',
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'eliminar-clientes',
            'ver-caja',
            'ver-pagos',
            'crear-caja',
            'editar-caja',
            'eliminar-caja',
            'ver-reportes',
        ]);
    }
}
