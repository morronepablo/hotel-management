<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::orderBy('id', 'DESC')->get();
        return view('mantenimiento.nivel.index', compact('levels'));
    }

    public function create()
    {
        return view('mantenimiento.nivel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:levels,name',
        ]);

        Level::create([
            'name' => $request->name,
        ]);

        return redirect()->route('mantenimiento.nivel.index')
            ->with('success', 'Se registró el nivel satisfactoriamente.');
    }

    public function show($id)
    {
        $level = Level::findOrFail($id);
        return view('mantenimiento.nivel.show', compact('level'));
    }

    public function edit($id)
    {
        $level = Level::findOrFail($id);
        return view('mantenimiento.nivel.edit', compact('level'));
    }

    public function update(Request $request, $id)
    {
        $level = Level::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:levels,name,' . $level->id,
        ]);

        $level->update([
            'name' => $request->name,
        ]);

        return redirect()->route('mantenimiento.nivel.index')
            ->with('success', 'Se actualizó el nivel satisfactoriamente.');
    }

    public function destroy($id)
    {
        $level = Level::findOrFail($id);
        $level->delete();

        return redirect()->route('mantenimiento.nivel.index')
            ->with('success', 'Se eliminó el nivel satisfactoriamente.');
    }
}
