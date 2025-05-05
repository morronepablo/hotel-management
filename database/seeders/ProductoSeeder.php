<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigo' => '7790895000218',
                'producto' => 'Coca Cola 600ml.',
                'categoria_id' => 7, // Asegúrate de que exista una categoría con ID 7
                'imagen' => 'sin_imagen.png',
                'stock' => 10,
                'descripcion' => null,
                'precio' => 1000.00,
            ],
            [
                'codigo' => '7790895000330',
                'producto' => 'Cerveza Quilmes 500ml.',
                'categoria_id' => 2, // Asegúrate de que exista una categoría con ID 7
                'imagen' => 'sin_imagen.png',
                'stock' => 10,
                'descripcion' => null,
                'precio' => 1500.00,
            ],
            [
                'codigo' => '7795265005114',
                'producto' => 'Alfajores Maicena Cabo Blanco 145g.',
                'categoria_id' => 1, // Asegúrate de que exista una categoría con ID 7
                'imagen' => 'sin_imagen.png',
                'stock' => 10,
                'descripcion' => null,
                'precio' => 1500.00,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::firstOrCreate(['codigo' => $producto['codigo']], $producto);
        }
    }
}
