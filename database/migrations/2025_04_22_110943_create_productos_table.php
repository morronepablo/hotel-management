<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique(); // Tamaño suficiente para código de barras (EAN-13, UPC, etc.)
            $table->string('producto'); // Nombre del producto
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict'); // Relación con categorías
            $table->string('imagen')->default('sin_imagen.png'); // Imagen, con valor por defecto
            $table->integer('stock')->default(0); // Stock, por defecto 0
            $table->text('descripcion')->nullable(); // Descripción, opcional
            $table->decimal('precio', 10, 2); // Precio, con 2 decimales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
