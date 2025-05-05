<?php

namespace App\Http\Controllers;

use App\Models\Arqueo;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class ArqueoController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_permission:ver-caja')->except(['store', 'update', 'destroy']);
        $this->middleware('check_permission:crear-caja')->only(['create', 'store', 'ingresoegreso', 'store_ingresos_egresos', 'cierre', 'store_cierre']);
        $this->middleware('check_permission:editar-caja')->only(['edit', 'update']);
        $this->middleware('check_permission:eliminar-caja')->only(['destroy']);
    }

    public function index()
    {
        $arqueoAbierto = Arqueo::whereNull('fecha_cierre')
            ->where('usuario_id', Auth::id())
            ->first();

        $arqueos = Arqueo::with('movimientos')
            ->where('usuario_id', Auth::id())
            ->with(['usuario'])
            ->get()
            ->sortByDesc('id');

        return view('caja.arqueos.index', compact('arqueos', 'arqueoAbierto'));
    }

    public function create()
    {
        return view('caja.arqueos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_apertura' => 'required',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $arqueo = new Arqueo();
        $arqueo->fecha_apertura = $request->fecha_apertura;
        $arqueo->monto_inicial = $request->monto_inicial;
        $arqueo->descripcion = $request->descripcion;
        $arqueo->usuario_id = Auth::id();
        $arqueo->save();

        return redirect()->route('caja.arqueos.index')
            ->with('mensaje', 'Se registró la apertura de caja satisfactoriamente.')
            ->with('icono', 'success');
    }

    public function show($id)
    {
        $arqueo = Arqueo::with('usuario')->findOrFail($id);
        $movimientos = MovimientoCaja::where('arqueo_id', $id)->get();
        return view('caja.arqueos.show', compact('arqueo', 'movimientos'));
    }

    public function edit($id)
    {
        $arqueo = Arqueo::with('usuario')->findOrFail($id);
        return view('caja.arqueos.edit', compact('arqueo'));
    }

    public function ingresoegreso($id)
    {
        $arqueo = Arqueo::findOrFail($id);
        return view('caja.arqueos.ingreso-egreso', compact('arqueo'));
    }

    public function store_ingresos_egresos(Request $request)
    {
        $request->validate([
            'arqueo_id' => 'required|exists:arqueos,id',
            'tipo' => 'required|in:Ingreso,Egreso',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $movimiento = new MovimientoCaja();
        $movimiento->tipo = $request->tipo;
        $movimiento->clase = $request->tipo;
        $movimiento->monto = $request->monto;
        $movimiento->descripcion = $request->descripcion;
        $movimiento->arqueo_id = $request->arqueo_id;
        $movimiento->save();

        return redirect()->route('caja.arqueos.index')
            ->with('mensaje', 'Se registró el movimiento de caja satisfactoriamente.')
            ->with('icono', 'success');
    }

    public function cierre($id, Request $request)
    {
        $arqueo = Arqueo::findOrFail($id);

        if (!is_null($arqueo->fecha_cierre)) {
            return redirect()->route('caja.arqueos.index')->with('error', 'Este arqueo ya está cerrado.');
        }

        $fechaCierre = $request->input('fecha_cierre', now());

        // Obtener los movimientos del arqueo entre la fecha de apertura y cierre
        $movimientos = MovimientoCaja::where('arqueo_id', $arqueo->id)
            ->whereBetween('created_at', [$arqueo->fecha_apertura, $fechaCierre])
            ->get();

        // Calcular los totales considerando ingresos y egresos
        $totalTarjetasIngresos = $movimientos->where('tipo', 'Ingreso')->sum('tarjeta');
        $totalTarjetasEgresos = $movimientos->where('tipo', 'Egreso')->sum('tarjeta');
        $totalMercadoPagoIngresos = $movimientos->where('tipo', 'Ingreso')->sum('mercadopago');
        $totalMercadoPagoEgresos = $movimientos->where('tipo', 'Egreso')->sum('mercadopago');

        // Calcular los totales netos
        $totalTarjetas = $totalTarjetasIngresos - $totalTarjetasEgresos;
        $totalMercadoPago = $totalMercadoPagoIngresos - $totalMercadoPagoEgresos;

        if ($request->ajax()) {
            return response()->json([
                'totalTarjetas' => $totalTarjetas,
                'totalMercadoPago' => $totalMercadoPago,
            ]);
        }

        return view('caja.arqueos.cierre', compact('arqueo', 'totalTarjetas', 'totalMercadoPago'));
    }

    public function store_cierre(Request $request)
    {
        $request->validate([
            'fecha_cierre' => 'required',
            'monto_final' => 'required|numeric|min:0',
            'ventas_efectivo' => 'required|numeric|min:0',
            'ventas_tarjeta' => 'required|numeric|min:0',
            'ventas_mercadopago' => 'required|numeric|min:0',
        ]);

        $arqueo = Arqueo::findOrFail($request->arqueo_id);
        $arqueo->status = 'Cerrado';
        $arqueo->fecha_cierre = $request->fecha_cierre;
        $arqueo->monto_final = $request->monto_final;
        $arqueo->ventas_efectivo = $request->ventas_efectivo;
        $arqueo->ventas_tarjeta = $request->ventas_tarjeta;
        $arqueo->ventas_mercadopago = $request->ventas_mercadopago;
        $arqueo->save();

        return redirect()->route('caja.arqueos.index')
            ->with('mensaje', 'Se registró el cierre de caja satisfactoriamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_apertura' => 'required',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $arqueo = Arqueo::findOrFail($id);
        $arqueo->fecha_apertura = $request->fecha_apertura;
        $arqueo->monto_inicial = $request->monto_inicial;
        $arqueo->descripcion = $request->descripcion;
        $arqueo->usuario_id = Auth::id();
        $arqueo->save();

        return redirect()->route('caja.arqueos.index')
            ->with('mensaje', 'Se actualizó el arqueo de caja satisfactoriamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        Arqueo::destroy($id);
        return redirect()->route('caja.arqueos.index')
            ->with('mensaje', 'Se eliminó el arqueo satisfactoriamente.')
            ->with('icono', 'success');
    }

    public function reporte()
    {
        $arqueos = Arqueo::with('movimientos')
            ->where('usuario_id', Auth::id())
            ->get()
            ->sortByDesc('id');

        $pdf = PDF::loadView('caja.arqueos.reporte', compact('arqueos'))
            ->setPaper('letter', 'landscape');
        return $pdf->stream();
    }
}
