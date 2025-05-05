<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewalsTable extends Migration
{
    public function up()
    {
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->onDelete('cascade'); // Relación con la entrada original
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('entry_type'); // Tipo de renovación: 4_hours, full_night, month
            $table->dateTime('check_in'); // Fecha y hora de inicio de la renovación
            $table->dateTime('check_out'); // Fecha y hora de fin de la renovación
            $table->integer('quantity')->default(1); // Cantidad de unidades de tiempo
            $table->decimal('discount', 10, 2)->default(0); // Descuento
            $table->decimal('efectivo', 10, 2)->default(0); // Pago en efectivo
            $table->decimal('mercadopago', 10, 2)->default(0); // Pago por MercadoPago
            $table->decimal('tarjeta', 10, 2)->default(0); // Pago por tarjeta
            $table->decimal('transferencia', 10, 2)->default(0); // Pago por transferencia
            $table->decimal('total', 10, 2); // Total a pagar
            $table->decimal('debt', 10, 2)->default(0); // Deuda pendiente
            $table->string('pago')->default('Falta Pagar'); // Estado del pago: Pagado, Falta Pagar
            $table->text('observations')->nullable(); // Observaciones
            $table->string('status')->default('Active'); // Estado: Active, Completed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('renewals');
    }
}
