<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Level;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['level', 'roomType'])->orderBy('id', 'DESC')->get();
        return view('mantenimiento.habitacion.index', compact('rooms'));
    }

    public function create()
    {
        $levels = Level::orderBy('name', 'ASC')->get();
        $roomTypes = RoomType::orderBy('name', 'ASC')->get();
        return view('mantenimiento.habitacion.create', compact('levels', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number',
            'level_id' => 'required|exists:levels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:Disponible,Ocupada,Para la Limpieza,Limpieza Profunda,Limpieza Rápida',
        ]);

        Room::create([
            'room_number' => $request->room_number,
            'level_id' => $request->level_id,
            'room_type_id' => $request->room_type_id,
            'status' => $request->status,
        ]);

        return redirect()->route('mantenimiento.habitacion.index')
            ->with('success', 'Se registró la habitación satisfactoriamente.');
    }

    public function show($id)
    {
        $room = Room::with(['level', 'roomType'])->findOrFail($id);
        return view('mantenimiento.habitacion.show', compact('room'));
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $levels = Level::orderBy('name', 'ASC')->get();
        $roomTypes = RoomType::orderBy('name', 'ASC')->get();
        return view('mantenimiento.habitacion.edit', compact('room', 'levels', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'room_number' => 'required|string|max:255|unique:rooms,room_number,' . $room->id,
            'level_id' => 'required|exists:levels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:Disponible,Ocupada,Para la Limpieza,Limpieza Profunda,Limpieza Rápida',
        ]);

        $room->update([
            'room_number' => $request->room_number,
            'level_id' => $request->level_id,
            'room_type_id' => $request->room_type_id,
            'status' => $request->status,
        ]);

        return redirect()->route('mantenimiento.habitacion.index')
            ->with('success', 'Se actualizó la habitación satisfactoriamente.');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('mantenimiento.habitacion.index')
            ->with('success', 'Se eliminó la habitación satisfactoriamente.');
    }
}
