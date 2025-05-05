<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendidoToConsumoDetallesTable extends Migration
{
    public function up()
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->boolean('vendido')->default(false)->after('forma_pago');
        });
    }

    public function down()
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            $table->dropColumn('vendido');
        });
    }
}
