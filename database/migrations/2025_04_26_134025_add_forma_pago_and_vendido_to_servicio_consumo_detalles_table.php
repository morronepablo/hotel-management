<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormaPagoAndVendidoToServicioConsumoDetallesTable extends Migration
{
    public function up()
    {
        Schema::table('servicio_consumo_detalles', function (Blueprint $table) {
            $table->string('forma_pago')->nullable()->after('estado');
            $table->boolean('vendido')->default(false)->after('forma_pago');
        });
    }

    public function down()
    {
        Schema::table('servicio_consumo_detalles', function (Blueprint $table) {
            $table->dropColumn(['forma_pago', 'vendido']);
        });
    }
}
