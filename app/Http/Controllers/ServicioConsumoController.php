<?php

namespace App\Http\Controllers;

use App\Models\Arqueo;
use App\Models\Entry;
use App\Models\MovimientoCaja;
use App\Models\Servicio;
use App\Models\ServicioConsumo;
use App\Models\ServicioConsumoDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServicioConsumoController extends Controller
{

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
            'servicioConsumo.detalles.servicio', // Cargar los detalles y los servicios asociados
            'renovations' // Cargar las renovaciones para calcular la fecha de salida
        ])
            ->where('status', 'Active')
            ->where('salida', 0) // Solo habitaciones ocupadas
            ->get();

        // Log para depuración
        Log::info('Entradas activas para servicios:', $entradas->toArray());

        // Calcular totalPagado, saldoDeudor y la fecha de salida para cada entrada
        $entradas = $entradas->map(function ($entry) use ($currentDateTime) {
            // Calcular totalPagado y saldoDeudor para servicios
            if ($entry->servicioConsumo && $entry->servicioConsumo->detalles->isNotEmpty()) {
                $detalles = $entry->servicioConsumo->detalles;
                // Calcular el total de servicios
                $totalServicios = $detalles->sum('subtotal');
                // Calcular el total pagado (servicios pagados)
                $entry->totalPagado = $detalles->where('estado', 'Pagado')->sum('subtotal');
                // Calcular la deuda (servicios no pagados: "Falta Pagar" o "Pendiente")
                $entry->saldoDeudor = $detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal');
                // Agregar el total de servicios para mostrar en la vista
                $entry->totalServicios = $totalServicios;
            } else {
                $entry->totalPagado = 0;
                $entry->saldoDeudor = 0;
                $entry->totalServicios = 0;
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

        return view('servicio-consumo.index', compact('entradas'));
    }

    public function create($entry_id)
    {
        $entry = Entry::with(['client', 'room', 'roomType'])->findOrFail($entry_id);

        // Crear o obtener el registro de ServicioConsumo
        $servicioConsumo = ServicioConsumo::firstOrCreate(
            ['entry_id' => $entry_id],
            ['total' => 0]
        );

        // Cargar los detalles usando la relación correcta
        $detalles = $servicioConsumo->detalles()->with('servicio')->get();
        $servicios = Servicio::all();

        $totalPagado = $detalles->where('estado', 'Pagado')->sum('subtotal');
        $saldoDeudor = $servicioConsumo->total - $totalPagado;

        $hasPagadosNoVendidos = $detalles->where('estado', 'Pagado')->where('vendido', false)->count() > 0;

        return view('servicio-consumo.create', compact('entry', 'servicioConsumo', 'detalles', 'servicios', 'totalPagado', 'saldoDeudor', 'hasPagadosNoVendidos'));
    }

    public function addServicio(Request $request, $entry_id)
    {
        $entry = Entry::findOrFail($entry_id);
        $servicioConsumo = ServicioConsumo::firstOrCreate(
            ['entry_id' => $entry->id],
            ['total' => 0]
        );

        // Depuración: Verificar el valor de $request->servicio_id
        Log::info('Servicio ID recibido:', ['servicio_id' => $request->servicio_id]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        $cantidad = $request->cantidad;

        // Depuración: Verificar los datos del servicio
        Log::info('Datos del servicio:', ['servicio' => $servicio->toArray()]);

        $detalle = ServicioConsumoDetalle::create([
            'servicio_consumo_id' => $servicioConsumo->id,
            'servicio_id' => $servicio->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $servicio->precio,
            'subtotal' => $servicio->precio * $cantidad,
            'estado' => 'Pendiente',
            'vendido' => false,
        ]);

        $servicioConsumo->recalculateTotal();

        if ($request->ajax()) {
            // Asegurarse de que el nombre del servicio sea un string válido
            $nombreServicio = $servicio->nombre ?? 'Servicio no especificado';

            return response()->json([
                'success' => true,
                'detalle' => [
                    'id' => $detalle->id,
                    'servicio' => $nombreServicio,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal,
                    'estado' => $detalle->estado,
                    'vendido' => $detalle->vendido,
                ],
                'total' => $servicioConsumo->total,
            ]);
        }

        return redirect()->route('servicio-consumo.create', $entry->id)
            ->with('success', 'Servicio agregado correctamente.');
    }

    public function removeServicio(Request $request, $detalle_id)
    {
        $detalle = ServicioConsumoDetalle::with(['servicioConsumo.entry.client', 'servicioConsumo.entry.room'])->findOrFail($detalle_id);
        $servicioConsumo = $detalle->servicioConsumo;

        // Bloquear la eliminación si el servicio ya está vendido
        if ($detalle->vendido) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un servicio que ya ha sido vendido.',
            ], 403);
        }

        // Verificar si el detalle está pagado y no vendido para registrar un egreso
        if ($detalle->estado === 'Pagado') {
            $clienteNombre = $servicioConsumo->entry->client->name . ' ' . ($servicioConsumo->entry->client->lastname ?? '');
            $habitacionNumero = $servicioConsumo->entry->room->room_number;

            // Determinar la forma de pago del detalle eliminado
            $montosPorFormaPago = [
                'efectivo' => 0,
                'mercadopago' => 0,
                'tarjeta' => 0,
                'transferencia' => 0,
            ];

            $formaPago = strtolower($detalle->forma_pago);
            if (array_key_exists($formaPago, $montosPorFormaPago)) {
                $montosPorFormaPago[$formaPago] = $detalle->subtotal;
            }

            // Obtener el arqueo abierto
            $arqueoId = Arqueo::whereNull('fecha_cierre')->first()->id ?? 1;

            // Registrar el egreso en movimiento_cajas
            MovimientoCaja::create([
                'tipo' => 'Egreso',
                'clase' => 'Servicio',
                'monto' => $detalle->subtotal,
                'efectivo' => $montosPorFormaPago['efectivo'],
                'mercadopago' => $montosPorFormaPago['mercadopago'],
                'tarjeta' => $montosPorFormaPago['tarjeta'],
                'transferencia' => $montosPorFormaPago['transferencia'],
                'descripcion' => "Eliminación de servicio en la habitación {$habitacionNumero} (Cliente: {$clienteNombre})",
                'usuario_id' => auth()->id(),
                'arqueo_id' => $arqueoId,
            ]);

            // Registrar el egreso en la tabla pagos
            $descripcionPago = "Eliminación de servicio en la habitación {$habitacionNumero} (Cliente: {$clienteNombre})";
            \App\Models\Pago::create([
                'fecha' => now(),
                'descripcion' => $descripcionPago,
                'clase' => 'Servicio',
                'room_id' => $servicioConsumo->entry->room_id,
                'monto' => -$detalle->subtotal, // Monto negativo para indicar egreso
                'efectivo' => $montosPorFormaPago['efectivo'] ? -$montosPorFormaPago['efectivo'] : 0,
                'mercadopago' => $montosPorFormaPago['mercadopago'] ? -$montosPorFormaPago['mercadopago'] : 0,
                'tarjeta' => $montosPorFormaPago['tarjeta'] ? -$montosPorFormaPago['tarjeta'] : 0,
                'transferencia' => $montosPorFormaPago['transferencia'] ? -$montosPorFormaPago['transferencia'] : 0,
                'usuario_id' => auth()->id(),
                'arqueo_id' => $arqueoId,
            ]);
        }

        // Eliminar el detalle
        $detalle->delete();

        // Recalcular el total del ServicioConsumo
        $servicioConsumo->recalculateTotal();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => $servicioConsumo->total,
                'message' => 'Servicio eliminado correctamente.'
            ]);
        }

        return redirect()->route('servicio-consumo.create', $servicioConsumo->entry_id)
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function vendido(Request $request, $servicioConsumo_id)
    {
        $servicioConsumo = ServicioConsumo::with(['detalles.servicio', 'entry.client', 'entry.room'])->findOrFail($servicioConsumo_id);

        $detallesPagadosNoVendidos = $servicioConsumo->detalles()
            ->where('estado', 'Pagado')
            ->where('vendido', false)
            ->get();

        if ($detallesPagadosNoVendidos->isEmpty()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay servicios pagados para registrar como vendidos.'
                ]);
            }
            return redirect()->route('servicio-consumo.create', $servicioConsumo->entry_id)
                ->with('error', 'No hay servicios pagados para registrar como vendidos.');
        }

        $totalVenta = $detallesPagadosNoVendidos->sum('subtotal');
        $clienteNombre = $servicioConsumo->entry->client->name . ' ' . ($servicioConsumo->entry->client->lastname ?? '');
        $habitacionNumero = $servicioConsumo->entry->room->room_number;

        // Calcular los montos por forma de pago
        $montosPorFormaPago = [
            'efectivo' => 0,
            'mercadopago' => 0,
            'tarjeta' => 0,
            'transferencia' => 0,
        ];

        foreach ($detallesPagadosNoVendidos as $detalle) {
            $formaPago = strtolower($detalle->forma_pago);
            if (array_key_exists($formaPago, $montosPorFormaPago)) {
                $montosPorFormaPago[$formaPago] += $detalle->subtotal;
            }
        }

        // Obtener el arqueo abierto
        $arqueoId = Arqueo::whereNull('fecha_cierre')->first()->id ?? 1; // Ajusta según tu lógica para el arqueo

        // Crear el registro en movimiento_cajas
        MovimientoCaja::create([
            'tipo' => 'Ingreso',
            'clase' => 'Servicio',
            'monto' => $totalVenta,
            'efectivo' => $montosPorFormaPago['efectivo'],
            'mercadopago' => $montosPorFormaPago['mercadopago'],
            'tarjeta' => $montosPorFormaPago['tarjeta'],
            'transferencia' => $montosPorFormaPago['transferencia'],
            'descripcion' => "Venta de servicio en la habitación {$habitacionNumero}",
            'usuario_id' => auth()->id(),
            'arqueo_id' => $arqueoId,
        ]);

        // Registrar el ingreso en la tabla pagos
        $descripcionPago = "Pago de servicio en la habitación {$habitacionNumero} (Cliente: {$clienteNombre})";
        \App\Models\Pago::create([
            'fecha' => now(),
            'descripcion' => $descripcionPago,
            'clase' => 'Servicio',
            'room_id' => $servicioConsumo->entry->room_id,
            'monto' => $totalVenta,
            'efectivo' => $montosPorFormaPago['efectivo'],
            'mercadopago' => $montosPorFormaPago['mercadopago'],
            'tarjeta' => $montosPorFormaPago['tarjeta'],
            'transferencia' => $montosPorFormaPago['transferencia'],
            'usuario_id' => auth()->id(),
            'arqueo_id' => $arqueoId,
        ]);

        // Marcar los detalles como vendidos
        $detallesPagadosNoVendidos->each(function ($detalle) {
            $detalle->update(['vendido' => true]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente.'
            ]);
        }

        return redirect()->route('servicio-consumo.create', $servicioConsumo->entry_id)
            ->with('success', 'Venta registrada correctamente.');
    }

    public function updatePaymentStatus(Request $request)
    {
        try {
            // Buscar el detalle
            $detalle = ServicioConsumoDetalle::findOrFail($request->detalle_id);
            $nuevoEstado = $request->estado;
            $formaPago = $request->forma_pago;

            // Bloquear modificaciones si el servicio ya está vendido
            if ($detalle->vendido) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede modificar el estado de un servicio que ya ha sido vendido.',
                ], 403);
            }

            // Bloquear el cambio de "Pagado" a "Pendiente"
            if ($detalle->estado === 'Pagado' && $nuevoEstado === 'Pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar el estado de "Pagado" a "Pendiente". Si hubo un error, elimine el servicio.',
                ], 403);
            }

            // Validar que se proporcione una forma de pago si el estado es "Pagado"
            if ($nuevoEstado === 'Pagado' && !$formaPago) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar una forma de pago.',
                ], 400);
            }

            // Actualizar el detalle
            $detalle->estado = $nuevoEstado;
            if ($nuevoEstado === 'Pagado') {
                $detalle->forma_pago = $formaPago;
            } else {
                $detalle->forma_pago = null;
            }
            $detalle->save();

            // Verificar si el servicioConsumo relacionado existe
            if (!$detalle->servicio_consumo_id) {
                Log::error('El detalle no tiene un servicio_consumo_id asociado.', ['detalle_id' => $detalle->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'El detalle no está asociado a un servicio de consumo.',
                ], 500);
            }

            $servicioConsumo = ServicioConsumo::find($detalle->servicio_consumo_id);
            if (!$servicioConsumo) {
                Log::error('No se encontró el ServicioConsumo relacionado.', ['servicio_consumo_id' => $detalle->servicio_consumo_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el servicio de consumo relacionado.',
                ], 500);
            }

            // Calcular el estado del servicioConsumo de forma segura
            $detalles = ServicioConsumoDetalle::where('servicio_consumo_id', $servicioConsumo->id)->get();
            $todosPagados = $detalles->count() > 0 && $detalles->every(function ($detalle) {
                return $detalle->estado === 'Pagado';
            });

            $servicioConsumo->estado = $todosPagados ? 'Pagado' : 'Falta Pagar';
            $servicioConsumo->save();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el estado de pago: ' . $e->getMessage(), [
                'detalle_id' => $request->detalle_id,
                'estado' => $request->estado,
                'forma_pago' => $request->forma_pago,
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function quitar(Request $request, $detalle_id)
    {
        $detalle = ServicioConsumoDetalle::findOrFail($detalle_id);
        $servicioConsumo = $detalle->servicioConsumo;

        if ($detalle->estado !== 'Pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede quitar un servicio que está pagado.',
            ], 403);
        }

        $detalle->delete();
        $servicioConsumo->recalculateTotal();

        return response()->json([
            'success' => true,
            'total' => $servicioConsumo->total,
        ]);
    }
}
