<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToServicioConsumosTable extends Migration
{
    public function up()
    {
        Schema::table('servicio_consumos', function (Blueprint $table) {
            $table->enum('estado', ['Pagado', 'Falta Pagar'])->default('Falta Pagar')->after('total');
        });
    }

    public function down()
    {
        Schema::table('servicio_consumos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
