<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('descripcion');
            $table->string('clase')->nullable(); // Alquiler, Consumo, Servicio, Manual
            $table->decimal('monto', 10, 2);
            $table->decimal('efectivo', 10, 2)->default(0.00);
            $table->decimal('mercadopago', 10, 2)->default(0.00);
            $table->decimal('tarjeta', 10, 2)->default(0.00);
            $table->decimal('transferencia', 10, 2)->default(0.00);
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('arqueo_id')->nullable()->constrained('arqueos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
