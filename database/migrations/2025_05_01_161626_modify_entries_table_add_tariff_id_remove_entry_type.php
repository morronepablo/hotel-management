<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\RoomTypeTariff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ModifyEntriesTableAddTariffIdRemoveEntryType extends Migration
{
    public function up()
    {
        // Agregar la columna tariff_id como clave foránea
        Schema::table('entries', function (Blueprint $table) {
            $table->bigInteger('tariff_id')->unsigned()->nullable()->after('room_type_id');
            $table->foreign('tariff_id')->references('id')->on('room_type_tariffs')->onDelete('set null');
        });

        // Migrar los datos existentes para asignar el tariff_id correcto
        $entries = DB::table('entries')->get();

        foreach ($entries as $entry) {
            // Calcular la duración de la entrada en horas
            $checkIn = Carbon::parse($entry->check_in);
            $checkOut = Carbon::parse($entry->check_out);
            $durationInHours = $checkIn->diffInHours($checkOut);

            // Buscar la tarifa que coincida con el room_type_id y la duración
            $tariff = RoomTypeTariff::where('room_type_id', $entry->room_type_id)
                ->get()
                ->first(function ($tariff) use ($durationInHours) {
                    // Extraer la duración del nombre de la tarifa (por ejemplo, "P.HORA (2)" -> 2)
                    if (preg_match('/\((\d+)\)/', $tariff->name, $matches)) {
                        $tariffDuration = (int) $matches[1];
                        return $tariffDuration == $durationInHours;
                    }
                    return false;
                });

            // Actualizar la entrada con el tariff_id encontrado
            if ($tariff) {
                DB::table('entries')
                    ->where('id', $entry->id)
                    ->update(['tariff_id' => $tariff->id]);
            }
        }

        // Eliminar la columna entry_type
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('entry_type');
        });
    }

    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->enum('entry_type', ['4_hours', 'full_night', 'month'])->after('room_type_id');
        });

        // Limpiar los datos de tariff_id
        DB::table('entries')->update(['tariff_id' => null]);

        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['tariff_id']);
            $table->dropColumn('tariff_id');
        });
    }
}
