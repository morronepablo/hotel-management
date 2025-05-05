<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidad_medidas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Nombre de la unidad de medida (ej. Unidad, Docena)
            $table->integer('valor_unidad'); // Valor numérico de la unidad (ej. 1, 12, 10)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidad_medidas');
    }
};
