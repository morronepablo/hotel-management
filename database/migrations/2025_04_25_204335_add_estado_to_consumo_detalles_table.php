<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToConsumoDetallesTable extends Migration
{
    public function up()
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->string('estado')->default('Pendiente'); // Puede ser "Pendiente" o "Pagado"
        });
    }

    public function down()
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
