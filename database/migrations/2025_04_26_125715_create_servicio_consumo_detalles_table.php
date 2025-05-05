<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicioConsumoDetallesTable extends Migration
{
    public function up()
    {
        Schema::create('servicio_consumo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_consumo_id')->constrained()->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('estado')->default('Pendiente'); // Pendiente, Pagado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servicio_consumo_detalles');
    }
}
