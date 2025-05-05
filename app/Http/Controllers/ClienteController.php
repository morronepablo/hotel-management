<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_permission:ver-clientes')->only(['index', 'show']);
        $this->middleware('check_permission:crear-clientes')->only(['create', 'store']);
        $this->middleware('check_permission:editar-clientes')->only(['edit', 'update']);
        $this->middleware('check_permission:eliminar-clientes')->only(['destroy']);
    }

    public function index()
    {
        $clients = Client::with('tipoDocumento')->orderBy('id', 'DESC')->get();
        return view('clientes.index', compact('clients'));
    }

    public function create()
    {
        $tiposDocumento = TipoDocumento::all();
        return view('clientes.create', compact('tiposDocumento'));
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipo_documentos,id',
            'nro_documento' => 'required|string|max:20|unique:clients,nro_documento',
            'nro_matricula' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Crear el cliente
        $client = Client::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'tipo_id' => $request->tipo_id,
            'nro_documento' => $request->nro_documento,
            'nro_matricula' => $request->nro_matricula,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Si la petición es AJAX, devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
            ]);
        }

        // Si no es AJAX, redirigir
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function storeAjax(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipo_documentos,id',
            'nro_documento' => 'required|string|max:20|unique:clients,nro_documento',
            'nro_matricula' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Crear el cliente
            $cliente = Client::create([
                'name' => $request->input('name'),
                'lastname' => $request->input('lastname'),
                'tipo_id' => $request->input('tipo_id'),
                'nro_documento' => $request->input('nro_documento'),
                'nro_matricula' => $request->input('nro_matricula'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado correctamente.',
                'client' => $cliente,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear cliente en storeAjax: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $client = Client::with('tipoDocumento')->findOrFail($id);
        return view('clientes.show', compact('client'));
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $tiposDocumento = TipoDocumento::all();
        return view('clientes.edit', compact('client', 'tiposDocumento'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipo_documentos,id',
            'nro_documento' => 'required|string|max:20|unique:clients,nro_documento,' . $client->id,
            'nro_matricula' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client->update([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'tipo_id' => $request->tipo_id,
            'nro_documento' => $request->nro_documento,
            'nro_matricula' => $request->nro_matricula,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
