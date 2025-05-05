<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioIdToMovimientoCajasTable extends Migration
{
    public function up()
    {
        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->nullable()->after('descripcion');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
    }
}
