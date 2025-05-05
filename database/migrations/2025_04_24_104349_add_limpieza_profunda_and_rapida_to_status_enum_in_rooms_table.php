<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimpiezaProfundaAndRapidaToStatusEnumInRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('status', [
                'Disponible',
                'Ocupada',
                'Para la Limpieza',
                'En Limpieza',
                'Reservada',
                'Limpieza Profunda',
                'Limpieza Rápida'
            ])->default('Disponible')->change();
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('status', [
                'Disponible',
                'Ocupada',
                'Para la Limpieza',
                'En Limpieza',
                'Reservada'
            ])->default('Disponible')->change();
        });
    }
}
