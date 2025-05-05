<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToMovimientosCajaTable extends Migration
{
    public function up()
    {
        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->decimal('efectivo', 10, 2)->default(0.00)->after('monto');
            $table->decimal('mercadopago', 10, 2)->default(0.00)->after('efectivo');
            $table->decimal('tarjeta', 10, 2)->default(0.00)->after('mercadopago');
            $table->decimal('transferencia', 10, 2)->default(0.00)->after('tarjeta');
        });
    }

    public function down()
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropColumn(['efectivo', 'mercadopago', 'tarjeta', 'transferencia']);
        });
    }
}
