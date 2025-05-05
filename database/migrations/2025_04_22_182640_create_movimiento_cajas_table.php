<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoCajasTable extends Migration
{
    public function up()
    {
        Schema::create('movimiento_cajas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion')->nullable();
            $table->foreignId('arqueo_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimiento_cajas');
    }
}
