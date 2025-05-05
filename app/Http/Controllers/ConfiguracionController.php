<?php

namespace App\Http\Controllers;

use App\Models\HotelSetting;
use App\Models\TipoDocumento;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function datosHotel()
    {
        // Obtener los datos del hotel (si existen)
        $hotelSetting = HotelSetting::first();

        // Si no hay datos, inicializamos un objeto vacío para la vista
        if (!$hotelSetting) {
            $hotelSetting = new HotelSetting();
        }

        return view('configuracion.datos_hotel', compact('hotelSetting'));
    }

    public function actualizarDatosHotel(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'cuit' => 'nullable|string|max:20',
            'simbolo_monetario' => 'nullable|string|max:5',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Máximo 2MB
        ]);

        // Obtener o crear el registro de configuración
        $hotelSetting = HotelSetting::first();
        if (!$hotelSetting) {
            $hotelSetting = new HotelSetting();
        }

        // Actualizar los datos
        $hotelSetting->nombre = $request->nombre;
        $hotelSetting->direccion = $request->direccion;
        $hotelSetting->telefono = $request->telefono;
        $hotelSetting->cuit = $request->cuit;
        $hotelSetting->simbolo_monetario = $request->simbolo_monetario;

        // Manejar la carga del logo
        if ($request->hasFile('logo')) {
            // Eliminar el logo anterior si existe
            if ($hotelSetting->logo) {
                Storage::disk('public_uploads')->delete($hotelSetting->logo);
            }

            // Guardar el nuevo logo en public/uploads
            $logoPath = $request->file('logo')->store('', 'public_uploads');
            $hotelSetting->logo = $logoPath;
        }

        $hotelSetting->save();

        return redirect()->route('configuracion.datos_hotel')
            ->with('success', 'Datos del hotel actualizados correctamente.');
    }

    // Nuevos métodos para Tipo Documento
    public function tipoDocumentoIndex()
    {
        $tipos = TipoDocumento::orderBy('id', 'DESC')->get();
        return view('configuracion.tipo_documento.index', compact('tipos'));
    }

    public function tipoDocumentoCreate()
    {
        return view('configuracion.tipo_documento.create');
    }

    public function tipoDocumentoStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_documentos,nombre',
            'longitud' => 'required|integer|min:1',
        ]);

        $tipoDocumento = new TipoDocumento();
        $tipoDocumento->nombre = $request->nombre;
        $tipoDocumento->longitud = $request->longitud;
        $tipoDocumento->save();

        return redirect()->route('configuracion.tipo_documento')
            ->with('success', 'Se registró el tipo de documento satisfactoriamente.');
    }

    public function tipoDocumentoShow($id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);
        return view('configuracion.tipo_documento.show', compact('tipoDocumento'));
    }

    public function tipoDocumentoEdit($id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);
        return view('configuracion.tipo_documento.edit', compact('tipoDocumento'));
    }

    public function tipoDocumentoUpdate(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_documentos,nombre,' . $id,
            'longitud' => 'required|integer|min:1',
        ]);

        $tipoDocumento = TipoDocumento::findOrFail($id);
        $tipoDocumento->nombre = $request->nombre;
        $tipoDocumento->longitud = $request->longitud;
        $tipoDocumento->save();

        return redirect()->route('configuracion.tipo_documento')
            ->with('success', 'Se actualizó el tipo de documento satisfactoriamente.');
    }

    public function tipoDocumentoDestroy($id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);
        $tipoDocumento->delete();

        return redirect()->route('configuracion.tipo_documento')
            ->with('success', 'Se eliminó el tipo de documento satisfactoriamente.');
    }

    // Nuevos métodos para Unidad Medida
    public function unidadMedidaIndex()
    {
        $unidades = UnidadMedida::orderBy('id', 'DESC')->get();
        return view('configuracion.unidad_medida.index', compact('unidades'));
    }

    public function unidadMedidaCreate()
    {
        return view('configuracion.unidad_medida.create');
    }

    public function unidadMedidaStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:unidad_medidas,nombre',
            'valor_unidad' => 'required|integer|min:1',
        ]);

        $unidadMedida = new UnidadMedida();
        $unidadMedida->nombre = $request->nombre;
        $unidadMedida->valor_unidad = $request->valor_unidad;
        $unidadMedida->save();

        return redirect()->route('configuracion.unidad_medida')
            ->with('success', 'Se registró la unidad de medida satisfactoriamente.');
    }

    public function unidadMedidaShow($id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);
        return view('configuracion.unidad_medida.show', compact('unidadMedida'));
    }

    public function unidadMedidaEdit($id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);
        return view('configuracion.unidad_medida.edit', compact('unidadMedida'));
    }

    public function unidadMedidaUpdate(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:unidad_medidas,nombre,' . $id,
            'valor_unidad' => 'required|integer|min:1',
        ]);

        $unidadMedida = UnidadMedida::findOrFail($id);
        $unidadMedida->nombre = $request->nombre;
        $unidadMedida->valor_unidad = $request->valor_unidad;
        $unidadMedida->save();

        return redirect()->route('configuracion.unidad_medida')
            ->with('success', 'Se actualizó la unidad de medida satisfactoriamente.');
    }

    public function unidadMedidaDestroy($id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);
        $unidadMedida->delete();

        return redirect()->route('configuracion.unidad_medida')
            ->with('success', 'Se eliminó la unidad de medida satisfactoriamente.');
    }
}
