<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->decimal('efectivo', 8, 2)->default(0.00)->after('payment_received');
            $table->decimal('mercadopago', 8, 2)->default(0.00)->after('efectivo');
            $table->decimal('tarjeta', 8, 2)->default(0.00)->after('mercadopago');
            $table->decimal('transferencia', 8, 2)->default(0.00)->after('tarjeta');
            $table->enum('pago', ['Pagado', 'Falta Pagar'])->default('Falta Pagar')->after('transferencia');
        });
    }

    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['efectivo', 'mercadopago', 'tarjeta', 'transferencia', 'pago']);
        });
    }
}
