<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateStatusEnumInRoomsTable extends Migration
{
    public function up()
    {
        // Cambiar el campo status para incluir "En Limpieza"
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('status', ['Disponible', 'Ocupada', 'Para la Limpieza', 'En Limpieza', 'Reservada'])->default('Disponible')->change();
        });
    }

    public function down()
    {
        // Revertir el cambio, eliminando "En Limpieza" del enum
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('status', ['Disponible', 'Ocupada', 'Para la Limpieza', 'Reservada'])->default('Disponible')->change();
        });
    }
}
