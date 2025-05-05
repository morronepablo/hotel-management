<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypeTariffsTable extends Migration
{
    public function up()
    {
        Schema::create('room_type_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['HORA', 'DIA']);
            $table->integer('duration')->nullable(); // Para tipo HORA
            $table->time('hour_checkout')->nullable(); // Para tipo DIA
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_type_tariffs');
    }
}
