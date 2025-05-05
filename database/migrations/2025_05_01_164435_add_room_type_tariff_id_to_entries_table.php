<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoomTypeTariffIdToEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->unsignedBigInteger('room_type_tariff_id')->nullable()->after('room_type_id');
            $table->foreign('room_type_tariff_id')->references('id')->on('room_type_tariffs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['room_type_tariff_id']);
            $table->dropColumn('room_type_tariff_id');
        });
    }
}
