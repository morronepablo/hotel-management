<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRenewalsTableForTariffs extends Migration
{
    public function up()
    {
        Schema::table('renewals', function (Blueprint $table) {
            // Eliminar la columna entry_type
            $table->dropColumn('entry_type');

            // Agregar la columna room_type_tariff_id
            $table->unsignedBigInteger('room_type_tariff_id')->nullable()->after('room_type_id');
            $table->foreign('room_type_tariff_id')->references('id')->on('room_type_tariffs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('renewals', function (Blueprint $table) {
            // Revertir los cambios: eliminar la relación y la columna room_type_tariff_id
            $table->dropForeign(['room_type_tariff_id']);
            $table->dropColumn('room_type_tariff_id');

            // Restaurar la columna entry_type
            $table->string('entry_type', 255)->after('room_type_id')->default('4_hours');
        });
    }
}
