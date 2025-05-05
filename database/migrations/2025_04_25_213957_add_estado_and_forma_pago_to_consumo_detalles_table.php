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
        Schema::table('consumo_detalles', function (Blueprint $table) {
            // Agregar la columna 'estado' si no existe
            if (!Schema::hasColumn('consumo_detalles', 'estado')) {
                $table->string('estado')->default('Pendiente')->after('subtotal');
            }

            // Agregar la columna 'forma_pago' si no existe
            if (!Schema::hasColumn('consumo_detalles', 'forma_pago')) {
                $table->string('forma_pago')->nullable()->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumo_detalles', function (Blueprint $table) {
            // Eliminar las columnas si existen
            if (Schema::hasColumn('consumo_detalles', 'estado')) {
                $table->dropColumn('estado');
            }

            if (Schema::hasColumn('consumo_detalles', 'forma_pago')) {
                $table->dropColumn('forma_pago');
            }
        });
    }
};
