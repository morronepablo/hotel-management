<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumoDetallesTable extends Migration
{
    public function up()
    {
        Schema::create('consumo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumo_id')->constrained('consumos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumo_detalles');
    }
}
