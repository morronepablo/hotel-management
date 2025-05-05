<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckInOutTimesToReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->time('check_in_time')->nullable()->after('check_in');
            $table->time('check_out_time')->nullable()->after('check_out');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('check_in_time');
            $table->dropColumn('check_out_time');
        });
    }
}
