<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArqueosTable extends Migration
{
    public function up()
    {
        Schema::create('arqueos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 10, 2)->nullable();
            $table->decimal('monto_final', 10, 2)->nullable();
            $table->decimal('ventas_efectivo', 10, 2)->default(0.00);
            $table->decimal('ventas_tarjeta', 10, 2)->default(0.00);
            $table->decimal('ventas_mercadopago', 10, 2)->default(0.00);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arqueos');
    }
}
