<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToArqueosTable extends Migration
{
    public function up()
    {
        Schema::table('arqueos', function (Blueprint $table) {
            $table->string('status')->default('Abierto')->after('id');
        });
    }

    public function down()
    {
        Schema::table('arqueos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
