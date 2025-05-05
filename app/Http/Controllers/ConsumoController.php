<?php

namespace App\Http\Controllers;

use App\Models\Arqueo;
use App\Models\Consumo;
use App\Models\ConsumoDetalle;
use App\Models\Entry;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsumoController extends Controller
{
    // Mostrar el formulario de consumo para una habitación específica (desde el Panel de Control)

    public function create($entryId)
    {
        $entry = Entry::with(['room', 'roomType', 'client'])->findOrFail($entryId);

        // Buscar un registro de Consumo existente para este entry_id, independientemente del estado
        $consumo = Consumo::where('entry_id', $entryId)->first();

        // Si no existe, crear uno nuevo con estado 'Falta Pagar'
        if (!$consumo) {
            $consumo = Consumo::create([
                'entry_id' => $entryId,
                'estado' => 'Falta Pagar',
                'total' => 0,
            ]);
        }

        // Cargar los detalles y recalcular el total
        $detalles = ConsumoDetalle::where('consumo_id', $consumo->id)->with('producto')->get();
        $consumo->total = $detalles->sum('subtotal');

        // Actualizar el estado de consumos basado en los detalles
        $todosPagados = $detalles->count() > 0 && $detalles->every(function ($detalle) {
            return $detalle->estado === 'Pagado';
        });
        $consumo->estado = $todosPagados ? 'Pagado' : 'Falta Pagar';
        $consumo->save();

        // Calcular el total pagado
        $totalPagado = $detalles->where('estado', 'Pagado')->sum('subtotal');

        // Calcular el saldo deudor
        $saldoDeudor = $consumo->total - $totalPagado;
        $saldoDeudor = max($saldoDeudor, 0); // Asegurarse de que no sea negativo

        $productos = Producto::all();

        return view('consumo.create', compact('entry', 'consumo', 'detalles', 'productos', 'totalPagado', 'saldoDeudor'));
    }

    public function addProduct(Request $request, $consumoId)
    {
        $consumo = Consumo::findOrFail($consumoId);
        $producto = Producto::findOrFail($request->producto_id);
        $cantidad = $request->cantidad;

        // Verificar stock
        if ($cantidad > $producto->stock) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock disponible para este producto.'
            ], 400);
        }

        // Crear el detalle con estado "Pendiente"
        $detalle = ConsumoDetalle::create([
            'consumo_id' => $consumo->id,
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio' => $producto->precio,
            'subtotal' => $producto->precio * $cantidad,
            'estado' => 'Pendiente', // Asegurar que el estado inicial sea "Pendiente"
        ]);

        // Recalcular el total
        $consumo->total = $consumo->detalles()->sum('subtotal');
        $consumo->save();

        // Actualizar el stock
        $producto->stock -= $cantidad;
        $producto->save();

        // Asegurarse de cargar la relación 'producto' para el detalle
        $detalle->load('producto');

        return response()->json([
            'success' => true,
            'detalle' => [
                'id' => $detalle->id,
                'producto' => $detalle->producto ? $detalle->producto->producto : 'Producto no encontrado', // Cambiado de 'name' a 'producto'
                'cantidad' => $detalle->cantidad,
                'precio' => $detalle->precio,
                'subtotal' => $detalle->subtotal,
                'estado' => $detalle->estado,
            ],
            'total' => $consumo->total,
        ]);
    }

    public function removeProduct($detalleId)
    {
        $detalle = ConsumoDetalle::findOrFail($detalleId);
        $consumo = $detalle->consumo;
        $producto = $detalle->producto;

        // Restaurar el stock
        $producto->stock += $detalle->cantidad;
        $producto->save();

        // Eliminar el detalle
        $detalle->delete();

        // Recalcular el total
        $consumo->total = $consumo->detalles()->sum('subtotal');
        $consumo->save();

        return response()->json([
            'success' => true,
            'total' => $consumo->total,
        ]);
    }

    public function markAsPaid($consumoId)
    {
        $consumo = Consumo::findOrFail($consumoId);
        $detalles = ConsumoDetalle::where('consumo_id', $consumo->id)->with('producto')->get();

        foreach ($detalles as $detalle) {
            if ($detalle->estado === 'Pagado' && !$detalle->vendido) {
                // Marcar el ítem como vendido
                $detalle->vendido = true;
                $detalle->save();

                // Registrar el ingreso en movimiento_cajas
                $formaPago = $detalle->forma_pago;
                $monto = $detalle->subtotal;

                $movimiento = [
                    'tipo' => 'Ingreso',
                    'clase' => 'Consumo',
                    'monto' => $monto,
                    'efectivo' => $formaPago === 'Efectivo' ? $monto : 0,
                    'mercadopago' => $formaPago === 'MercadoPago' ? $monto : 0,
                    'tarjeta' => $formaPago === 'Tarjeta' ? $monto : 0,
                    'transferencia' => $formaPago === 'Transferencia' ? $monto : 0,
                    'descripcion' => "Ingreso por consumo de habitación {$consumo->entry->room->room_number}",
                    'usuario_id' => auth()->id() ?? 1,
                    'arqueo_id' => Arqueo::whereNull('fecha_cierre')->first()->id ?? 1,
                ];

                $movimientoCaja = MovimientoCaja::create($movimiento);

                // Registrar el ingreso en la tabla pagos
                $descripcionPago = "Pago de consumo de habitación {$consumo->entry->room->room_number} (Cliente: {$consumo->entry->client->name} {$consumo->entry->client->lastname})";
                \App\Models\Pago::create([
                    'fecha' => now(),
                    'descripcion' => $descripcionPago,
                    'clase' => 'Consumo',
                    'room_id' => $consumo->entry->room_id,
                    'monto' => $monto,
                    'efectivo' => $formaPago === 'Efectivo' ? $monto : 0,
                    'mercadopago' => $formaPago === 'MercadoPago' ? $monto : 0,
                    'tarjeta' => $formaPago === 'Tarjeta' ? $monto : 0,
                    'transferencia' => $formaPago === 'Transferencia' ? $monto : 0,
                    'usuario_id' => auth()->id() ?? 1,
                    'arqueo_id' => Arqueo::whereNull('fecha_cierre')->first()->id ?? 1,
                ]);
            }
        }

        // Actualizar el estado de consumos
        $todosPagados = $detalles->count() > 0 && $detalles->every(function ($detalle) {
            return $detalle->estado === 'Pagado';
        });
        $consumo->estado = $todosPagados ? 'Pagado' : 'Falta Pagar';
        $consumo->save();

        return redirect()->route('entradas.panel-control')->with('success', 'Venta registrada correctamente.');
    }

    public function index()
    {
        // Configurar idioma y zona horaria
        Carbon::setLocale('es');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');

        // Obtener las entradas activas con las relaciones necesarias
        $entradas = Entry::with([
            'room',
            'roomType',
            'client',
            'consumo.detalles.producto', // Cargar los detalles y los productos asociados
            'renovations' // Cargar las renovaciones para calcular la fecha de salida
        ])
            ->where('status', 'Active')
            ->where('salida', 0) // Solo habitaciones ocupadas
            ->get();

        // Log para depuración
        Log::info('Entradas activas para consumos:', $entradas->toArray());

        // Mapear las entradas para calcular totalPagado, saldoDeudor y la fecha de salida
        $entradas = $entradas->map(function ($entry) use ($currentDateTime) {
            // Calcular totalPagado y saldoDeudor para consumos
            if ($entry->consumo && $entry->consumo->detalles->isNotEmpty()) {
                $detalles = $entry->consumo->detalles;
                // Calcular el total de consumos
                $totalConsumos = $detalles->sum('subtotal');
                // Calcular el total pagado (consumos pagados)
                $entry->totalPagado = $detalles->where('estado', 'Pagado')->sum('subtotal');
                // Calcular la deuda (consumos no pagados: "Falta Pagar" o "Pendiente")
                $entry->saldoDeudor = $detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal');
                // Agregar el total de consumos para mostrar en la vista
                $entry->totalConsumos = $totalConsumos;
            } else {
                $entry->totalPagado = 0;
                $entry->saldoDeudor = 0;
                $entry->totalConsumos = 0;
            }

            // Calcular la fecha de salida más reciente considerando renovaciones
            $latestCheckOut = $entry->check_out; // Fecha de salida inicial de la entrada
            if ($entry->renovations->isNotEmpty()) {
                // Ordenar renovaciones por check_out descendente y tomar la más reciente
                $latestRenovation = $entry->renovations->sortByDesc('check_out')->first();
                $latestCheckOut = $latestRenovation->check_out;
                Log::info("Room {$entry->room->room_number} - Latest check_out from renovations: {$latestCheckOut}");
            } else {
                Log::info("Room {$entry->room->room_number} - No renovations found, using entry check_out: {$latestCheckOut}");
            }

            // Verificar si $latestCheckOut es válido antes de parsearlo
            try {
                $entry->latest_check_out = !empty($latestCheckOut) ? Carbon::parse($latestCheckOut)->setTimezone('America/Argentina/Buenos_Aires') : null;
            } catch (\Exception $e) {
                Log::error("Room {$entry->room->room_number} - Error parsing check_out: {$latestCheckOut}. Error: " . $e->getMessage());
                $entry->latest_check_out = null;
            }

            return $entry;
        });

        return view('consumo.index', compact('entradas'));
    }

    public function updatePaymentStatus(Request $request)
    {
        try {
            // Validar los datos enviados
            $request->validate([
                'detalle_id' => 'required|exists:consumo_detalles,id',
                'estado' => 'required|in:Pendiente,Pagado',
                'forma_pago' => 'nullable|in:Efectivo,MercadoPago,Tarjeta,Transferencia',
                'monto' => 'nullable|numeric|min:0',
            ]);

            // Buscar el detalle
            $detalle = ConsumoDetalle::findOrFail($request->detalle_id);

            // Actualizar el estado y la forma de pago (si aplica)
            $detalle->estado = $request->estado;
            if ($request->estado === 'Pagado') {
                $detalle->forma_pago = $request->forma_pago;
                // Opcional: podrías registrar el monto pagado en otra tabla si es necesario
            } else {
                $detalle->forma_pago = null; // Limpiar la forma de pago si el estado no es "Pagado"
            }

            $detalle->save();

            // Recalcular el total pagado y el saldo (opcional, si necesitas devolver estos datos)
            $consumo = $detalle->consumo;
            $totalPagado = $consumo->detalles()->where('estado', 'Pagado')->sum('subtotal');
            $saldo = $consumo->total - $totalPagado;

            return response()->json([
                'success' => true,
                'message' => 'Estado de pago actualizado correctamente.',
                'total_pagado' => $totalPagado,
                'saldo' => $saldo,
            ]);
        } catch (\Exception $e) {
            // Loguear el error para depuración
            Log::error('Error al actualizar el estado de pago: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function devolver($detalleId)
    {
        try {
            // Buscar el detalle de consumo
            $detalle = ConsumoDetalle::findOrFail($detalleId);
            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'El detalle de consumo no fue encontrado.'
                ], 404);
            }

            $consumo = $detalle->consumo;
            if (!$consumo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El consumo asociado no fue encontrado.'
                ], 404);
            }

            // Verificar si el detalle puede ser devuelto
            if ($detalle->estado !== 'Pagado' || !$detalle->vendido) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no puede ser devuelto porque no está pagado o ya fue devuelto.'
                ], 400);
            }

            // Buscar el producto asociado
            if (!$detalle->producto_id) {
                Log::error('Producto no asociado al detalle de consumo', ['detalle_id' => $detalleId]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un producto asociado al detalle de consumo.'
                ], 400);
            }

            $producto = Producto::find($detalle->producto_id);
            if (!$producto) {
                Log::error('Producto no encontrado para el detalle de consumo', [
                    'detalle_id' => $detalleId,
                    'producto_id' => $detalle->producto_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'El producto asociado no fue encontrado.'
                ], 404);
            }

            // Iniciar una transacción para asegurar consistencia
            DB::beginTransaction();

            // Obtener el arqueo abierto
            $arqueoId = $this->getArqueoAbierto();
            if (!$arqueoId) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No hay un arqueo abierto para registrar el egreso.'
                ], 400);
            }

            // Registrar el egreso en movimiento_cajas
            $movimientoCaja = new MovimientoCaja();
            $movimientoCaja->tipo = 'Egreso';
            $movimientoCaja->clase = 'Consumo';
            $movimientoCaja->monto = $detalle->subtotal;

            // Determinar la forma de pago y asignar el monto correspondiente
            $formaPago = $detalle->forma_pago ?? 'Efectivo';
            $movimientoCaja->efectivo = 0.00;
            $movimientoCaja->mercadopago = 0.00;
            $movimientoCaja->tarjeta = 0.00;
            $movimientoCaja->transferencia = 0.00;

            switch ($formaPago) {
                case 'Efectivo':
                    $movimientoCaja->efectivo = $detalle->subtotal;
                    break;
                case 'MercadoPago':
                    $movimientoCaja->mercadopago = $detalle->subtotal;
                    break;
                case 'Tarjeta':
                    $movimientoCaja->tarjeta = $detalle->subtotal;
                    break;
                case 'Transferencia':
                    $movimientoCaja->transferencia = $detalle->subtotal;
                    break;
                default:
                    $movimientoCaja->efectivo = $detalle->subtotal;
                    Log::warning('Forma de pago desconocida al registrar egreso', [
                        'detalle_id' => $detalleId,
                        'forma_pago' => $formaPago
                    ]);
            }

            $movimientoCaja->descripcion = "Devolución de producto: {$producto->producto} (ID: {$detalle->id})";
            $movimientoCaja->usuario_id = auth()->id();
            $movimientoCaja->arqueo_id = $arqueoId;
            $movimientoCaja->save();

            // Registrar el egreso en la tabla pagos
            $descripcionPago = "Devolución de consumo de habitación {$consumo->entry->room->room_number} (Cliente: {$consumo->entry->client->name} {$consumo->entry->client->lastname})";
            \App\Models\Pago::create([
                'fecha' => now(),
                'descripcion' => $descripcionPago,
                'clase' => 'Consumo',
                'room_id' => $consumo->entry->room_id,
                'monto' => -$detalle->subtotal, // Usar un monto negativo para indicar un egreso
                'efectivo' => $formaPago === 'Efectivo' ? -$detalle->subtotal : 0,
                'mercadopago' => $formaPago === 'MercadoPago' ? -$detalle->subtotal : 0,
                'tarjeta' => $formaPago === 'Tarjeta' ? -$detalle->subtotal : 0,
                'transferencia' => $formaPago === 'Transferencia' ? -$detalle->subtotal : 0,
                'usuario_id' => auth()->id(),
                'arqueo_id' => $arqueoId,
            ]);

            // Actualizar el stock del producto
            $producto->stock += $detalle->cantidad;
            $producto->save();

            // Eliminar el detalle de consumo
            $detalle->delete();

            // Recalcular el total del consumo
            $total = $consumo->detalles()->sum('subtotal') ?? 0;
            $consumo->total = $total;
            $consumo->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto devuelto correctamente y stock actualizado.',
                'total' => $total
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al devolver el producto', [
                'detalle_id' => $detalleId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al devolver el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getArqueoAbierto()
    {
        // Buscar un arqueo abierto (fecha_cierre es null)
        $arqueo = Arqueo::whereNull('fecha_cierre')->first();
        if (!$arqueo) {
            // Crear un nuevo arqueo si no existe uno abierto
            $arqueo = new Arqueo();
            $arqueo->fecha_apertura = Carbon::now();
            $arqueo->saldo_inicial = 0; // Ajusta según tus necesidades
            $arqueo->fecha_cierre = null; // Aseguramos que el arqueo esté abierto
            $arqueo->user_id = auth()->id(); // O el usuario que sea relevante
            $arqueo->save();
            Log::info('Se creó un nuevo arqueo automáticamente', ['arqueo_id' => $arqueo->id]);
        }
        return $arqueo->id;
    }
}
