<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea
            $table->dropForeign(['companion_id']); // Esto elimina la clave foránea 'entries_companion_id_foreign'

            // Eliminar la columna companion_id
            $table->dropColumn('companion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            // Restaurar la columna companion_id
            $table->foreignId('companion_id')->nullable()->constrained('companions')->onDelete('set null');
        });
    }
};
