<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Entry;
use App\Models\Consumo;
use App\Models\ConsumoDetalle;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\ServicioConsumo;
use App\Models\ServicioConsumoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    public function index()
    {
        // Obtener los pagos con las relaciones usuario, arqueo y room, ordenados por fecha descendente
        $pagos = Pago::with(['usuario', 'arqueo', 'room'])
            ->orderBy('fecha', 'desc')
            ->get();

        return view('caja.pagos.index', compact('pagos'));
    }

    public function create(Request $request)
    {
        $rooms = Room::whereHas('entries', function ($query) {
            $query->where('status', 'Active');
        })->with(['entries' => function ($query) {
            $query->where('status', 'Active')
                ->with('client');
        }])->get()->map(function ($room) {
            $entry = $room->entries->first();
            $room->client_name = $entry->client->name ?? 'N/A';
            $room->client_lastname = $entry->client->lastname ?? '';
            $room->debt = $entry->debt ?? 0;
            return $room;
        });

        // Obtener el room_id de la URL (si está presente)
        $selectedRoomId = $request->query('room_id');

        return view('caja.pagos.create', compact('rooms', 'selectedRoomId'));
    }

    public function getDetails(Request $request)
    {
        $roomId = $request->input('room_id');
        Log::info('getDetails llamado con room_id: ' . $roomId);

        $entry = Entry::where('room_id', $roomId)
            ->where('status', 'Active')
            ->with(['client', 'renovations'])
            ->first();

        if (!$entry) {
            Log::error('Entrada no encontrada para room_id: ' . $roomId);
            return response()->json(['error' => 'Entrada no encontrada'], 404);
        }

        // Calcular payment_received y deuda para la entrada
        $entryPaymentReceived = (
            ($entry->efectivo ?? 0) +
            ($entry->mercadopago ?? 0) +
            ($entry->tarjeta ?? 0) +
            ($entry->transferencia ?? 0)
        );
        $entryTotal = $entry->total ?? 0;
        $entryDiscount = $entry->discount ?? 0;
        $entryDebt = max(0, ($entryTotal - $entryDiscount) - $entryPaymentReceived);

        // Calcular el total, descuento, pagos y deuda de las renovaciones
        $renovationTotal = 0;
        $renovationDiscount = 0;
        $renovationPaymentReceived = 0;
        $renovationDebt = 0;
        $hasRenovations = $entry->renovations->isNotEmpty();

        foreach ($entry->renovations as $renovation) {
            $renovationPaymentReceived += (
                ($renovation->efectivo ?? 0) +
                ($renovation->mercadopago ?? 0) +
                ($renovation->tarjeta ?? 0) +
                ($renovation->transferencia ?? 0)
            );
            $renovationTotal += $renovation->total ?? 0;
            $renovationDiscount += $renovation->discount ?? 0;
        }
        $renovationDebt = max(0, ($renovationTotal - $renovationDiscount) - $renovationPaymentReceived);

        // Calcular la deuda total (para la entrada y para mostrar en la UI)
        $totalDebt = $entryDebt + $renovationDebt;
        $entry->debt = $totalDebt;
        $entry->save();

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
                        'estado' => $detalle->estado == 'Pagado' ? 'Pagado' : 'Falta Pagar',
                    ];
                });
            });

        Log::info('Consumos obtenidos:', $consumos->toArray());

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
                        'estado' => $detalle->estado == 'Pagado' ? 'Pagado' : 'Falta Pagar',
                    ];
                });
            });

        Log::info('Servicios obtenidos:', $servicios->toArray());

        return response()->json([
            'entry' => [
                'id' => $entry->id,
                'total' => $entryTotal,
                'discount' => $entryDiscount,
                'payment_received_entry' => $entryPaymentReceived,
                'debt_entry' => $entryDebt,
                'renovation_total' => $renovationTotal,
                'renovation_discount' => $renovationDiscount,
                'payment_received_renovation' => $renovationPaymentReceived,
                'debt_renovation' => $renovationDebt,
                'total_debt' => $totalDebt,
                'has_renovations' => $hasRenovations,
            ],
            'consumos' => $consumos,
            'servicios' => $servicios,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'entry_id' => 'required|exists:entries,id',
            'payment_type' => 'required|in:alquiler,renovacion',
            'metodo_pago' => 'required|in:efectivo,mercadopago,tarjeta,transferencia',
            'importe' => 'required|numeric|min:0',
        ]);

        try {
            $entry = Entry::with('renovations')->findOrFail($request->entry_id);
            $room = Room::findOrFail($request->room_id);
            $client = $entry->client;

            // Calcular valores para la entrada antes del pago
            $entryPaymentReceived = (
                ($entry->efectivo ?? 0) +
                ($entry->mercadopago ?? 0) +
                ($entry->tarjeta ?? 0) +
                ($entry->transferencia ?? 0)
            );
            $entryTotal = $entry->total ?? 0;
            $entryDiscount = $entry->discount ?? 0;
            $entryDebtBeforePayment = max(0, ($entryTotal - $entryDiscount) - $entryPaymentReceived);

            // Calcular valores para las renovaciones antes del pago
            $renovationTotal = 0;
            $renovationDiscount = 0;
            $renovationPaymentReceived = 0;

            foreach ($entry->renovations as $renovation) {
                $renovationPaymentReceived += (
                    ($renovation->efectivo ?? 0) +
                    ($renovation->mercadopago ?? 0) +
                    ($renovation->tarjeta ?? 0) +
                    ($renovation->transferencia ?? 0)
                );
                $renovationTotal += $renovation->total ?? 0;
                $renovationDiscount += $renovation->discount ?? 0;
            }
            $renovationDebtBeforePayment = max(0, ($renovationTotal - $renovationDiscount) - $renovationPaymentReceived);

            // Deuda total antes del pago
            $totalDebtBeforePayment = $entryDebtBeforePayment + $renovationDebtBeforePayment;

            // Obtener consumos y servicios impagos para calcular la deuda total incluyendo consumos/servicios
            $consumosImpagos = Consumo::where('entry_id', $entry->id)
                ->with(['detalles' => function ($query) {
                    $query->where('estado', 'Falta Pagar');
                }])
                ->get()
                ->flatMap(function ($consumo) {
                    return $consumo->detalles->pluck('subtotal');
                })
                ->sum();

            $serviciosImpagos = ServicioConsumo::where('entry_id', $entry->id)
                ->with(['detalles' => function ($query) {
                    $query->where('estado', 'Falta Pagar');
                }])
                ->get()
                ->flatMap(function ($servicio) {
                    return $servicio->detalles->pluck('subtotal');
                })
                ->sum();

            $totalDebtIncludingConsumos = $totalDebtBeforePayment + $consumosImpagos + $serviciosImpagos;

            // Verificar que el importe no sea mayor a la deuda total
            if ($request->importe > $totalDebtIncludingConsumos) {
                return redirect()->back()->with('error', 'El importe no puede ser mayor a la deuda total.')->withInput();
            }

            // Obtener el arqueo abierto
            $arqueoId = $this->getArqueoAbierto();

            // Determinar si el pago es para la entrada o la renovación
            $metodoPago = $request->metodo_pago;
            $paymentType = $request->payment_type;

            if ($paymentType === 'alquiler') {
                // Actualizar el método de pago en la entrada
                $entry->$metodoPago = ($entry->$metodoPago ?? 0) + $request->importe;
                $entry->save();

                $descripcionMovimiento = "Pago de alquiler de habitación {$room->room_number}";
                $descripcionPago = "Pago de alquiler de habitación {$room->room_number} (Cliente: {$client->name} {$client->lastname})";
            } else {
                // Pago para la renovación (última renovación)
                $renovation = $entry->renovations->last();
                if (!$renovation) {
                    return redirect()->back()->with('error', 'No hay renovaciones disponibles para esta entrada.')->withInput();
                }

                $renovation->$metodoPago = ($renovation->$metodoPago ?? 0) + $request->importe;
                $renovation->save();
                // Calcular y actualizar la deuda de la renovación después del pago
                $renovationPaymentReceivedAfter = (
                    ($renovation->efectivo ?? 0) +
                    ($renovation->mercadopago ?? 0) +
                    ($renovation->tarjeta ?? 0) +
                    ($renovation->transferencia ?? 0)
                );
                $renovationDebtAfterPayment = max(0, ($renovation->total - ($renovation->discount ?? 0)) - $renovationPaymentReceivedAfter);
                $renovation->debt = $renovationDebtAfterPayment;
                // Actualizar el campo pago de la renovación
                $renovation->pago = $renovationDebtAfterPayment == 0 ? 'Pagado' : 'Falta Pagar';
                $renovation->save();

                $descripcionMovimiento = "Pago de renovación de habitación {$room->room_number}";
                $descripcionPago = "Pago de renovación de habitación {$room->room_number} (Cliente: {$client->name} {$client->lastname})";
            }

            // Recalcular valores después del pago
            $entryPaymentReceived = (
                ($entry->efectivo ?? 0) +
                ($entry->mercadopago ?? 0) +
                ($entry->tarjeta ?? 0) +
                ($entry->transferencia ?? 0)
            );
            $entryDebtAfterPayment = max(0, ($entryTotal - $entryDiscount) - $entryPaymentReceived);

            $renovationPaymentReceived = 0;
            $renovationTotal = 0;
            $renovationDiscount = 0;
            foreach ($entry->renovations as $renovation) {
                $renovationPaymentReceived += (
                    ($renovation->efectivo ?? 0) +
                    ($renovation->mercadopago ?? 0) +
                    ($renovation->tarjeta ?? 0) +
                    ($renovation->transferencia ?? 0)
                );
                $renovationTotal += $renovation->total ?? 0;
                $renovationDiscount += $renovation->discount ?? 0;
            }
            $renovationDebtAfterPayment = max(0, ($renovationTotal - $renovationDiscount) - $renovationPaymentReceived);

            // Actualizar la deuda total
            $totalDebtAfterPayment = $entryDebtAfterPayment + $renovationDebtAfterPayment;
            $entry->debt = $totalDebtAfterPayment;
            // Actualizar el campo pago de la entrada
            $entry->pago = $entryDebtAfterPayment == 0 ? 'Pagado' : 'Falta Pagar';
            $entry->save();

            // Registrar en movimiento_cajas
            $usuarioId = auth()->id() ?? 1;

            MovimientoCaja::create([
                'tipo' => 'Ingreso',
                'clase' => $paymentType === 'alquiler' ? 'Alquiler' : 'Renovación',
                'monto' => $request->importe,
                'efectivo' => $metodoPago === 'efectivo' ? $request->importe : 0,
                'mercadopago' => $metodoPago === 'mercadopago' ? $request->importe : 0,
                'tarjeta' => $metodoPago === 'tarjeta' ? $request->importe : 0,
                'transferencia' => $metodoPago === 'transferencia' ? $request->importe : 0,
                'descripcion' => $descripcionMovimiento,
                'usuario_id' => $usuarioId,
                'arqueo_id' => $arqueoId,
            ]);

            // Registrar en pagos
            Pago::create([
                'fecha' => now(),
                'descripcion' => $descripcionPago,
                'clase' => $paymentType === 'alquiler' ? 'Alquiler' : 'Renovación',
                'room_id' => $request->room_id,
                'monto' => $request->importe,
                'efectivo' => $metodoPago === 'efectivo' ? $request->importe : 0,
                'mercadopago' => $metodoPago === 'mercadopago' ? $request->importe : 0,
                'tarjeta' => $metodoPago === 'tarjeta' ? $request->importe : 0,
                'transferencia' => $metodoPago === 'transferencia' ? $request->importe : 0,
                'usuario_id' => $usuarioId,
                'arqueo_id' => $arqueoId,
            ]);

            return redirect()->route('caja.pagos.create', ['room_id' => $request->room_id])
                ->with('success', 'Pago registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al registrar el pago: ' . $e->getMessage(), [
                'room_id' => $request->room_id,
                'entry_id' => $request->entry_id,
                'payment_type' => $request->payment_type,
                'metodo_pago' => $request->metodo_pago,
                'importe' => $request->importe,
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
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
                $room = Room::findOrFail($entry->room_id);
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
                $room = Room::findOrFail($entry->room_id);
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
