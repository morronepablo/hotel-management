<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryCompanionTable extends Migration
{
    public function up()
    {
        Schema::create('entry_companion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('companion_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entry_companion');
    }
}
