<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\RoomTypeTariff;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    // public function index()
    // {
    //     $roomTypes = RoomType::orderBy('id', 'DESC')->get();
    //     return view('mantenimiento.tipo_habitacion.index', compact('roomTypes'));
    // }

    // public function create()
    // {
    //     return view('mantenimiento.tipo_habitacion.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255|unique:room_types,name',
    //         'price_4_hours' => 'nullable|numeric|min:0',
    //         'price_full_night' => 'nullable|numeric|min:0',
    //         'price_month' => 'nullable|numeric|min:0',
    //     ]);

    //     RoomType::create([
    //         'name' => $request->name,
    //         'price_4_hours' => $request->price_4_hours,
    //         'price_full_night' => $request->price_full_night,
    //         'price_month' => $request->price_month,
    //     ]);

    //     return redirect()->route('mantenimiento.tipo_habitacion.index')
    //         ->with('success', 'Se registró el tipo de habitación satisfactoriamente.');
    // }

    // public function show($id)
    // {
    //     $roomType = RoomType::findOrFail($id);
    //     return view('mantenimiento.tipo_habitacion.show', compact('roomType'));
    // }

    // public function edit($id)
    // {
    //     $roomType = RoomType::findOrFail($id);
    //     return view('mantenimiento.tipo_habitacion.edit', compact('roomType'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $roomType = RoomType::findOrFail($id);

    //     $request->validate([
    //         'name' => 'required|string|max:255|unique:room_types,name,' . $roomType->id,
    //         'price_4_hours' => 'nullable|numeric|min:0',
    //         'price_full_night' => 'nullable|numeric|min:0',
    //         'price_month' => 'nullable|numeric|min:0',
    //     ]);

    //     $roomType->update([
    //         'name' => $request->name,
    //         'price_4_hours' => $request->price_4_hours,
    //         'price_full_night' => $request->price_full_night,
    //         'price_month' => $request->price_month,
    //     ]);

    //     return redirect()->route('mantenimiento.tipo_habitacion.index')
    //         ->with('success', 'Se actualizó el tipo de habitación satisfactoriamente.');
    // }

    // public function destroy($id)
    // {
    //     $roomType = RoomType::findOrFail($id);
    //     $roomType->delete();

    //     return redirect()->route('mantenimiento.tipo_habitacion.index')
    //         ->with('success', 'Se eliminó el tipo de habitación satisfactoriamente.');
    // }


















    public function index()
    {
        $roomTypes = RoomType::orderBy('id', 'DESC')->get();
        return view('mantenimiento.tipo_habitacion.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('mantenimiento.tipo_habitacion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name',
            'description' => 'nullable|string',
        ]);

        RoomType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('mantenimiento.tipo_habitacion.index')
            ->with('success', 'Se registró el tipo de habitación satisfactoriamente.');
    }

    public function show($id)
    {
        $roomType = RoomType::findOrFail($id);
        return view('mantenimiento.tipo_habitacion.show', compact('roomType'));
    }

    public function edit($id)
    {
        $roomType = RoomType::findOrFail($id);
        return view('mantenimiento.tipo_habitacion.edit', compact('roomType'));
    }

    public function update(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string',
        ]);

        $roomType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('mantenimiento.tipo_habitacion.index')
            ->with('success', 'Se actualizó el tipo de habitación satisfactoriamente.');
    }

    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();

        return redirect()->route('mantenimiento.tipo_habitacion.index')
            ->with('success', 'Se eliminó el tipo de habitación satisfactoriamente.');
    }









    /**
     * Muestra la vista para gestionar las tarifas de un tipo de habitación.
     */
    public function manageTariffs($id)
    {
        $roomType = RoomType::findOrFail($id);
        return view('mantenimiento.tipo_habitacion.tarifas', compact('roomType'));
    }

    /**
     * Almacena una nueva tarifa para un tipo de habitación.
     */
    public function storeTariff(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:HORA,DIA',
            'duration' => 'required_if:type,HORA|nullable|integer|min:1',
            'hour_checkout' => 'required_if:type,DIA|nullable',
            'price' => 'required|numeric|min:0',
        ]);

        $roomType->roomTypeTariffs()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'duration' => $validated['type'] == 'HORA' ? $validated['duration'] : null,
            'hour_checkout' => $validated['type'] == 'DIA' ? $validated['hour_checkout'] : null,
            'price' => $validated['price'],
        ]);

        return redirect()->route('mantenimiento.tipo_habitacion.tarifas', $roomType->id)
            ->with('success', 'Tarifa creada correctamente.');
    }

    /**
     * Actualiza una tarifa existente.
     */
    public function updateTariff(Request $request, $tariff)
    {
        $roomTypeTariff = RoomTypeTariff::findOrFail($tariff);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:HORA,DIA',
            'duration' => 'required_if:type,HORA|nullable|integer|min:1',
            'hour_checkout' => 'required_if:type,DIA|nullable',
            'price' => 'required|numeric|min:0',
        ]);

        $roomTypeTariff->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'duration' => $validated['type'] == 'HORA' ? $validated['duration'] : null,
            'hour_checkout' => $validated['type'] == 'DIA' ? $validated['hour_checkout'] : null,
            'price' => $validated['price'],
        ]);

        return redirect()->route('mantenimiento.tipo_habitacion.tarifas', $roomTypeTariff->room_type_id)
            ->with('success', 'Tarifa actualizada correctamente.');
    }

    /**
     * Elimina una tarifa existente.
     */
    public function destroyTariff($tariff)
    {
        $roomTypeTariff = RoomTypeTariff::findOrFail($tariff);
        $roomTypeId = $roomTypeTariff->room_type_id;
        $roomTypeTariff->delete();

        return redirect()->route('mantenimiento.tipo_habitacion.tarifas', $roomTypeId)
            ->with('success', 'Tarifa eliminada correctamente.');
    }
}
