<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Consumo;
use App\Models\ConsumoDetalle;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Room;
use App\Models\ServicioConsumo;
use App\Models\ServicioConsumoDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        // Configurar idioma y zona horaria
        Carbon::setLocale('es');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
        $roomId = $request->query('room_id');

        // Obtener entradas activas con las relaciones necesarias
        $query = Entry::with([
            'room',
            'roomType',
            'client',
            'renovations',
            'consumo.detalles.producto',
            'servicioConsumo.detalles.servicio'
        ])
            ->where('status', 'Active')
            ->where('salida', 0); // Solo habitaciones ocupadas

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        $entradas = $query->get();

        // Log para depuración
        Log::info('Entradas activas para salida:', $entradas->toArray());

        // Mapear las entradas para calcular totales, deudas y fecha de salida
        $entradas = $entradas->map(function ($entry) use ($currentDateTime) {
            // Calcular el total del alquiler (incluye renovaciones)
            $totalAlquiler = $entry->total + $entry->renovations->sum('total');

            // Calcular el total de consumos y servicios
            $totalConsumos = $entry->consumo ? $entry->consumo->detalles->sum('subtotal') : 0;
            $totalServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->sum('subtotal') : 0;
            $totalAdicionales = $totalConsumos + $totalServicios;

            // Calcular el total pagado (alquiler + renovaciones + consumos/servicios pagados)
            $pagadoAlquiler = $entry->efectivo + $entry->mercadopago + $entry->tarjeta + $entry->transferencia +
                $entry->renovations->sum('efectivo') + $entry->renovations->sum('mercadopago') +
                $entry->renovations->sum('tarjeta') + $entry->renovations->sum('transferencia');
            $pagadoConsumos = $entry->consumo ? $entry->consumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
            $pagadoServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
            $entry->totalPagado = $pagadoAlquiler + $pagadoConsumos + $pagadoServicios;

            // Calcular la deuda pendiente
            $deudaConsumos = $entry->consumo ? $entry->consumo->detalles->where('estado', 'Falta Pagar')->sum('subtotal') : 0;
            $deudaServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->where('estado', 'Falta Pagar')->sum('subtotal') : 0;
            $entry->saldoDeudor = ($totalAlquiler + $totalAdicionales) - $entry->totalPagado + $deudaConsumos + $deudaServicios;

            // Total a pagar
            $entry->totalAPagar = $totalAlquiler + $totalAdicionales;

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

        return view('salidas.index', compact('entradas'));
    }

    public function show(Entry $entry)
    {
        // Configurar idioma y zona horaria
        Carbon::setLocale('es');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        // Verificar si la salida ya se ha efectuado
        if ($entry->salida == 1) {
            return redirect()->route('salidas.index')
                ->with('info', 'La salida para esta entrada ya ha sido efectuada.');
        }

        // Cargar relaciones necesarias
        $entry->load([
            'room',
            'roomType',
            'client',
            'renovations.roomTypeTariff',
            'roomTypeTariff',
            'consumo.detalles.producto',
            'servicioConsumo.detalles.servicio'
        ]);

        // Log para depurar las relaciones
        Log::info('Relaciones cargadas en show:', [
            'entry_id' => $entry->id,
            'room_type_tariff_id' => $entry->room_type_tariff_id,
            'roomTypeTariff' => $entry->roomTypeTariff ? $entry->roomTypeTariff->toArray() : 'NULL',
            'renovations' => $entry->renovations->toArray(),
            'latest_renovation_room_type_tariff_id' => $entry->renovations->isNotEmpty() ? $entry->renovations->sortByDesc('check_out')->first()->room_type_tariff_id : 'No renovations',
        ]);

        // Log para depurar consumos y servicios
        Log::info('Consumos y servicios cargados:', [
            'entry_id' => $entry->id,
            'consumo' => $entry->consumo ? $entry->consumo->toArray() : 'NULL',
            'consumo_detalles' => $entry->consumo && $entry->consumo->detalles ? $entry->consumo->detalles->toArray() : 'NULL',
            'servicioConsumo' => $entry->servicioConsumo ? $entry->servicioConsumo->toArray() : 'NULL',
            'servicioConsumo_detalles' => $entry->servicioConsumo && $entry->servicioConsumo->detalles ? $entry->servicioConsumo->detalles->toArray() : 'NULL',
        ]);

        // Determinar la tarifa a mostrar
        $tariffToShow = null;
        $tariffName = 'OTRO'; // Valor por defecto
        if ($entry->renovations->isNotEmpty()) {
            $latestRenovation = $entry->renovations->sortByDesc('check_out')->first();
            Log::info('Latest renovation details:', ['renewal' => $latestRenovation->toArray()]);
            if ($latestRenovation && $latestRenovation->room_type_tariff_id) {
                $tariffToShow = $latestRenovation->roomTypeTariff;
                $tariffName = $tariffToShow ? ($tariffToShow->name . ' (' . $tariffToShow->type . ')') : 'OTRO';
                Log::info('Tariff from latest renovation:', ['tariffName' => $tariffName]);
            } else {
                Log::warning('room_type_tariff_id is NULL in latest renovation');
            }
        }
        if (!$tariffToShow && $entry->room_type_tariff_id) {
            $tariffToShow = $entry->roomTypeTariff;
            $tariffName = $tariffToShow ? ($tariffToShow->name . ' (' . $tariffToShow->type . ')') : 'OTRO';
            Log::info('Tariff from entry:', ['tariffName' => $tariffName]);
        }

        if (!$tariffToShow) {
            $tariffName = $entry->roomType->name ?? 'PERSONALIZADO';
            Log::warning('No valid tariff found, using roomType as fallback:', ['tariffName' => $tariffName]);
        }

        // Calcular el total del alquiler (solo alquiler + renovaciones)
        $totalAlquiler = $entry->total + $entry->renovations->sum('total');

        // Calcular el total de consumos y servicios
        $totalConsumos = $entry->consumo ? $entry->consumo->detalles->sum('subtotal') : 0;
        $totalServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->sum('subtotal') : 0;
        $totalAdicionales = $totalConsumos + $totalServicios;

        // Calcular el total pagado para alquiler y renovaciones (excluye consumos/servicios)
        $pagadoAlquiler = $entry->efectivo + $entry->mercadopago + $entry->tarjeta + $entry->transferencia +
            $entry->renovations->sum('efectivo') + $entry->renovations->sum('mercadopago') +
            $entry->renovations->sum('tarjeta') + $entry->renovations->sum('transferencia');

        // Calcular el total pagado para consumos y servicios
        $pagadoConsumos = $entry->consumo ? $entry->consumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
        $pagadoServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
        $totalPagadoAdicionales = $pagadoConsumos + $pagadoServicios;

        // Calcular la deuda de consumos y servicios (ítems con estado "Falta Pagar" o "Pendiente")
        $deudaConsumos = $entry->consumo ? $entry->consumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
        $deudaServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
        $deudaConsumosServicios = $deudaConsumos + $deudaServicios;

        // Calcular la deuda del alquiler (solo alquiler + renovaciones - pagos de alquiler/renovaciones)
        $deudaAlquiler = $totalAlquiler - $pagadoAlquiler;

        // Total general (para "Totales" y "Pagar Deuda Pendiente")
        $totalGeneral = $totalAlquiler + $totalAdicionales;
        $totalPagadoGeneral = $pagadoAlquiler + $totalPagadoAdicionales;
        $deudaTotal = $totalGeneral - $totalPagadoGeneral;

        // Asignar valores a la entrada para la vista
        $entry->totalAlquiler = $totalAlquiler;
        $entry->pagadoAlquiler = $pagadoAlquiler;
        $entry->deudaAlquiler = $deudaAlquiler;
        $entry->totalAPagar = $totalGeneral;
        $entry->totalPagado = $totalPagadoGeneral;
        $entry->saldoDeudor = $deudaTotal;
        $entry->totalPagadoAdicionales = $totalPagadoAdicionales;

        // Log para depuración
        Log::info('Valores calculados en show:', [
            'entry_id' => $entry->id,
            'totalAlquiler' => $totalAlquiler,
            'totalAdicionales' => $totalAdicionales,
            'pagadoAlquiler' => $pagadoAlquiler,
            'totalPagadoAdicionales' => $totalPagadoAdicionales,
            'totalPagadoGeneral' => $totalPagadoGeneral,
            'deudaAlquiler' => $deudaAlquiler,
            'deudaTotal' => $deudaTotal,
            'deudaConsumosServicios' => $deudaConsumosServicios,
            'tariffName' => $tariffName,
        ]);

        return view('salidas.show', compact('entry', 'deudaConsumosServicios', 'tariffName'));
    }

    public function getDetails(Entry $entry)
    {
        // Cargar relaciones necesarias
        $entry->load([
            'renovations',
            'consumo.detalles.producto',
            'servicioConsumo.detalles.servicio'
        ]);

        // Calcular el total del alquiler (incluye renovaciones)
        $totalAlquiler = $entry->total + $entry->renovations->sum('total');

        // Calcular el total de consumos y servicios
        $totalConsumos = $entry->consumo ? $entry->consumo->detalles->sum('subtotal') : 0;
        $totalServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->sum('subtotal') : 0;
        $totalAdicionales = $totalConsumos + $totalServicios;

        // Calcular el total pagado (alquiler + renovaciones + consumos/servicios pagados)
        $pagadoAlquiler = $entry->efectivo + $entry->mercadopago + $entry->tarjeta + $entry->transferencia +
            $entry->renovations->sum('efectivo') + $entry->renovations->sum('mercadopago') +
            $entry->renovations->sum('tarjeta') + $entry->renovations->sum('transferencia');
        $pagadoConsumos = $entry->consumo ? $entry->consumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
        $pagadoServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->where('estado', 'Pagado')->sum('subtotal') : 0;
        $totalPagado = $pagadoAlquiler + $pagadoConsumos + $pagadoServicios;

        // Calcular la deuda de consumos y servicios (ítems con estado "Falta Pagar" o "Pendiente")
        $deudaConsumos = $entry->consumo ? $entry->consumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
        $deudaServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
        $deudaConsumosServicios = $deudaConsumos + $deudaServicios;

        // Calcular la deuda total (sin mora/penalidad)
        $saldoDeudor = ($totalAlquiler + $totalAdicionales) - $totalPagado;

        // Log para depuración
        Log::info('Valores calculados en getDetails:', [
            'entry_id' => $entry->id,
            'totalAlquiler' => $totalAlquiler,
            'totalAdicionales' => $totalAdicionales,
            'totalPagado' => $totalPagado,
            'saldoDeudor' => $saldoDeudor,
            'deudaConsumosServicios' => $deudaConsumosServicios,
        ]);

        // Consumos
        $consumos = Consumo::where('entry_id', $entry->id)
            ->with(['detalles.producto'])
            ->get()
            ->flatMap(function ($consumo) {
                return $consumo->detalles->map(function ($detalle) use ($consumo) {
                    return [
                        'id' => $detalle->id,
                        'tipo' => 'Consumo',
                        'nombre' => $detalle->producto->producto ?? 'N/A',
                        'cantidad' => $detalle->cantidad,
                        'precio' => floatval($detalle->precio),
                        'subtotal' => floatval($detalle->subtotal),
                        'estado' => $detalle->estado,
                    ];
                });
            });

        // Servicios
        $servicios = ServicioConsumo::where('entry_id', $entry->id)
            ->with(['detalles.servicio'])
            ->get()
            ->flatMap(function ($servicio) {
                return $servicio->detalles->map(function ($detalle) use ($servicio) {
                    return [
                        'id' => $detalle->id,
                        'tipo' => 'Servicio',
                        'nombre' => $detalle->servicio->nombre ?? 'N/A',
                        'cantidad' => $detalle->cantidad,
                        'precio' => floatval($detalle->precio_unitario),
                        'subtotal' => floatval($detalle->subtotal),
                        'estado' => $detalle->estado,
                    ];
                });
            });

        $items = $consumos->concat($servicios);

        return response()->json([
            'saldoDeudor' => $saldoDeudor,
            'deudaConsumosServicios' => $deudaConsumosServicios,
            'items' => $items,
        ]);
    }

    public function pagarConsumoServicio(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo;
        $metodoPago = $request->metodo_pago;

        $usuarioId = auth()->id() ?? 1; // ID del usuario autenticado

        try {
            // Obtener el arqueo abierto
            $arqueoId = $this->getArqueoAbierto();

            if ($tipo === 'Consumo') {
                $detalle = ConsumoDetalle::findOrFail($id);
                $consumo = $detalle->consumo;
                $entry = Entry::findOrFail($consumo->entry_id);
                $room = $entry->room;
                $client = $entry->client;

                // Actualizar el estado, forma de pago y vendido del ítem
                $detalle->estado = 'Pagado';
                $detalle->forma_pago = match ($metodoPago) {
                    'efectivo' => 'Efectivo',
                    'mercadopago' => 'MercadoPago',
                    'tarjeta' => 'Tarjeta',
                    'transferencia' => 'Transferencia',
                    default => 'Desconocido',
                };
                $detalle->vendido = 1;
                $detalle->save();

                // Verificar si todos los ítems del consumo están pagados
                $allItemsPaid = ConsumoDetalle::where('consumo_id', $consumo->id)
                    ->where('estado', '!=', 'Pagado')
                    ->doesntExist();

                if ($allItemsPaid) {
                    $consumo->estado = 'Pagado';
                    $consumo->save();
                }

                $descripcion = "Pago de consumo de habitación {$room->room_number}";
            } else {
                $detalle = ServicioConsumoDetalle::findOrFail($id);
                $servicio = $detalle->servicioConsumo;
                $entry = Entry::findOrFail($servicio->entry_id);
                $room = $entry->room;
                $client = $entry->client;

                // Actualizar el estado, forma de pago y vendido del ítem
                $detalle->estado = 'Pagado';
                $detalle->forma_pago = match ($metodoPago) {
                    'efectivo' => 'Efectivo',
                    'mercadopago' => 'MercadoPago',
                    'tarjeta' => 'Tarjeta',
                    'transferencia' => 'Transferencia',
                    default => 'Desconocido',
                };
                $detalle->vendido = 1;
                $detalle->save();

                // Verificar si todos los ítems del servicio están pagados
                $allItemsPaid = ServicioConsumoDetalle::where('servicio_consumo_id', $servicio->id)
                    ->where('estado', '!=', 'Pagado')
                    ->doesntExist();

                if ($allItemsPaid) {
                    $servicio->estado = 'Pagado';
                    $servicio->save();
                }

                $descripcion = "Pago de servicio de habitación {$room->room_number}";
            }

            // Registrar en movimiento_cajas
            MovimientoCaja::create([
                'tipo' => 'Ingreso',
                'clase' => $tipo === 'Consumo' ? 'Consumo' : 'Servicio',
                'monto' => $detalle->subtotal,
                'efectivo' => $metodoPago === 'efectivo' ? $detalle->subtotal : 0,
                'mercadopago' => $metodoPago === 'mercadopago' ? $detalle->subtotal : 0,
                'tarjeta' => $metodoPago === 'tarjeta' ? $detalle->subtotal : 0,
                'transferencia' => $metodoPago === 'transferencia' ? $detalle->subtotal : 0,
                'descripcion' => $descripcion,
                'usuario_id' => $usuarioId,
                'arqueo_id' => $arqueoId,
            ]);

            // Registrar en pagos
            Pago::create([
                'fecha' => now(),
                'descripcion' => $descripcion,
                'clase' => $tipo === 'Consumo' ? 'Consumo' : 'Servicio',
                'room_id' => $room->id,
                'monto' => $detalle->subtotal,
                'efectivo' => $metodoPago === 'efectivo' ? $detalle->subtotal : 0,
                'mercadopago' => $metodoPago === 'mercadopago' ? $detalle->subtotal : 0,
                'tarjeta' => $metodoPago === 'tarjeta' ? $detalle->subtotal : 0,
                'transferencia' => $metodoPago === 'transferencia' ? $detalle->subtotal : 0,
                'usuario_id' => $usuarioId,
                'arqueo_id' => $arqueoId,
            ]);

            return response()->json(['success' => true, 'message' => 'Pago registrado correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al registrar el pago de consumo/servicio: ' . $e->getMessage(), [
                'id' => $id,
                'tipo' => $tipo,
                'metodo_pago' => $metodoPago,
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkout(Request $request, Entry $entry)
    {
        // Validar los datos del formulario
        $request->validate([
            'total_a_pagar' => 'required|numeric|min:0',
            'mora_penalidad' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,mercadopago,tarjeta,transferencia',
            'monto' => 'required|numeric|min:0',
        ]);

        // Obtener los datos del formulario
        $totalAPagar = $request->input('total_a_pagar');
        $moraPenalidad = $request->input('mora_penalidad');
        $metodoPago = $request->input('metodo_pago');
        $monto = $request->input('monto');

        // Log inicial para depuración
        Log::info('Iniciando checkout:', [
            'entry_id' => $entry->id,
            'total_a_pagar' => $totalAPagar,
            'mora_penalidad' => $moraPenalidad,
            'metodo_pago' => $metodoPago,
            'monto' => $monto,
        ]);

        // Verificar si el monto ingresado es suficiente
        if ($totalAPagar > 0 && $monto < $totalAPagar) {
            return redirect()->back()->with('error', 'El monto ingresado es insuficiente para cubrir la deuda pendiente y la mora/penalidad.');
        }

        // Iniciar una transacción para asegurar consistencia
        DB::beginTransaction();

        try {
            // Obtener el usuario autenticado y el arqueo abierto
            $usuarioId = auth()->id() ?? 1;
            $arqueoId = $this->getArqueoAbierto();

            // Cargar relaciones necesarias
            $entry->load(['renovations', 'room', 'client']);

            // Calcular el total del alquiler y renovaciones
            $totalEntry = $entry->total ?? 0;
            $totalRenovations = $entry->renovations->sum('total') ?? 0;
            $totalAlquiler = $totalEntry + $totalRenovations;

            // Calcular el total de consumos y servicios
            $totalConsumos = $entry->consumo ? $entry->consumo->detalles->sum('subtotal') : 0;
            $totalServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->sum('subtotal') : 0;
            $totalAdicionales = $totalConsumos + $totalServicios;

            // Calcular el total pagado existente (solo alquiler y renovaciones)
            $pagadoEntry = ($entry->efectivo ?? 0) + ($entry->mercadopago ?? 0) + ($entry->tarjeta ?? 0) + ($entry->transferencia ?? 0);
            $pagadoRenovations = $entry->renovations->sum('efectivo') + $entry->renovations->sum('mercadopago') +
                $entry->renovations->sum('tarjeta') + $entry->renovations->sum('transferencia');
            $totalPagadoAlquiler = $pagadoEntry + $pagadoRenovations;

            // Calcular el total pagado existente para consumos y servicios
            $pagadoConsumos = $entry->consumo ? $entry->consumo->detalles->whereIn('estado', 'Pagado')->sum('subtotal') : 0;
            $pagadoServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->whereIn('estado', 'Pagado')->sum('subtotal') : 0;
            $totalPagadoAdicionales = $pagadoConsumos + $pagadoServicios;

            // Calcular la deuda actual del alquiler (sin consumos/servicios)
            $deudaAlquiler = $totalAlquiler - $totalPagadoAlquiler;

            // Calcular la deuda de consumos y servicios (ítems con estado "Falta Pagar" o "Pendiente")
            $deudaConsumos = $entry->consumo ? $entry->consumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
            $deudaServicios = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente'])->sum('subtotal') : 0;
            $deudaAdicionales = $deudaConsumos + $deudaServicios;

            // Calcular la deuda total (alquiler + consumos/servicios + mora)
            $deudaTotal = $deudaAlquiler + $deudaAdicionales + $moraPenalidad;

            // Log de los cálculos iniciales
            Log::info('Cálculos iniciales:', [
                'entry_id' => $entry->id,
                'total_alquiler' => $totalAlquiler,
                'total_adicionales' => $totalAdicionales,
                'total_pagado_alquiler' => $totalPagadoAlquiler,
                'total_pagado_adicionales' => $totalPagadoAdicionales,
                'deuda_alquiler' => $deudaAlquiler,
                'deuda_adicionales' => $deudaAdicionales,
                'deuda_total' => $deudaTotal,
            ]);

            // Variable para rastrear el monto total pagado en esta operación
            $montoPagadoTotal = 0;

            // Si hay algo que pagar, distribuir el monto pagado
            if ($totalAPagar > 0) {
                $montoRestante = $monto;

                // 1. Pagar consumos y servicios pendientes primero
                $montoPagadoAdicionales = 0;
                if ($deudaAdicionales > 0 && $montoRestante > 0) {
                    $montoParaAdicionales = min($deudaAdicionales, $montoRestante);
                    $montoRestante -= $montoParaAdicionales;
                    $montoPagadoAdicionales = $montoParaAdicionales;

                    // Actualizar los consumos pendientes
                    if ($deudaConsumos > 0) {
                        $consumoDetalles = $entry->consumo ? $entry->consumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente']) : collect([]);
                        foreach ($consumoDetalles as $detalle) {
                            if ($montoParaAdicionales <= 0) break;
                            $montoParaDetalle = min($detalle->subtotal, $montoParaAdicionales);
                            $detalle->estado = 'Pagado';
                            $detalle->forma_pago = match ($metodoPago) {
                                'efectivo' => 'Efectivo',
                                'mercadopago' => 'MercadoPago',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                                default => 'Desconocido',
                            };
                            $detalle->vendido = 1;
                            $detalle->save();
                            $montoParaAdicionales -= $montoParaDetalle;
                        }
                        // Verificar si todos los ítems del consumo están pagados
                        $allItemsPaid = $entry->consumo ? $entry->consumo->detalles->where('estado', '!=', 'Pagado')->doesntExist() : true;
                        if ($allItemsPaid && $entry->consumo) {
                            $entry->consumo->estado = 'Pagado';
                            $entry->consumo->save();
                        }
                    }

                    // Actualizar los servicios pendientes
                    if ($deudaServicios > 0 && $montoParaAdicionales > 0) {
                        $servicioDetalles = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->whereIn('estado', ['Falta Pagar', 'Pendiente']) : collect([]);
                        foreach ($servicioDetalles as $detalle) {
                            if ($montoParaAdicionales <= 0) break;
                            $montoParaDetalle = min($detalle->subtotal, $montoParaAdicionales);
                            $detalle->estado = 'Pagado';
                            $detalle->forma_pago = match ($metodoPago) {
                                'efectivo' => 'Efectivo',
                                'mercadopago' => 'MercadoPago',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                                default => 'Desconocido',
                            };
                            $detalle->vendido = 1;
                            $detalle->save();
                            $montoParaAdicionales -= $montoParaDetalle;
                        }
                        // Verificar si todos los ítems del servicio están pagados
                        $allItemsPaid = $entry->servicioConsumo ? $entry->servicioConsumo->detalles->where('estado', '!=', 'Pagado')->doesntExist() : true;
                        if ($allItemsPaid && $entry->servicioConsumo) {
                            $entry->servicioConsumo->estado = 'Pagado';
                            $entry->servicioConsumo->save();
                        }
                    }
                }

                // 2. Distribuir el monto restante entre la entrada y las renovaciones
                $montoPagadoAlquiler = 0;
                if ($deudaAlquiler > 0 && $montoRestante > 0) {
                    // Calcular la deuda de la entrada y las renovaciones por separado
                    $deudaEntry = $totalEntry - $pagadoEntry;
                    $deudaRenovations = $totalRenovations - $pagadoRenovations;

                    // Calcular el porcentaje de deuda de cada parte
                    $totalDeudaAlquiler = $deudaEntry + $deudaRenovations;
                    $proporcionEntry = $totalDeudaAlquiler > 0 ? $deudaEntry / $totalDeudaAlquiler : 0;
                    $proporcionRenovations = $totalDeudaAlquiler > 0 ? $deudaRenovations / $totalDeudaAlquiler : 0;

                    // Calcular cuánto se paga a cada parte (incluyendo mora)
                    $montoParaAlquiler = min($deudaAlquiler + $moraPenalidad, $montoRestante);
                    $montoRestante -= $montoParaAlquiler;
                    $montoPagadoAlquiler = $montoParaAlquiler;

                    $montoParaEntry = $totalDeudaAlquiler > 0 ? $montoParaAlquiler * $proporcionEntry : 0;
                    $montoParaRenovations = $totalDeudaAlquiler > 0 ? $montoParaAlquiler * $proporcionRenovations : 0;

                    // Ajustar si no hay renovaciones
                    if ($entry->renovations->isEmpty()) {
                        $montoParaEntry = $montoParaAlquiler;
                        $montoParaRenovations = 0;
                    }

                    // 2.1 Actualizar la entrada (`entries`)
                    if ($montoParaEntry > 0) {
                        $entry->{$metodoPago} = ($entry->{$metodoPago} ?? 0) + $montoParaEntry;
                        $entry->payment_received = ($entry->payment_received ?? 0) + $montoParaEntry;
                        $entry->debt = max(0, $deudaEntry - $montoParaEntry);
                        if ($entry->debt == 0) {
                            $entry->pago = 'Pagado';
                        }
                        $entry->save();
                    } else {
                        $entry->debt = $deudaEntry;
                        $entry->save();
                    }

                    // 2.2 Actualizar las renovaciones (`renewals`)
                    if ($montoParaRenovations > 0 && $entry->renovations->isNotEmpty()) {
                        $montoRestanteRenovations = $montoParaRenovations;
                        foreach ($entry->renovations as $renovation) {
                            if ($montoRestanteRenovations <= 0) break;

                            $pagadoRenovation = ($renovation->efectivo ?? 0) + ($renovation->mercadopago ?? 0) +
                                ($renovation->tarjeta ?? 0) + ($renovation->transferencia ?? 0);
                            $deudaRenovation = ($renovation->total ?? 0) - $pagadoRenovation;

                            if ($deudaRenovation <= 0) continue;

                            $montoParaEstaRenovation = min($deudaRenovation, $montoRestanteRenovations);
                            $renovation->{$metodoPago} = ($renovation->{$metodoPago} ?? 0) + $montoParaEstaRenovation;
                            $renovation->debt = max(0, $deudaRenovation - $montoParaEstaRenovation);
                            if ($renovation->debt == 0) {
                                $renovation->pago = 'Pagado';
                            }
                            $renovation->save();

                            $montoRestanteRenovations -= $montoParaEstaRenovation;
                        }
                    }
                } else {
                    Log::warning('No se pagó alquiler:', [
                        'entry_id' => $entry->id,
                        'deuda_alquiler' => $deudaAlquiler,
                        'monto_restante' => $montoRestante,
                    ]);
                }

                // 3. Calcular el monto total pagado en esta operación
                $montoPagadoTotal = $montoPagadoAdicionales + $montoPagadoAlquiler;

                // Log del monto pagado
                Log::info('Monto pagado calculado:', [
                    'entry_id' => $entry->id,
                    'monto_pagado_adicionales' => $montoPagadoAdicionales,
                    'monto_pagado_alquiler' => $montoPagadoAlquiler,
                    'monto_pagado_total' => $montoPagadoTotal,
                ]);
            } else {
                Log::warning('No se procesó ningún pago porque total_a_pagar es 0:', [
                    'entry_id' => $entry->id,
                    'total_a_pagar' => $totalAPagar,
                ]);
            }

            // 4. Actualizar la entrada en la tabla `entries`
            $entry->status = 'Finished';
            $entry->salida = 1;
            $entry->save();

            // 5. Actualizar el estado de la habitación en la tabla `rooms`
            $room = Room::find($entry->room_id);
            if ($room) {
                $room->status = 'Para la Limpieza';
                $room->save();
            } else {
                Log::error('Habitación no encontrada para la entrada', ['entry_id' => $entry->id, 'room_id' => $entry->room_id]);
                throw new \Exception('Habitación no encontrada.');
            }

            // 6. Registrar el pago total en `movimiento_cajas` y `pagos` justo antes de confirmar la transacción
            if ($montoPagadoTotal > 0) {
                $room = $entry->room;
                $client = $entry->client;

                try {
                    // Registrar en `movimiento_cajas`
                    $movimiento = MovimientoCaja::create([
                        'tipo' => 'Ingreso',
                        'clase' => 'Alquiler',
                        'monto' => $montoPagadoTotal,
                        'efectivo' => $metodoPago === 'efectivo' ? $montoPagadoTotal : 0,
                        'mercadopago' => $metodoPago === 'mercadopago' ? $montoPagadoTotal : 0,
                        'tarjeta' => $metodoPago === 'tarjeta' ? $montoPagadoTotal : 0,
                        'transferencia' => $metodoPago === 'transferencia' ? $montoPagadoTotal : 0,
                        'descripcion' => "Pago de salida de habitación {$room->room_number}",
                        'usuario_id' => $usuarioId,
                        'arqueo_id' => $arqueoId,
                    ]);

                    // Registrar en `pagos`
                    $pago = Pago::create([
                        'fecha' => now(),
                        'descripcion' => "Pago de salida de habitación {$room->room_number} (Cliente: {$client->name} {$client->lastname})",
                        'clase' => 'Alquiler',
                        'room_id' => $room->id,
                        'monto' => $montoPagadoTotal,
                        'efectivo' => $metodoPago === 'efectivo' ? $montoPagadoTotal : 0,
                        'mercadopago' => $metodoPago === 'mercadopago' ? $montoPagadoTotal : 0,
                        'tarjeta' => $metodoPago === 'tarjeta' ? $montoPagadoTotal : 0,
                        'transferencia' => $metodoPago === 'transferencia' ? $montoPagadoTotal : 0,
                        'usuario_id' => $usuarioId,
                        'arqueo_id' => $arqueoId,
                    ]);

                    Log::info('Movimiento registrado en movimiento_cajas y pagos:', [
                        'entry_id' => $entry->id,
                        'monto_pagado_total' => $montoPagadoTotal,
                        'metodo_pago' => $metodoPago,
                        'movimiento_caja_id' => $movimiento->id,
                        'pago_id' => $pago->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al registrar movimiento en movimiento_cajas o pagos:', [
                        'entry_id' => $entry->id,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e; // Re-lanzar la excepción para que la transacción se revierta
                }
            } else {
                Log::warning('No se registró movimiento porque monto_pagado_total es 0:', [
                    'entry_id' => $entry->id,
                    'monto_pagado_total' => $montoPagadoTotal,
                ]);
            }

            // Log para depuración
            Log::info('Salida culminada con éxito:', [
                'entry_id' => $entry->id,
                'total_a_pagar' => $totalAPagar,
                'mora_penalidad' => $moraPenalidad,
                'metodo_pago' => $metodoPago,
                'monto' => $monto,
                'monto_pagado_total' => $montoPagadoTotal,
                'room_id' => $entry->room_id,
                'room_status' => $room->status,
            ]);

            // Confirmar la transacción
            DB::commit();

            return redirect()->route('entradas.panel-control')->with('success', 'Salida culminada con éxito.');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            Log::error('Error al culminar la salida:', [
                'entry_id' => $entry->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Error al culminar la salida: ' . $e->getMessage());
        }
    }

    private function getArqueoAbierto()
    {
        $arqueo = \App\Models\Arqueo::where('status', 'Abierto')
            ->whereNull('fecha_cierre')
            ->first();

        if (!$arqueo) {
            Log::error('No se encontró un arqueo abierto para registrar el movimiento.');
            throw new \Exception('No hay un arqueo abierto. Por favor, abra un nuevo arqueo antes de registrar pagos.');
        }

        return $arqueo->id;
    }
}
