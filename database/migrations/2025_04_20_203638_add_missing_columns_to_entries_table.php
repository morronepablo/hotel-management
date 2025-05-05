<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('payment_method')->nullable(); // Método de pago (Efectivo, Mercadopago, etc.)
            $table->decimal('discount', 8, 2)->default(0); // Descuento, con 2 decimales
            $table->decimal('payment_received', 8, 2)->default(0); // Pago recibido, con 2 decimales
            $table->decimal('debt', 8, 2)->default(0); // Deuda, con 2 decimales
            $table->text('observations')->nullable(); // Observaciones, puede ser largo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'discount', 'payment_received', 'debt', 'observations']);
        });
    }
};
