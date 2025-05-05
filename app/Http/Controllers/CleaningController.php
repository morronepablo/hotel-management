<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Staff;
use App\Models\Cleaning;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CleaningController extends Controller
{
    public function assignCleaning(Request $request, Room $room)
    {
        $request->validate([
            'staff' => 'required|exists:staff,id',
            'cleaning_type' => 'required|in:deep,quick',
        ]);

        $currentDateTime = Carbon::now();
        $duration = $request->cleaning_type === 'deep' ? 60 : 30; // 60 min para profunda, 30 min para rápida
        $endTime = $currentDateTime->copy()->addMinutes($duration);

        // Crear el registro de limpieza
        $cleaning = new Cleaning();
        $cleaning->room_id = $room->id;
        $cleaning->staff_id = $request->staff;
        $cleaning->cleaning_type = $request->cleaning_type;
        $cleaning->start_time = $currentDateTime;
        $cleaning->end_time = $endTime;
        $cleaning->status = 'Active';
        $cleaning->save();

        // Actualizar el estado de la habitación
        $room->status = $request->cleaning_type === 'deep' ? 'Limpieza Profunda' : 'Limpieza Rápida';
        $room->save();

        return redirect()->route('entradas.panel-control')
            ->with('success', 'Limpieza asignada correctamente.');
    }

    public function finishCleaning(Room $room)
    {
        // Buscar el registro de limpieza activo para esta habitación
        $cleaning = Cleaning::where('room_id', $room->id)
            ->where('status', 'Active')
            ->first();

        if (!$cleaning) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se encontró una limpieza activa para esta habitación.');
        }

        // Actualizar el registro de limpieza
        $cleaning->status = 'Completed';
        $cleaning->end_time = Carbon::now();
        $cleaning->save();

        // Actualizar el estado de la habitación a Disponible
        $room->status = 'Disponible';
        $room->updated_at = Carbon::now();
        $room->save();

        return redirect()->route('entradas.panel-control')
            ->with('success', 'Limpieza finalizada correctamente. La habitación ahora está disponible.');
    }
}
