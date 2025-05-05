<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['denominacion' => 'Alimentos'],
            ['denominacion' => 'Cerveza'],
            ['denominacion' => 'Productos Varios'],
            ['denominacion' => 'Preservativos'],
            ['denominacion' => 'Cigarros'],
            ['denominacion' => 'Desechables'],
            ['denominacion' => 'Bebidas'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(['denominacion' => $categoria['denominacion']], $categoria);
        }
    }
}
