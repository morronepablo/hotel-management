<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRenovacionToClaseInMovimientoCajasTable extends Migration
{
    public function up()
    {
        // Primero, eliminamos el enum actual y lo reemplazamos con el nuevo que incluye 'Renovación'
        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->dropColumn('clase');
        });

        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->enum('clase', ['Venta', 'Servicio', 'Consumo', 'Alquiler', 'Ingreso', 'Egreso', 'Renovación'])
                ->after('tipo')
                ->default('Ingreso');
        });

        // Opcional: Actualizar los registros existentes según el campo 'tipo'
        DB::table('movimiento_cajas')
            ->where('tipo', 'Ingreso')
            ->update(['clase' => 'Ingreso']);

        DB::table('movimiento_cajas')
            ->where('tipo', 'Egreso')
            ->update(['clase' => 'Egreso']);
    }

    public function down()
    {
        // Revertimos los cambios eliminando 'Renovación' del enum
        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->dropColumn('clase');
        });

        Schema::table('movimiento_cajas', function (Blueprint $table) {
            $table->enum('clase', ['Venta', 'Servicio', 'Consumo', 'Alquiler', 'Ingreso', 'Egreso'])
                ->after('tipo')
                ->default('Ingreso');
        });

        // Restaurar los valores según el campo 'tipo'
        DB::table('movimiento_cajas')
            ->where('tipo', 'Ingreso')
            ->update(['clase' => 'Ingreso']);

        DB::table('movimiento_cajas')
            ->where('tipo', 'Egreso')
            ->update(['clase' => 'Egreso']);
    }
}
