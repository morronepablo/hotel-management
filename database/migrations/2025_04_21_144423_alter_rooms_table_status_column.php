<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoomsTableStatusColumn extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Cambiar el campo status para incluir los nuevos valores
            $table->enum('status', [
                'Disponible',
                'Ocupada',
                'Para la Limpieza',
                'Limpieza Profunda',
                'Limpieza Rápida'
            ])->change();
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Revertir los cambios, volviendo a los valores originales
            $table->enum('status', [
                'Disponible',
                'Ocupada',
                'Para la Limpieza'
            ])->change();
        });
    }
}
