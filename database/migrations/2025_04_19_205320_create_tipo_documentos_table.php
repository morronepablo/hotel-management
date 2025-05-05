<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Nombre del tipo de documento (ej. Factura, Boleta)
            $table->integer('longitud'); // Longitud del documento
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
};
