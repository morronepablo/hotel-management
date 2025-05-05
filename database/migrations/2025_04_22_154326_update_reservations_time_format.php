<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Actualizar check_in_time y check_out_time para eliminar los segundos
        DB::table('reservations')
            ->whereNotNull('check_in_time')
            ->whereRaw("check_in_time REGEXP '^[0-9]{2}:[0-9]{2}:[0-9]{2}$'")
            ->update([
                'check_in_time' => DB::raw("SUBSTRING(check_in_time, 1, 5)"),
            ]);

        DB::table('reservations')
            ->whereNotNull('check_out_time')
            ->whereRaw("check_out_time REGEXP '^[0-9]{2}:[0-9]{2}:[0-9]{2}$'")
            ->update([
                'check_out_time' => DB::raw("SUBSTRING(check_out_time, 1, 5)"),
            ]);
    }

    public function down()
    {
        // No se necesita rollback para esta migración
    }
};
