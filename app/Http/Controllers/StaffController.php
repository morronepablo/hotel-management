<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    public function index()
    {
        $personal = Staff::orderBy('id', 'DESC')->get();
        return view('acceso.personal.index', compact('personal'));
    }

    public function create()
    {
        return view('acceso.personal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'telefono' => 'required',
        ]);

        $staff = new Staff();
        $staff->nombre = $request->nombre;
        $staff->telefono = $request->telefono;
        $staff->save();

        return redirect()->route('acceso.personal')
            ->with('success', 'Se registró el personal satisfactoriamente.');
    }

    public function show($id)
    {
        $staff = Staff::findOrFail($id);
        return view('acceso.personal.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return view('acceso.personal.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'telefono' => 'required',
        ]);

        $staff = Staff::findOrFail($id);
        $staff->nombre = $request->nombre;
        $staff->telefono = $request->telefono;
        $staff->save();

        return redirect()->route('acceso.personal')
            ->with('success', 'Se actualizó el personal satisfactoriamente.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('acceso.personal')
            ->with('success', 'Se eliminó el personal satisfactoriamente.');
    }
}
