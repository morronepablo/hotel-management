<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumosTable extends Migration
{
    public function up()
    {
        Schema::create('consumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('entries')->onDelete('cascade');
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('estado', ['Pagado', 'Falta Pagar'])->default('Falta Pagar');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumos');
    }
}
