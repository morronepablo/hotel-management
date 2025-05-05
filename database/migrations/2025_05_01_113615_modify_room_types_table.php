<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRoomTypesTable extends Migration
{
    public function up()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Eliminar las columnas de precios actuales
            $table->dropColumn(['price_4_hours', 'price_full_night', 'price_month']);

            // Agregar la columna description después de name
            $table->text('description')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Revertir los cambios en caso de rollback
            $table->decimal('price_4_hours', 8, 2)->nullable();
            $table->decimal('price_full_night', 8, 2)->nullable();
            $table->decimal('price_month', 8, 2)->nullable();

            $table->dropColumn('description');
        });
    }
}
