<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleaningsTable extends Migration
{
    public function up()
    {
        Schema::create('cleanings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('cleaning_type'); // 'deep' o 'quick'
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('Active'); // 'Active', 'Completed'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanings');
    }
}
