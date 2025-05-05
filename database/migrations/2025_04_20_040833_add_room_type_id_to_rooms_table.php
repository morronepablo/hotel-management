<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoomTypeIdToRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('room_type_id')->nullable()->constrained('room_types')->onDelete('set null')->after('type');
            $table->dropColumn('type'); // Eliminamos el campo type antiguo
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
            $table->dropColumn('room_type_id');
            $table->string('type')->nullable()->after('room_number'); // Restauramos el campo type
        });
    }
}
