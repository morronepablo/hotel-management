<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypesTable extends Migration
{
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre del tipo de habitación (ej. Doble, Sencilla, Premium, King)
            $table->decimal('price_4_hours', 8, 2)->nullable(); // Precio por 4 horas
            $table->decimal('price_full_night', 8, 2)->nullable(); // Precio por toda la noche
            $table->decimal('price_month', 8, 2)->nullable(); // Precio por mes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_types');
    }
}
