<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Permisos para Dashboard
            'ver-dashboard',

            // Permisos para Reservas/Entradas
            'ver-reservas',
            'crear-reservas',
            'editar-reservas',
            'eliminar-reservas',

            // Permisos para Entradas
            'ver-panel-control',
            'ver-recepcion',
            'ver-registros',
            'ver-renovaciones',
            'ver-entradas',
            'crear-entrada',
            'crear-renovacion',

            // Permisos para Clientes
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'eliminar-clientes',

            // Permisos para Consumos/Servicio
            'ver-consumo-servicio',
            'crear-consumos-servicios',
            'editar-consumos-servicios',
            'eliminar-consumos-servicios',

            // Permisos para Compras
            'ver-compras',

            // Perisos para Salidas
            'ver-salidas',

            // Permisos para Caja
            'ver-caja',
            'crear-caja',
            'editar-caja',
            'eliminar-caja',

            // Permisos para Pagos
            'ver-pagos',
            'crear-pagos',
            'editar-pagos',
            'eliminar-pagos',

            // Permisos para Reportes
            'ver-reportes',

            // Permisos para Mantenimiento
            'ver-niveles',          // Permiso para ver y gestionar niveles
            'ver-tipos-habitacion', // Permiso para ver y gestionar tipos de habitación
            'ver-habitaciones',     // Permiso para ver y gestionar habitaciones

            // Permisos para Almacén
            // Permiso para Servicios
            'ver-servicios',
            'crear-servicio',
            'editar-servicio',
            'eliminar-servicio',
            // Permiso para Productos
            'ver-productos',
            'crear-producto',
            'editar-producto',
            'eliminar-producto',
            // Permiso para Categorías
            'ver-categorias',
            'crear-categoria',
            'editar-categoria',
            'eliminar-categoria',

            // Permisos para Acceso
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            'ver-personal',
            'crear-personal',
            'editar-personal',
            'eliminar-personal',

            // Permisos para Seguridad
            'ver-seguridad',
            'crear-seguridad',
            'editar-seguridad',
            'eliminar-seguridad',

            // Permisos para Roles y Permisos
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',
            'ver-permisos',
            'crear-permisos',
            'editar-permisos',
            'eliminar-permisos',

            // Permisos para Configuración
            'ver-configuracion',
            'ver-tipo-documento',
            'crear-tipo-documento',
            'editar-tipo-documento',
            'eliminar-tipo-documento',
            'ver-unidad-medida',
            'crear-unidad-medida',
            'editar-unidad-medida',
            'eliminar-unidad-medida',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
