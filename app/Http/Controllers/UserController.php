<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Asegúrate de que el modelo User esté importado
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('id', 'DESC')->get(); // Obtener todos los usuarios
        return view('acceso.usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $roles = Role::all();
        $roles = Role::all();
        return view('acceso.usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $datos = request()->all();
        // return response()->json($datos);
        $request->validate([
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
        ]);

        $usuario = new User();

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->password);

        $usuario->save();

        $usuario->assignRole($request->role);

        return redirect()->route('acceso.usuarios')
            ->with('success', 'Se registró el usuario satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);
        return view('acceso.usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);
        $roles = Role::all()->sortByDesc('id');
        return view('acceso.usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id, // Ignorar el email actual del usuario
            'password' => 'nullable|confirmed', // Solo validar si la contraseña está presente
        ]);

        $usuario = User::findOrFail($id);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        $usuario->syncRoles($request->role);

        return redirect()->route('acceso.usuarios')
            ->with('success', 'Se actualizó el usuario satisfactoriamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('acceso.usuarios')
            ->with('success', 'Se eliminó el usuario satisfactoriamente.');
    }
}
