<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('companion_id')->nullable()->constrained('companions')->onDelete('set null');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->enum('entry_type', ['4_hours', 'full_night', 'month']);
            $table->dateTime('check_in');
            $table->dateTime('check_out');
            $table->decimal('total', 8, 2);
            $table->enum('status', ['Active', 'Finished'])->default('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entries');
    }
}
