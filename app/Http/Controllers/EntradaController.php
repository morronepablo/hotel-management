<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Companion;
use App\Models\Entry;
use App\Models\Room;
use App\Models\Arqueo;
use App\Models\HotelSetting;
use App\Models\Level;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Usar DomPDF
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntradaController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->get();
        $currentDateTime = Carbon::now();

        $roomStatuses = $rooms->mapWithKeys(function ($room) use ($currentDateTime) {
            $hasActiveReservation = $room->reservations()
                ->where('check_in', '<=', $currentDateTime)
                ->where('check_out', '>=', $currentDateTime)
                ->exists();

            $hasActiveEntry = $room->entries()
                ->where('status', 'Active')
                ->where('check_in', '<=', $currentDateTime)
                ->where('check_out', '>=', $currentDateTime)
                ->exists();

            return [$room->id => !$hasActiveReservation && !$hasActiveEntry ? 'Disponible' : 'Ocupada'];
        });

        return view('entradas.index', compact('rooms', 'roomStatuses'));
    }

    public function create($roomId)
    {
        $room = Room::with('roomType')->findOrFail($roomId);
        $clients = Client::all();
        return view('entradas.create', compact('room', 'clients'));
    }

    // public function store(Request $request)
    // {
    //     // Registrar los datos recibidos para depuración
    //     Log::info('Datos recibidos en store: ' . json_encode($request->all()));

    //     // Validar los datos del formulario
    //     $request->validate([
    //         'room_id' => 'required|exists:rooms,id',
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'client_id' => 'required|exists:clients,id',
    //         'entry_type' => 'required|in:4_hours,full_night,month',
    //         'quantity' => 'required|integer|min:1',
    //         'check_in_date' => 'required|date',
    //         'check_in_time' => 'required',
    //         'check_out_date' => 'required|date',
    //         'check_out_time' => 'required',
    //         'efectivo' => 'nullable|numeric|min:0',
    //         'mercadopago' => 'nullable|numeric|min:0',
    //         'tarjeta' => 'nullable|numeric|min:0',
    //         'transferencia' => 'nullable|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0',
    //         'total' => 'required|numeric|min:0',
    //         'debt' => 'required|numeric',
    //         'observations' => 'nullable|string',
    //         'companions' => 'nullable|string',
    //     ]);

    //     $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
    //     $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

    //     DB::beginTransaction();
    //     try {
    //         // Verificar si hay un arqueo abierto
    //         $currentArqueo = Arqueo::where('status', 'Abierto')->first();
    //         if (!$currentArqueo) {
    //             throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una entrada.');
    //         }

    //         // Buscar la habitación
    //         $room = Room::findOrFail($request->room_id);

    //         // Verificar que la habitación esté disponible
    //         if ($room->status !== 'Disponible' && $room->status !== 'Reservada') {
    //             throw new \Exception('La habitación no está disponible para crear una entrada. Estado actual: ' . $room->status);
    //         }

    //         // Combinar fecha y hora para check_in y check_out
    //         $checkIn = Carbon::createFromFormat(
    //             'Y-m-d H:i',
    //             $request->check_in_date . ' ' . $request->check_in_time,
    //             'America/Argentina/Buenos_Aires'
    //         );

    //         $checkOut = Carbon::createFromFormat(
    //             'Y-m-d H:i',
    //             $request->check_out_date . ' ' . $request->check_out_time,
    //             'America/Argentina/Buenos_Aires'
    //         );

    //         // Validar que check_out sea posterior a check_in
    //         if ($checkOut->lte($checkIn)) {
    //             throw new \Exception('La fecha y hora de salida deben ser posteriores a la fecha y hora de entrada.');
    //         }

    //         // Verificar si existe una reserva confirmada que se solape con el rango de fechas
    //         $overlappingReservation = Reservation::where('room_id', $request->room_id)
    //             ->where('status', 'Confirmada')
    //             ->where(function ($query) use ($checkIn, $checkOut) {
    //                 $query->whereBetween('check_in', [$checkIn, $checkOut])
    //                     ->orWhereBetween('check_out', [$checkIn, $checkOut])
    //                     ->orWhere(function ($subQuery) use ($checkIn, $checkOut) {
    //                         $subQuery->where('check_in', '<=', $checkIn)
    //                             ->where('check_out', '>=', $checkOut);
    //                     });
    //             })
    //             ->first();

    //         // Si existe una reserva confirmada, actualizar su estado a "Ocupada"
    //         if ($overlappingReservation) {
    //             $overlappingReservation->update([
    //                 'status' => 'Ocupada',
    //             ]);
    //             Log::info("Reserva ID {$overlappingReservation->id} actualizada a 'Ocupada' para la habitación {$request->room_id}.");
    //         }

    //         // Crear la entrada
    //         $entry = Entry::create([
    //             'room_id' => $request->room_id,
    //             'room_type_id' => $request->room_type_id,
    //             'client_id' => $request->client_id,
    //             'entry_type' => $request->entry_type,
    //             'quantity' => $request->quantity,
    //             'check_in' => $checkIn,
    //             'check_out' => $checkOut,
    //             'efectivo' => $request->efectivo,
    //             'mercadopago' => $request->mercadopago,
    //             'tarjeta' => $request->tarjeta,
    //             'transferencia' => $request->transferencia,
    //             'discount' => $request->discount,
    //             'total' => $request->total,
    //             'debt' => $request->debt,
    //             'pago' => $pagoStatus,
    //             'observations' => $request->observations,
    //             'status' => 'Active',
    //         ]);

    //         // Actualizar el estado de la habitación a "Ocupada"
    //         $room->update([
    //             'status' => 'Ocupada',
    //         ]);

    //         $companionIds = [];
    //         if ($request->filled('companions')) {
    //             $companions = json_decode($request->companions, true);
    //             foreach ($companions as $companionData) {
    //                 $companion = Companion::create([
    //                     'name' => $companionData['name'],
    //                     'lastname' => $companionData['lastname'],
    //                     'dni' => $companionData['dni'],
    //                     'phone' => $companionData['phone'],
    //                     'email' => $companionData['email'] ?? null,
    //                 ]);
    //                 $companionIds[] = $companion->id;
    //             }
    //         }

    //         if (!empty($companionIds)) {
    //             $entry->companions()->attach($companionIds);
    //         }

    //         // Registrar movimiento en caja y pago en la tabla pagos
    //         if ($totalPagado > 0) {
    //             // Registrar en movimiento_cajas
    //             MovimientoCaja::create([
    //                 'arqueo_id' => $currentArqueo->id,
    //                 'tipo' => 'Ingreso',
    //                 'clase' => 'Alquiler',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'descripcion' => "Ingreso por entrada de habitación {$entry->room->room_number}",
    //                 'usuario_id' => auth()->id(),
    //             ]);

    //             // Registrar en pagos
    //             $descripcionPago = "Pago de alquiler de habitación {$entry->room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
    //             Pago::create([
    //                 'fecha' => now(),
    //                 'descripcion' => $descripcionPago,
    //                 'clase' => 'Alquiler',
    //                 'room_id' => $entry->room_id,
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'usuario_id' => auth()->id(),
    //                 'arqueo_id' => $currentArqueo->id,
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('entradas.panel-control')
    //             ->with('success', 'Entrada registrada correctamente.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al crear la entrada: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Error al crear la entrada: ' . $e->getMessage())->withInput();
    //     }
    // }



    // public function store(Request $request)
    // {
    //     // Registrar los datos recibidos para depuración
    //     Log::info('Datos recibidos en store: ' . json_encode($request->all()));

    //     // Validar los datos del formulario
    //     $request->validate([
    //         'room_id' => 'required|exists:rooms,id',
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'client_id' => 'required|exists:clients,id',
    //         'entry_type' => 'required|exists:room_type_tariffs,id', // Ahora validamos que sea un ID de tarifa
    //         'quantity' => 'required|integer|min:1',
    //         'check_in_date' => 'required|date',
    //         'check_in_time' => 'required',
    //         'check_out_date' => 'required|date',
    //         'check_out_time' => 'required',
    //         'efectivo' => 'nullable|numeric|min:0',
    //         'mercadopago' => 'nullable|numeric|min:0',
    //         'tarjeta' => 'nullable|numeric|min:0',
    //         'transferencia' => 'nullable|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0',
    //         'total' => 'required|numeric|min:0',
    //         'debt' => 'required|numeric',
    //         'observations' => 'nullable|string',
    //         'companions' => 'nullable|string',
    //     ]);

    //     $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
    //     $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

    //     DB::beginTransaction();
    //     try {
    //         // Verificar si hay un arqueo abierto
    //         $currentArqueo = Arqueo::where('status', 'Abierto')->first();
    //         if (!$currentArqueo) {
    //             throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una entrada.');
    //         }

    //         // Buscar la habitación
    //         $room = Room::findOrFail($request->room_id);

    //         // Verificar que la habitación esté disponible
    //         if ($room->status !== 'Disponible' && $room->status !== 'Reservada') {
    //             throw new \Exception('La habitación no está disponible para crear una entrada. Estado actual: ' . $room->status);
    //         }

    //         // Combinar fecha y hora para check_in y check_out
    //         $checkIn = Carbon::createFromFormat(
    //             'Y-m-d H:i',
    //             $request->check_in_date . ' ' . $request->check_in_time,
    //             'America/Argentina/Buenos_Aires'
    //         );

    //         $checkOut = Carbon::createFromFormat(
    //             'Y-m-d H:i',
    //             $request->check_out_date . ' ' . $request->check_out_time,
    //             'America/Argentina/Buenos_Aires'
    //         );

    //         // Validar que check_out sea posterior a check_in
    //         if ($checkOut->lte($checkIn)) {
    //             throw new \Exception('La fecha y hora de salida deben ser posteriores a la fecha y hora de entrada.');
    //         }

    //         // Verificar si existe una reserva confirmada que se solape con el rango de fechas
    //         $overlappingReservation = Reservation::where('room_id', $request->room_id)
    //             ->where('status', 'Confirmada')
    //             ->where(function ($query) use ($checkIn, $checkOut) {
    //                 $query->whereBetween('check_in', [$checkIn, $checkOut])
    //                     ->orWhereBetween('check_out', [$checkIn, $checkOut])
    //                     ->orWhere(function ($subQuery) use ($checkIn, $checkOut) {
    //                         $subQuery->where('check_in', '<=', $checkIn)
    //                             ->where('check_out', '>=', $checkOut);
    //                     });
    //             })
    //             ->first();

    //         // Si existe una reserva confirmada, actualizar su estado a "Ocupada"
    //         if ($overlappingReservation) {
    //             $overlappingReservation->update([
    //                 'status' => 'Ocupada',
    //             ]);
    //             Log::info("Reserva ID {$overlappingReservation->id} actualizada a 'Ocupada' para la habitación {$request->room_id}.");
    //         }

    //         // Crear la entrada
    //         $entry = Entry::create([
    //             'room_id' => $request->room_id,
    //             'room_type_id' => $request->room_type_id,
    //             'client_id' => $request->client_id,
    //             'entry_type' => $request->entry_type, // Ahora es el ID de la tarifa
    //             'quantity' => $request->quantity,
    //             'check_in' => $checkIn,
    //             'check_out' => $checkOut,
    //             'efectivo' => $request->efectivo,
    //             'mercadopago' => $request->mercadopago,
    //             'tarjeta' => $request->tarjeta,
    //             'transferencia' => $request->transferencia,
    //             'discount' => $request->discount,
    //             'total' => $request->total,
    //             'debt' => $request->debt,
    //             'pago' => $pagoStatus,
    //             'observations' => $request->observations,
    //             'status' => 'Active',
    //         ]);

    //         // Actualizar el estado de la habitación a "Ocupada"
    //         $room->update([
    //             'status' => 'Ocupada',
    //         ]);

    //         $companionIds = [];
    //         if ($request->filled('companions')) {
    //             $companions = json_decode($request->companions, true);
    //             foreach ($companions as $companionData) {
    //                 $companion = Companion::create([
    //                     'name' => $companionData['name'],
    //                     'lastname' => $companionData['lastname'],
    //                     'dni' => $companionData['dni'],
    //                     'phone' => $companionData['phone'],
    //                     'email' => $companionData['email'] ?? null,
    //                 ]);
    //                 $companionIds[] = $companion->id;
    //             }
    //         }

    //         if (!empty($companionIds)) {
    //             $entry->companions()->attach($companionIds);
    //         }

    //         // Registrar movimiento en caja y pago en la tabla pagos
    //         if ($totalPagado > 0) {
    //             // Registrar en movimiento_cajas
    //             MovimientoCaja::create([
    //                 'arqueo_id' => $currentArqueo->id,
    //                 'tipo' => 'Ingreso',
    //                 'clase' => 'Alquiler',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'descripcion' => "Ingreso por entrada de habitación {$entry->room->room_number}",
    //                 'usuario_id' => auth()->id(),
    //             ]);

    //             // Registrar en pagos
    //             $descripcionPago = "Pago de alquiler de habitación {$entry->room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
    //             Pago::create([
    //                 'fecha' => now(),
    //                 'descripcion' => $descripcionPago,
    //                 'clase' => 'Alquiler',
    //                 'room_id' => $entry->room_id,
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'usuario_id' => auth()->id(),
    //                 'arqueo_id' => $currentArqueo->id,
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('entradas.panel-control')
    //             ->with('success', 'Entrada registrada correctamente.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al crear la entrada: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Error al crear la entrada: ' . $e->getMessage())->withInput();
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'room_id' => 'required|exists:rooms,id',
    //             'room_type_id' => 'required|exists:room_types,id',
    //             'client_id' => 'required|exists:clients,id',
    //             'entry_type' => 'required|exists:room_type_tariffs,id',
    //             'check_in_date' => 'required|date',
    //             'check_in_time' => 'required',
    //             'check_out_date' => 'required|date',
    //             'check_out_time' => 'required',
    //             'quantity' => 'required|integer|min:1',
    //             'efectivo' => 'nullable|numeric|min:0',
    //             'mercadopago' => 'nullable|numeric|min:0',
    //             'tarjeta' => 'nullable|numeric|min:0',
    //             'transferencia' => 'nullable|numeric|min:0',
    //             'discount' => 'nullable|numeric|min:0',
    //             'total' => 'required|numeric|min:0',
    //             'debt' => 'nullable|numeric',
    //             'observations' => 'nullable|string',
    //             'companions' => 'nullable|json',
    //         ]);

    //         // Combinar fecha y hora para check_in y check_out
    //         $checkIn = Carbon::createFromFormat('Y-m-d H:i', $validated['check_in_date'] . ' ' . $validated['check_in_time']);
    //         $checkOut = Carbon::createFromFormat('Y-m-d H:i', $validated['check_out_date'] . ' ' . $validated['check_out_time']);

    //         // Crear la entrada
    //         $entry = new Entry();
    //         $entry->room_id = $validated['room_id'];
    //         $entry->room_type_id = $validated['room_type_id'];
    //         $entry->client_id = $validated['client_id'];
    //         $entry->room_type_tariff_id = $validated['entry_type'];
    //         $entry->check_in = $checkIn;
    //         $entry->check_out = $checkOut;
    //         $entry->quantity = $validated['quantity'];
    //         $entry->total = $validated['total'];
    //         $entry->debt = $validated['debt'];
    //         $entry->observations = $validated['observations'];
    //         $entry->save();

    //         // Guardar métodos de pago
    //         $entry->payment()->create([
    //             'efectivo' => $validated['efectivo'] ?? 0,
    //             'mercadopago' => $validated['mercadopago'] ?? 0,
    //             'tarjeta' => $validated['tarjeta'] ?? 0,
    //             'transferencia' => $validated['transferencia'] ?? 0,
    //             'discount' => $validated['discount'] ?? 0,
    //         ]);

    //         // Guardar acompañantes si existen
    //         if ($validated['companions']) {
    //             $companions = json_decode($validated['companions'], true);
    //             foreach ($companions as $companion) {
    //                 $entry->companions()->create($companion);
    //             }
    //         }

    //         return redirect()->route('entradas.panel-control')->with('success', 'Entrada registrada correctamente.');
    //     } catch (\Exception $e) {
    //         Log::error('Error al registrar entrada: ' . $e->getMessage());
    //         return redirect()->back()->withErrors(['error' => 'No se pudo registrar la entrada: ' . $e->getMessage()])->withInput();
    //     }
    // }


    public function store(Request $request)
    {
        // Registrar los datos recibidos para depuración
        Log::info('Datos recibidos en store: ' . json_encode($request->all()));

        // Validar los datos del formulario
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'room_type_id' => 'required|exists:room_types,id',
            'client_id' => 'required|exists:clients,id',
            'entry_type' => 'required|exists:room_type_tariffs,id', // Ahora es el ID de la tarifa
            'quantity' => 'required|integer|min:1',
            'check_in_date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_date' => 'required|date',
            'check_out_time' => 'required',
            'efectivo' => 'nullable|numeric|min:0',
            'mercadopago' => 'nullable|numeric|min:0',
            'tarjeta' => 'nullable|numeric|min:0',
            'transferencia' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'debt' => 'required|numeric',
            'observations' => 'nullable|string',
            'companions' => 'nullable|string',
        ]);

        $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
        $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

        DB::beginTransaction();
        try {
            // Verificar si hay un arqueo abierto
            $currentArqueo = Arqueo::where('status', 'Abierto')->first();
            if (!$currentArqueo) {
                throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una entrada.');
            }

            // Buscar la habitación
            $room = Room::findOrFail($request->room_id);

            // Verificar que la habitación esté disponible
            if ($room->status !== 'Disponible' && $room->status !== 'Reservada') {
                throw new \Exception('La habitación no está disponible para crear una entrada. Estado actual: ' . $room->status);
            }

            // Combinar fecha y hora para check_in y check_out
            $checkIn = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->check_in_date . ' ' . $request->check_in_time,
                'America/Argentina/Buenos_Aires'
            );

            $checkOut = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->check_out_date . ' ' . $request->check_out_time,
                'America/Argentina/Buenos_Aires'
            );

            // Validar que check_out sea posterior a check_in
            if ($checkOut->lte($checkIn)) {
                throw new \Exception('La fecha y hora de salida deben ser posteriores a la fecha y hora de entrada.');
            }

            // Verificar si existe una reserva confirmada que se solape con el rango de fechas
            $overlappingReservation = Reservation::where('room_id', $request->room_id)
                ->where('status', 'Confirmada')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out', [$checkIn, $checkOut])
                        ->orWhere(function ($subQuery) use ($checkIn, $checkOut) {
                            $subQuery->where('check_in', '<=', $checkIn)
                                ->where('check_out', '>=', $checkOut);
                        });
                })
                ->first();

            // Si existe una reserva confirmada, actualizar su estado a "Ocupada"
            if ($overlappingReservation) {
                $overlappingReservation->update([
                    'status' => 'Ocupada',
                ]);
                Log::info("Reserva ID {$overlappingReservation->id} actualizada a 'Ocupada' para la habitación {$request->room_id}.");
            }

            // Crear la entrada
            $entry = Entry::create([
                'room_id' => $request->room_id,
                'room_type_id' => $request->room_type_id,
                'client_id' => $request->client_id,
                'room_type_tariff_id' => $request->entry_type, // Ahora usamos el ID de la tarifa
                'quantity' => $request->quantity,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'efectivo' => $request->efectivo ?? 0,
                'mercadopago' => $request->mercadopago ?? 0,
                'tarjeta' => $request->tarjeta ?? 0,
                'transferencia' => $request->transferencia ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
                'debt' => $request->debt,
                'pago' => $pagoStatus,
                'observations' => $request->observations,
                'status' => 'Active',
            ]);

            // Actualizar el estado de la habitación a "Ocupada"
            $room->update([
                'status' => 'Ocupada',
            ]);

            $companionIds = [];
            if ($request->filled('companions')) {
                $companions = json_decode($request->companions, true);
                foreach ($companions as $companionData) {
                    $companion = Companion::create([
                        'name' => $companionData['name'],
                        'lastname' => $companionData['lastname'],
                        'dni' => $companionData['dni'],
                        'phone' => $companionData['phone'],
                        'email' => $companionData['email'] ?? null,
                    ]);
                    $companionIds[] = $companion->id;
                }
            }

            if (!empty($companionIds)) {
                $entry->companions()->attach($companionIds);
            }

            // Registrar movimiento en caja y pago en la tabla pagos
            if ($totalPagado > 0) {
                // Registrar en movimiento_cajas
                MovimientoCaja::create([
                    'arqueo_id' => $currentArqueo->id,
                    'tipo' => 'Ingreso',
                    'clase' => 'Alquiler',
                    'monto' => $totalPagado,
                    'efectivo' => $request->efectivo ?? 0,
                    'mercadopago' => $request->mercadopago ?? 0,
                    'tarjeta' => $request->tarjeta ?? 0,
                    'transferencia' => $request->transferencia ?? 0,
                    'descripcion' => "Ingreso por entrada de habitación {$entry->room->room_number}",
                    'usuario_id' => auth()->id(),
                ]);

                // Registrar en pagos
                $descripcionPago = "Pago de alquiler de habitación {$entry->room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
                Pago::create([
                    'fecha' => now(),
                    'descripcion' => $descripcionPago,
                    'clase' => 'Alquiler',
                    'room_id' => $entry->room_id,
                    'monto' => $totalPagado,
                    'efectivo' => $request->efectivo ?? 0,
                    'mercadopago' => $request->mercadopago ?? 0,
                    'tarjeta' => $request->tarjeta ?? 0,
                    'transferencia' => $request->transferencia ?? 0,
                    'usuario_id' => auth()->id(),
                    'arqueo_id' => $currentArqueo->id,
                ]);
            }

            DB::commit();
            return redirect()->route('entradas.panel-control')
                ->with('success', 'Entrada registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la entrada: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la entrada: ' . $e->getMessage())->withInput();
        }
    }




    // public function panelControl()
    // {
    //     // Configurar idioma y zona horaria
    //     Carbon::setLocale('es');
    //     date_default_timezone_set('America/Argentina/Buenos_Aires');

    //     // Verificar el estado de la caja
    //     $currentUser = auth()->user();
    //     $openCashRegister = DB::table('arqueos')->whereNull('fecha_cierre')->first();

    //     if (!$openCashRegister) {
    //         session()->flash('showCajaCerradaAlert', true);
    //     } elseif ($openCashRegister->usuario_id != $currentUser->id) {
    //         session()->flash('showCajaOtroUsuarioAlert', true);
    //     }

    //     // Obtener el tiempo actual
    //     $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
    //     Log::info('Current DateTime at start: ' . $currentDateTime->toDateTimeString());

    //     // Cargar niveles (pisos)
    //     $levels = Level::all();

    //     // Cargar las relaciones necesarias, incluyendo el cliente y las renovaciones para las entradas
    //     $rooms = Room::with(['reservations' => function ($query) {
    //         $query->where('status', 'Confirmada')
    //             ->orderBy('check_in', 'asc');
    //     }, 'entries' => function ($query) {
    //         $query->with(['client', 'renovations' => function ($query) {
    //             $query->where('status', 'Active')
    //                 ->orderBy('check_out', 'desc');
    //         }]);
    //     }, 'cleanings' => function ($query) {
    //         $query->whereIn('status', ['Active', 'Completed'])->orderBy('end_time', 'desc');
    //     }])->get();

    //     $rooms = $rooms->map(function ($room) use ($currentDateTime) {
    //         // Inicializar propiedades
    //         $room->is_reserved_today = false;
    //         $room->current_reservation = null;
    //         $room->is_occupied_by_entry = false;
    //         $room->current_entry = null;
    //         $room->is_being_cleaned = false;
    //         $room->current_cleaning = null;
    //         $room->time_remaining = null;
    //         $room->display_check_out = null;

    //         // Usar el estado de la base de datos como fuente de verdad
    //         $room->status = $room->status ?? 'Disponible';
    //         Log::info('Room ' . $room->room_number . ' - Initial Status from DB: ' . $room->status);

    //         // Evaluar reservas
    //         foreach ($room->reservations as $reservation) {
    //             $checkInDate = Carbon::parse($reservation->check_in)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
    //             $checkInTime = $reservation->check_in_time ?? '00:00:00';
    //             $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $checkInDate . ' ' . $checkInTime, 'America/Argentina/Buenos_Aires');

    //             $checkOutDate = Carbon::parse($reservation->check_out)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
    //             $checkOutTime = $reservation->check_out_time ?? '00:00:00';
    //             $checkOut = Carbon::createFromFormat('Y-m-d H:i:s', $checkOutDate . ' ' . $checkOutTime, 'America/Argentina/Buenos_Aires');

    //             $checkOutWithTolerance = $checkOut->copy()->addHour();

    //             $reservation->check_in = $checkIn;
    //             $reservation->check_out = $checkOut;

    //             Log::info('Room ' . $room->room_number . ' - Evaluating Reservation: Check-in: ' . $checkIn->toDateTimeString() . ', Check-out: ' . $checkOut->toDateTimeString() . ', Check-out with tolerance: ' . $checkOutWithTolerance->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

    //             if ($currentDateTime->greaterThanOrEqualTo($checkIn) && $currentDateTime->lessThanOrEqualTo($checkOutWithTolerance)) {
    //                 $room->is_reserved_today = true;
    //                 $room->current_reservation = $reservation;
    //                 $room->display_check_out = $checkOut;
    //                 Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display due to active reservation.');
    //                 break;
    //             } elseif ($checkIn->isFuture()) {
    //                 $hoursUntilCheckIn = $currentDateTime->diffInHours($checkIn, false);
    //                 $minutesUntilCheckIn = $currentDateTime->diffInMinutes($checkIn, false);
    //                 $exactHoursUntilCheckIn = $minutesUntilCheckIn / 60;
    //                 Log::info('Room ' . $room->room_number . ' - Hours until check-in: ' . $hoursUntilCheckIn . ', Minutes until check-in: ' . $minutesUntilCheckIn . ', Exact hours: ' . $exactHoursUntilCheckIn);
    //                 if ($exactHoursUntilCheckIn < 4) {
    //                     $room->is_reserved_today = true;
    //                     $room->current_reservation = $reservation;
    //                     $room->display_check_out = $checkOut;
    //                     Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display because check-in is within 4 hours.');
    //                 }
    //                 break;
    //             }
    //         }

    //         // Verificar entradas activas
    //         $activeEntry = $room->entries()
    //             ->where('status', 'Active')
    //             ->where('check_in', '<=', $currentDateTime)
    //             ->where('check_out', '>=', $currentDateTime)
    //             ->first();

    //         if ($activeEntry) {
    //             $room->is_occupied_by_entry = true;
    //             $room->current_entry = $activeEntry;
    //             Log::info('Room ' . $room->room_number . ' - Marked as Ocupada for display due to active entry. Entry ID: ' . $activeEntry->id);
    //             Log::info('Room ' . $room->room_number . ' - Entry Check-in: ' . $activeEntry->check_in . ', Check-out: ' . $activeEntry->check_out);

    //             $checkIn = Carbon::parse($activeEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
    //             $latestCheckOut = $activeEntry->check_out;
    //             $totalQuantity = $activeEntry->quantity;

    //             if ($activeEntry->renovations->isNotEmpty()) {
    //                 $latestRenovation = $activeEntry->renovations->sortByDesc('check_out')->first();
    //                 $latestCheckOut = $latestRenovation->check_out;
    //                 $totalRenovationQuantity = $activeEntry->renovations->sum('quantity');
    //                 $totalQuantity += $totalRenovationQuantity;
    //                 Log::info('Room ' . $room->room_number . ' - Latest check_out from renovations: ' . $latestCheckOut);
    //                 Log::info('Room ' . $room->room_number . ' - Total quantity (entry + renovations): ' . $totalQuantity);
    //             }

    //             $checkOut = Carbon::parse($latestCheckOut)->setTimezone('America/Argentina/Buenos_Aires');
    //             $room->display_check_out = $checkOut;

    //             Log::info('Room ' . $room->room_number . ' - Check-in parsed: ' . $checkIn->toDateTimeString() . ', Check-out parsed: ' . $checkOut->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

    //             $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
    //             Log::info('Room ' . $room->room_number . ' - Diff in seconds: ' . $diffInSeconds);

    //             if ($activeEntry->entry_type === '4_hours') {
    //                 $maxSecondsFor4Hours = 4 * 60 * 60 * $totalQuantity;
    //                 if ($diffInSeconds > 0) {
    //                     $diffInSeconds = min($diffInSeconds, $maxSecondsFor4Hours);
    //                 }
    //                 $days = 0;
    //                 $hours = floor(abs($diffInSeconds) / 3600);
    //                 $remainingSeconds = abs($diffInSeconds) % 3600;
    //                 $minutes = floor($remainingSeconds / 60);
    //                 $seconds = $remainingSeconds % 60;
    //             } else {
    //                 $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
    //                 $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
    //                 $hours = floor($remainingSeconds / (60 * 60));
    //                 $remainingSeconds %= (60 * 60);
    //                 $minutes = floor($remainingSeconds / 60);
    //                 $seconds = $remainingSeconds % 60;
    //             }

    //             $room->time_remaining = [
    //                 'days' => $days,
    //                 'hours' => $hours,
    //                 'minutes' => $minutes,
    //                 'seconds' => $seconds,
    //                 'check_out_timestamp' => $checkOut->timestamp,
    //             ];

    //             Log::info('Room ' . $room->room_number . ' - Time remaining: ' . json_encode($room->time_remaining));
    //             Log::info('Room ' . $room->room_number . ' - check_out: ' . $checkOut->toDateTimeString() . ', timestamp: ' . $checkOut->timestamp);
    //         } else {
    //             Log::info('Room ' . $room->room_number . ' - No active entry found.');
    //             $lastEntry = $room->entries()->orderBy('check_out', 'desc')->first();

    //             if ($lastEntry) {
    //                 Log::info('Room ' . $room->room_number . ' - Last entry found. Entry ID: ' . $lastEntry->id . ', Status: ' . $lastEntry->status);
    //                 if ($room->status === 'Ocupada') {
    //                     $room->current_entry = $lastEntry;
    //                     Log::info('Room ' . $room->room_number . ' - Using last entry for client info: ' . $lastEntry->id);
    //                     if ($lastEntry->client) {
    //                         Log::info('Room ' . $room->room_number . ' - Client found: ' . $lastEntry->client->name . ' ' . $lastEntry->client->lastname);
    //                     } else {
    //                         Log::info('Room ' . $room->room_number . ' - Client not found for entry ID: ' . $lastEntry->id);
    //                     }

    //                     $checkIn = Carbon::parse($lastEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
    //                     $checkOut = Carbon::parse($lastEntry->check_out)->setTimezone('America/Argentina/Buenos_Aires');
    //                     $room->display_check_out = $checkOut;

    //                     $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
    //                     Log::info('Room ' . $room->room_number . ' - Diff in seconds (last entry): ' . $diffInSeconds);

    //                     if ($lastEntry->entry_type === '4_hours') {
    //                         $maxSecondsFor4Hours = 4 * 60 * 60 * $lastEntry->quantity;
    //                         if ($diffInSeconds > 0) {
    //                             $diffInSeconds = min($diffInSeconds, $maxSecondsFor4Hours);
    //                         }
    //                         $days = 0;
    //                         $hours = floor(abs($diffInSeconds) / 3600);
    //                         $remainingSeconds = abs($diffInSeconds) % 3600;
    //                         $minutes = floor($remainingSeconds / 60);
    //                         $seconds = $remainingSeconds % 60;
    //                     } else {
    //                         $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
    //                         $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
    //                         $hours = floor($remainingSeconds / (60 * 60));
    //                         $remainingSeconds %= (60 * 60);
    //                         $minutes = floor($remainingSeconds / 60);
    //                         $seconds = $remainingSeconds % 60;
    //                     }

    //                     $room->time_remaining = [
    //                         'days' => $days,
    //                         'hours' => $hours,
    //                         'minutes' => $minutes,
    //                         'seconds' => $seconds,
    //                         'check_out_timestamp' => $checkOut->timestamp,
    //                     ];

    //                     Log::info('Room ' . $room->room_number . ' - Time remaining (last entry): ' . json_encode($room->time_remaining));
    //                 }
    //             } else {
    //                 Log::info('Room ' . $room->room_number . ' - No entries found.');
    //             }
    //         }

    //         // Verificar limpiezas activas
    //         if ($room->status !== 'Ocupada') {
    //             $activeCleaning = $room->cleanings()
    //                 ->where('status', 'Active')
    //                 ->where('start_time', '<=', $currentDateTime)
    //                 ->where('end_time', '>=', $currentDateTime)
    //                 ->first();

    //             if (!$activeCleaning) {
    //                 $activeCleaning = $room->cleanings()
    //                     ->where('status', 'Active')
    //                     ->orderBy('end_time', 'desc')
    //                     ->first();
    //             }

    //             if ($activeCleaning) {
    //                 $room->current_cleaning = $activeCleaning;
    //                 $startTime = Carbon::parse($activeCleaning->start_time)->setTimezone('America/Argentina/Buenos_Aires');
    //                 $endTime = Carbon::parse($activeCleaning->end_time)->setTimezone('America/Argentina/Buenos_Aires');
    //                 $room->display_check_out = $endTime;

    //                 if ($endTime->lessThan($currentDateTime)) {
    //                     $room->is_being_cleaned = false;
    //                     $room->time_remaining = [
    //                         'days' => 0,
    //                         'hours' => 0,
    //                         'minutes' => 0,
    //                         'seconds' => 0,
    //                         'check_out_timestamp' => $endTime->timestamp,
    //                     ];
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning expired. Setting time_remaining to zero: ' . json_encode($room->time_remaining));
    //                 } else {
    //                     $room->is_being_cleaned = true;
    //                     Log::info('Room ' . $room->room_number . ' - Marked as ' . $room->status . ' for display due to active cleaning.');

    //                     $diffInSeconds = $currentDateTime->diffInSeconds($endTime, false);
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning Diff in seconds: ' . $diffInSeconds);

    //                     $maxSeconds = $activeCleaning->cleaning_type === 'deep' ? 60 * 60 : 30 * 60;
    //                     if ($diffInSeconds > 0) {
    //                         $diffInSeconds = min($diffInSeconds, $maxSeconds);
    //                     }

    //                     $days = 0;
    //                     $hours = floor(abs($diffInSeconds) / 3600);
    //                     $remainingSeconds = abs($diffInSeconds) % 3600;
    //                     $minutes = floor($remainingSeconds / 60);
    //                     $seconds = $remainingSeconds % 60;

    //                     $room->time_remaining = [
    //                         'days' => $days,
    //                         'hours' => $hours,
    //                         'minutes' => $minutes,
    //                         'seconds' => $seconds,
    //                         'check_out_timestamp' => $endTime->timestamp,
    //                     ];

    //                     Log::info('Room ' . $room->room_number . ' - Cleaning end_time: ' . $endTime->toDateTimeString() . ', timestamp: ' . $endTime->timestamp);
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning time remaining: ' . json_encode($room->time_remaining));
    //                 }
    //             }
    //         }

    //         // No realizar ajustes automáticos al estado; respetar el estado de la base de datos
    //         Log::info('Room ' . $room->room_number . ' - Final Status: ' . $room->status);
    //         return $room;
    //     });

    //     return view('entradas.panel-control', compact('levels', 'rooms'));
    // }

    // public function panelControl()
    // {
    //     // Configurar idioma y zona horaria
    //     Carbon::setLocale('es');
    //     date_default_timezone_set('America/Argentina/Buenos_Aires');

    //     // Verificar el estado de la caja
    //     $currentUser = auth()->user();
    //     $openCashRegister = DB::table('arqueos')->whereNull('fecha_cierre')->first();

    //     if (!$openCashRegister) {
    //         session()->flash('showCajaCerradaAlert', true);
    //     } elseif ($openCashRegister->usuario_id != $currentUser->id) {
    //         session()->flash('showCajaOtroUsuarioAlert', true);
    //     }

    //     // Obtener el tiempo actual
    //     $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
    //     Log::info('Current DateTime at start: ' . $currentDateTime->toDateTimeString());

    //     // Cargar niveles (pisos)
    //     $levels = Level::all();

    //     // Cargar las relaciones necesarias, incluyendo el cliente, la tarifa y las renovaciones para las entradas
    //     $rooms = Room::with(['reservations' => function ($query) {
    //         $query->where('status', 'Confirmada')
    //             ->orderBy('check_in', 'asc');
    //     }, 'entries' => function ($query) {
    //         $query->with(['client', 'tariff', 'renovations' => function ($query) {
    //             $query->where('status', 'Active')
    //                 ->orderBy('check_out', 'desc');
    //         }]);
    //     }, 'cleanings' => function ($query) {
    //         $query->whereIn('status', ['Active', 'Completed'])->orderBy('end_time', 'desc');
    //     }])->get();

    //     $rooms = $rooms->map(function ($room) use ($currentDateTime) {
    //         // Inicializar propiedades
    //         $room->is_reserved_today = false;
    //         $room->current_reservation = null;
    //         $room->is_occupied_by_entry = false;
    //         $room->current_entry = null;
    //         $room->is_being_cleaned = false;
    //         $room->current_cleaning = null;
    //         $room->time_remaining = null;
    //         $room->display_check_out = null;

    //         // Usar el estado de la base de datos como fuente de verdad
    //         $room->status = $room->status ?? 'Disponible';
    //         Log::info('Room ' . $room->room_number . ' - Initial Status from DB: ' . $room->status);

    //         // Evaluar reservas
    //         foreach ($room->reservations as $reservation) {
    //             $checkInDate = Carbon::parse($reservation->check_in)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
    //             $checkInTime = $reservation->check_in_time ?? '00:00:00';
    //             $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $checkInDate . ' ' . $checkInTime, 'America/Argentina/Buenos_Aires');

    //             $checkOutDate = Carbon::parse($reservation->check_out)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
    //             $checkOutTime = $reservation->check_out_time ?? '00:00:00';
    //             $checkOut = Carbon::createFromFormat('Y-m-d H:i:s', $checkOutDate . ' ' . $checkOutTime, 'America/Argentina/Buenos_Aires');

    //             $checkOutWithTolerance = $checkOut->copy()->addHour();

    //             $reservation->check_in = $checkIn;
    //             $reservation->check_out = $checkOut;

    //             Log::info('Room ' . $room->room_number . ' - Evaluating Reservation: Check-in: ' . $checkIn->toDateTimeString() . ', Check-out: ' . $checkOut->toDateTimeString() . ', Check-out with tolerance: ' . $checkOutWithTolerance->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

    //             if ($currentDateTime->greaterThanOrEqualTo($checkIn) && $currentDateTime->lessThanOrEqualTo($checkOutWithTolerance)) {
    //                 $room->is_reserved_today = true;
    //                 $room->current_reservation = $reservation;
    //                 $room->display_check_out = $checkOut;
    //                 Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display due to active reservation.');
    //                 break;
    //             } elseif ($checkIn->isFuture()) {
    //                 $hoursUntilCheckIn = $currentDateTime->diffInHours($checkIn, false);
    //                 $minutesUntilCheckIn = $currentDateTime->diffInMinutes($checkIn, false);
    //                 $exactHoursUntilCheckIn = $minutesUntilCheckIn / 60;
    //                 Log::info('Room ' . $room->room_number . ' - Hours until check-in: ' . $hoursUntilCheckIn . ', Minutes until check-in: ' . $minutesUntilCheckIn . ', Exact hours: ' . $exactHoursUntilCheckIn);
    //                 if ($exactHoursUntilCheckIn < 4) {
    //                     $room->is_reserved_today = true;
    //                     $room->current_reservation = $reservation;
    //                     $room->display_check_out = $checkOut;
    //                     Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display because check-in is within 4 hours.');
    //                 }
    //                 break;
    //             }
    //         }

    //         // Verificar entradas activas
    //         $activeEntry = $room->entries()
    //             ->where('status', 'Active')
    //             ->where('check_in', '<=', $currentDateTime)
    //             ->where('check_out', '>=', $currentDateTime)
    //             ->first();

    //         if ($activeEntry) {
    //             $room->is_occupied_by_entry = true;
    //             $room->current_entry = $activeEntry;
    //             Log::info('Room ' . $room->room_number . ' - Marked as Ocupada for display due to active entry. Entry ID: ' . $activeEntry->id);
    //             Log::info('Room ' . $room->room_number . ' - Entry Check-in: ' . $activeEntry->check_in . ', Check-out: ' . $activeEntry->check_out);

    //             $checkIn = Carbon::parse($activeEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
    //             $latestCheckOut = $activeEntry->check_out;
    //             $totalQuantity = $activeEntry->quantity;

    //             if ($activeEntry->renovations->isNotEmpty()) {
    //                 $latestRenovation = $activeEntry->renovations->sortByDesc('check_out')->first();
    //                 $latestCheckOut = $latestRenovation->check_out;
    //                 $totalRenovationQuantity = $activeEntry->renovations->sum('quantity');
    //                 $totalQuantity += $totalRenovationQuantity;
    //                 Log::info('Room ' . $room->room_number . ' - Latest check_out from renovations: ' . $latestCheckOut);
    //                 Log::info('Room ' . $room->room_number . ' - Total quantity (entry + renovations): ' . $totalQuantity);
    //             }

    //             $checkOut = Carbon::parse($latestCheckOut)->setTimezone('America/Argentina/Buenos_Aires');
    //             $room->display_check_out = $checkOut;

    //             Log::info('Room ' . $room->room_number . ' - Check-in parsed: ' . $checkIn->toDateTimeString() . ', Check-out parsed: ' . $checkOut->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

    //             $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
    //             Log::info('Room ' . $room->room_number . ' - Diff in seconds: ' . $diffInSeconds);

    //             if ($activeEntry->entry_type === '4_hours') {
    //                 $maxSecondsFor4Hours = 4 * 60 * 60 * $totalQuantity;
    //                 if ($diffInSeconds > 0) {
    //                     $diffInSeconds = min($diffInSeconds, $maxSecondsFor4Hours);
    //                 }
    //                 $days = 0;
    //                 $hours = floor(abs($diffInSeconds) / 3600);
    //                 $remainingSeconds = abs($diffInSeconds) % 3600;
    //                 $minutes = floor($remainingSeconds / 60);
    //                 $seconds = $remainingSeconds % 60;
    //             } else {
    //                 $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
    //                 $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
    //                 $hours = floor($remainingSeconds / (60 * 60));
    //                 $remainingSeconds %= (60 * 60);
    //                 $minutes = floor($remainingSeconds / 60);
    //                 $seconds = $remainingSeconds % 60;
    //             }

    //             $room->time_remaining = [
    //                 'days' => $days,
    //                 'hours' => $hours,
    //                 'minutes' => $minutes,
    //                 'seconds' => $seconds,
    //                 'check_out_timestamp' => $checkOut->timestamp,
    //             ];

    //             Log::info('Room ' . $room->room_number . ' - Time remaining: ' . json_encode($room->time_remaining));
    //             Log::info('Room ' . $room->room_number . ' - check_out: ' . $checkOut->toDateTimeString() . ', timestamp: ' . $checkOut->timestamp);
    //         } else {
    //             Log::info('Room ' . $room->room_number . ' - No active entry found.');
    //             $lastEntry = $room->entries()->orderBy('check_out', 'desc')->first();

    //             if ($lastEntry) {
    //                 Log::info('Room ' . $room->room_number . ' - Last entry found. Entry ID: ' . $lastEntry->id . ', Status: ' . $lastEntry->status);
    //                 if ($room->status === 'Ocupada') {
    //                     $room->current_entry = $lastEntry;
    //                     Log::info('Room ' . $room->room_number . ' - Using last entry for client info: ' . $lastEntry->id);
    //                     if ($lastEntry->client) {
    //                         Log::info('Room ' . $room->room_number . ' - Client found: ' . $lastEntry->client->name . ' ' . $lastEntry->client->lastname);
    //                     } else {
    //                         Log::info('Room ' . $room->room_number . ' - Client not found for entry ID: ' . $lastEntry->id);
    //                     }

    //                     $checkIn = Carbon::parse($lastEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
    //                     $checkOut = Carbon::parse($lastEntry->check_out)->setTimezone('America/Argentina/Buenos_Aires');
    //                     $room->display_check_out = $checkOut;

    //                     $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
    //                     Log::info('Room ' . $room->room_number . ' - Diff in seconds (last entry): ' . $diffInSeconds);

    //                     if ($lastEntry->entry_type === '4_hours') {
    //                         $maxSecondsFor4Hours = 4 * 60 * 60 * $lastEntry->quantity;
    //                         if ($diffInSeconds > 0) {
    //                             $diffInSeconds = min($diffInSeconds, $maxSecondsFor4Hours);
    //                         }
    //                         $days = 0;
    //                         $hours = floor(abs($diffInSeconds) / 3600);
    //                         $remainingSeconds = abs($diffInSeconds) % 3600;
    //                         $minutes = floor($remainingSeconds / 60);
    //                         $seconds = $remainingSeconds % 60;
    //                     } else {
    //                         $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
    //                         $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
    //                         $hours = floor($remainingSeconds / (60 * 60));
    //                         $remainingSeconds %= (60 * 60);
    //                         $minutes = floor($remainingSeconds / 60);
    //                         $seconds = $remainingSeconds % 60;
    //                     }

    //                     $room->time_remaining = [
    //                         'days' => $days,
    //                         'hours' => $hours,
    //                         'minutes' => $minutes,
    //                         'seconds' => $seconds,
    //                         'check_out_timestamp' => $checkOut->timestamp,
    //                     ];

    //                     Log::info('Room ' . $room->room_number . ' - Time remaining (last entry): ' . json_encode($room->time_remaining));
    //                 }
    //             } else {
    //                 Log::info('Room ' . $room->room_number . ' - No entries found.');
    //             }
    //         }

    //         // Verificar limpiezas activas
    //         if ($room->status !== 'Ocupada') {
    //             $activeCleaning = $room->cleanings()
    //                 ->where('status', 'Active')
    //                 ->where('start_time', '<=', $currentDateTime)
    //                 ->where('end_time', '>=', $currentDateTime)
    //                 ->first();

    //             if (!$activeCleaning) {
    //                 $activeCleaning = $room->cleanings()
    //                     ->where('status', 'Active')
    //                     ->orderBy('end_time', 'desc')
    //                     ->first();
    //             }

    //             if ($activeCleaning) {
    //                 $room->current_cleaning = $activeCleaning;
    //                 $startTime = Carbon::parse($activeCleaning->start_time)->setTimezone('America/Argentina/Buenos_Aires');
    //                 $endTime = Carbon::parse($activeCleaning->end_time)->setTimezone('America/Argentina/Buenos_Aires');
    //                 $room->display_check_out = $endTime;

    //                 if ($endTime->lessThan($currentDateTime)) {
    //                     $room->is_being_cleaned = false;
    //                     $room->time_remaining = [
    //                         'days' => 0,
    //                         'hours' => 0,
    //                         'minutes' => 0,
    //                         'seconds' => 0,
    //                         'check_out_timestamp' => $endTime->timestamp,
    //                     ];
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning expired. Setting time_remaining to zero: ' . json_encode($room->time_remaining));
    //                 } else {
    //                     $room->is_being_cleaned = true;
    //                     Log::info('Room ' . $room->room_number . ' - Marked as ' . $room->status . ' for display due to active cleaning.');

    //                     $diffInSeconds = $currentDateTime->diffInSeconds($endTime, false);
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning Diff in seconds: ' . $diffInSeconds);

    //                     $maxSeconds = $activeCleaning->cleaning_type === 'deep' ? 60 * 60 : 30 * 60;
    //                     if ($diffInSeconds > 0) {
    //                         $diffInSeconds = min($diffInSeconds, $maxSeconds);
    //                     }

    //                     $days = 0;
    //                     $hours = floor(abs($diffInSeconds) / 3600);
    //                     $remainingSeconds = abs($diffInSeconds) % 3600;
    //                     $minutes = floor($remainingSeconds / 60);
    //                     $seconds = $remainingSeconds % 60;

    //                     $room->time_remaining = [
    //                         'days' => $days,
    //                         'hours' => $hours,
    //                         'minutes' => $minutes,
    //                         'seconds' => $seconds,
    //                         'check_out_timestamp' => $endTime->timestamp,
    //                     ];

    //                     Log::info('Room ' . $room->room_number . ' - Cleaning end_time: ' . $endTime->toDateTimeString() . ', timestamp: ' . $endTime->timestamp);
    //                     Log::info('Room ' . $room->room_number . ' - Cleaning time remaining: ' . json_encode($room->time_remaining));
    //                 }
    //             }
    //         }

    //         // No realizar ajustes automáticos al estado; respetar el estado de la base de datos
    //         Log::info('Room ' . $room->room_number . ' - Final Status: ' . $room->status);
    //         return $room;
    //     });

    //     return view('entradas.panel-control', compact('levels', 'rooms'));
    // }

    public function panelControl()
    {
        // Configurar idioma y zona horaria
        Carbon::setLocale('es');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        // Verificar el estado de la caja
        $currentUser = auth()->user();
        $openCashRegister = DB::table('arqueos')->whereNull('fecha_cierre')->first();

        if (!$openCashRegister) {
            session()->flash('showCajaCerradaAlert', true);
        } elseif ($openCashRegister->usuario_id != $currentUser->id) {
            session()->flash('showCajaOtroUsuarioAlert', true);
        }

        // Obtener el tiempo actual
        $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');
        Log::info('Current DateTime at start: ' . $currentDateTime->toDateTimeString());

        // Cargar niveles (pisos)
        $levels = Level::all();

        // Cargar las relaciones necesarias, incluyendo el cliente, la tarifa y las renovaciones para las entradas
        $rooms = Room::with(['reservations' => function ($query) {
            $query->where('status', 'Confirmada')
                ->orderBy('check_in', 'asc');
        }, 'entries' => function ($query) {
            $query->with(['client', 'roomTypeTariff', 'renovations' => function ($query) {
                $query->where('status', 'Active')
                    ->orderBy('check_out', 'desc');
            }]);
        }, 'cleanings' => function ($query) {
            $query->whereIn('status', ['Active', 'Completed'])->orderBy('end_time', 'desc');
        }])->get();

        $rooms = $rooms->map(function ($room) use ($currentDateTime) {
            // Inicializar propiedades
            $room->is_reserved_today = false;
            $room->current_reservation = null;
            $room->is_occupied_by_entry = false;
            $room->current_entry = null;
            $room->is_being_cleaned = false;
            $room->current_cleaning = null;
            $room->time_remaining = null;
            $room->display_check_out = null;

            // Usar el estado de la base de datos como fuente de verdad
            $room->status = $room->status ?? 'Disponible';
            Log::info('Room ' . $room->room_number . ' - Initial Status from DB: ' . $room->status);

            // Evaluar reservas
            foreach ($room->reservations as $reservation) {
                $checkInDate = Carbon::parse($reservation->check_in)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
                $checkInTime = $reservation->check_in_time ?? '00:00:00';
                $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $checkInDate . ' ' . $checkInTime, 'America/Argentina/Buenos_Aires');

                $checkOutDate = Carbon::parse($reservation->check_out)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d');
                $checkOutTime = $reservation->check_out_time ?? '00:00:00';
                $checkOut = Carbon::createFromFormat('Y-m-d H:i:s', $checkOutDate . ' ' . $checkOutTime, 'America/Argentina/Buenos_Aires');

                $checkOutWithTolerance = $checkOut->copy()->addHour();

                $reservation->check_in = $checkIn;
                $reservation->check_out = $checkOut;

                Log::info('Room ' . $room->room_number . ' - Evaluating Reservation: Check-in: ' . $checkIn->toDateTimeString() . ', Check-out: ' . $checkOut->toDateTimeString() . ', Check-out with tolerance: ' . $checkOutWithTolerance->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

                if ($currentDateTime->greaterThanOrEqualTo($checkIn) && $currentDateTime->lessThanOrEqualTo($checkOutWithTolerance)) {
                    $room->is_reserved_today = true;
                    $room->current_reservation = $reservation;
                    $room->display_check_out = $checkOut;
                    Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display due to active reservation.');
                    break;
                } elseif ($checkIn->isFuture()) {
                    $hoursUntilCheckIn = $currentDateTime->diffInHours($checkIn, false);
                    $minutesUntilCheckIn = $currentDateTime->diffInMinutes($checkIn, false);
                    $exactHoursUntilCheckIn = $minutesUntilCheckIn / 60;
                    Log::info('Room ' . $room->room_number . ' - Hours until check-in: ' . $hoursUntilCheckIn . ', Minutes until check-in: ' . $minutesUntilCheckIn . ', Exact hours: ' . $exactHoursUntilCheckIn);
                    if ($exactHoursUntilCheckIn < 4) {
                        $room->is_reserved_today = true;
                        $room->current_reservation = $reservation;
                        $room->display_check_out = $checkOut;
                        Log::info('Room ' . $room->room_number . ' - Marked as Reservada for display because check-in is within 4 hours.');
                    }
                    break;
                }
            }

            // Verificar entradas activas
            $activeEntry = $room->entries()
                ->where('status', 'Active')
                ->where('check_in', '<=', $currentDateTime)
                ->where('check_out', '>=', $currentDateTime)
                ->first();

            if ($activeEntry) {
                $room->is_occupied_by_entry = true;
                $room->current_entry = $activeEntry;
                Log::info('Room ' . $room->room_number . ' - Marked as Ocupada for display due to active entry. Entry ID: ' . $activeEntry->id);
                Log::info('Room ' . $room->room_number . ' - Entry Check-in: ' . $activeEntry->check_in . ', Check-out: ' . $activeEntry->check_out);

                $checkIn = Carbon::parse($activeEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
                $latestCheckOut = $activeEntry->check_out;
                $totalQuantity = $activeEntry->quantity;

                if ($activeEntry->renovations->isNotEmpty()) {
                    $latestRenovation = $activeEntry->renovations->sortByDesc('check_out')->first();
                    $latestCheckOut = $latestRenovation->check_out;
                    $totalRenovationQuantity = $activeEntry->renovations->sum('quantity');
                    $totalQuantity += $totalRenovationQuantity;
                    Log::info('Room ' . $room->room_number . ' - Latest check_out from renovations: ' . $latestCheckOut);
                    Log::info('Room ' . $room->room_number . ' - Total quantity (entry + renovations): ' . $totalQuantity);
                }

                $checkOut = Carbon::parse($latestCheckOut)->setTimezone('America/Argentina/Buenos_Aires');
                $room->display_check_out = $checkOut;

                Log::info('Room ' . $room->room_number . ' - Check-in parsed: ' . $checkIn->toDateTimeString() . ', Check-out parsed: ' . $checkOut->toDateTimeString() . ', Current Time: ' . $currentDateTime->toDateTimeString());

                $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
                Log::info('Room ' . $room->room_number . ' - Diff in seconds: ' . $diffInSeconds);

                // Calcular el tiempo restante
                $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
                $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
                $hours = floor($remainingSeconds / (60 * 60));
                $remainingSeconds %= (60 * 60);
                $minutes = floor($remainingSeconds / 60);
                $seconds = $remainingSeconds % 60;

                $room->time_remaining = [
                    'days' => $days,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'seconds' => $seconds,
                    'check_out_timestamp' => $checkOut->timestamp,
                ];

                Log::info('Room ' . $room->room_number . ' - Time remaining: ' . json_encode($room->time_remaining));
                Log::info('Room ' . $room->room_number . ' - check_out: ' . $checkOut->toDateTimeString() . ', timestamp: ' . $checkOut->timestamp);
            } else {
                Log::info('Room ' . $room->room_number . ' - No active entry found.');
                $lastEntry = $room->entries()->orderBy('check_out', 'desc')->first();

                if ($lastEntry) {
                    Log::info('Room ' . $room->room_number . ' - Last entry found. Entry ID: ' . $lastEntry->id . ', Status: ' . $lastEntry->status);
                    if ($room->status === 'Ocupada') {
                        $room->current_entry = $lastEntry;
                        Log::info('Room ' . $room->room_number . ' - Using last entry for client info: ' . $lastEntry->id);
                        if ($lastEntry->client) {
                            Log::info('Room ' . $room->room_number . ' - Client found: ' . $lastEntry->client->name . ' ' . $lastEntry->client->lastname);
                        } else {
                            Log::info('Room ' . $room->room_number . ' - Client not found for entry ID: ' . $lastEntry->id);
                        }

                        $checkIn = Carbon::parse($lastEntry->check_in)->setTimezone('America/Argentina/Buenos_Aires');
                        $checkOut = Carbon::parse($lastEntry->check_out)->setTimezone('America/Argentina/Buenos_Aires');
                        $room->display_check_out = $checkOut;

                        $diffInSeconds = $currentDateTime->diffInSeconds($checkOut, false);
                        Log::info('Room ' . $room->room_number . ' - Diff in seconds (last entry): ' . $diffInSeconds);

                        $days = floor(abs($diffInSeconds) / (24 * 60 * 60));
                        $remainingSeconds = abs($diffInSeconds) % (24 * 60 * 60);
                        $hours = floor($remainingSeconds / (60 * 60));
                        $remainingSeconds %= (60 * 60);
                        $minutes = floor($remainingSeconds / 60);
                        $seconds = $remainingSeconds % 60;

                        $room->time_remaining = [
                            'days' => $days,
                            'hours' => $hours,
                            'minutes' => $minutes,
                            'seconds' => $seconds,
                            'check_out_timestamp' => $checkOut->timestamp,
                        ];

                        Log::info('Room ' . $room->room_number . ' - Time remaining (last entry): ' . json_encode($room->time_remaining));
                    }
                } else {
                    Log::info('Room ' . $room->room_number . ' - No entries found.');
                }
            }

            // Verificar limpiezas activas
            if ($room->status !== 'Ocupada') {
                $activeCleaning = $room->cleanings()
                    ->where('status', 'Active')
                    ->where('start_time', '<=', $currentDateTime)
                    ->where('end_time', '>=', $currentDateTime)
                    ->first();

                if (!$activeCleaning) {
                    $activeCleaning = $room->cleanings()
                        ->where('status', 'Active')
                        ->orderBy('end_time', 'desc')
                        ->first();
                }

                if ($activeCleaning) {
                    $room->current_cleaning = $activeCleaning;
                    $startTime = Carbon::parse($activeCleaning->start_time)->setTimezone('America/Argentina/Buenos_Aires');
                    $endTime = Carbon::parse($activeCleaning->end_time)->setTimezone('America/Argentina/Buenos_Aires');
                    $room->display_check_out = $endTime;

                    if ($endTime->lessThan($currentDateTime)) {
                        $room->is_being_cleaned = false;
                        $room->time_remaining = [
                            'days' => 0,
                            'hours' => 0,
                            'minutes' => 0,
                            'seconds' => 0,
                            'check_out_timestamp' => $endTime->timestamp,
                        ];
                        Log::info('Room ' . $room->room_number . ' - Cleaning expired. Setting time_remaining to zero: ' . json_encode($room->time_remaining));
                    } else {
                        $room->is_being_cleaned = true;
                        Log::info('Room ' . $room->room_number . ' - Marked as ' . $room->status . ' for display due to active cleaning.');

                        $diffInSeconds = $currentDateTime->diffInSeconds($endTime, false);
                        Log::info('Room ' . $room->room_number . ' - Cleaning Diff in seconds: ' . $diffInSeconds);

                        $maxSeconds = $activeCleaning->cleaning_type === 'deep' ? 60 * 60 : 30 * 60;
                        if ($diffInSeconds > 0) {
                            $diffInSeconds = min($diffInSeconds, $maxSeconds);
                        }

                        $days = 0;
                        $hours = floor(abs($diffInSeconds) / 3600);
                        $remainingSeconds = abs($diffInSeconds) % 3600;
                        $minutes = floor($remainingSeconds / 60);
                        $seconds = $remainingSeconds % 60;

                        $room->time_remaining = [
                            'days' => $days,
                            'hours' => $hours,
                            'minutes' => $minutes,
                            'seconds' => $seconds,
                            'check_out_timestamp' => $endTime->timestamp,
                        ];

                        Log::info('Room ' . $room->room_number . ' - Cleaning end_time: ' . $endTime->toDateTimeString() . ', timestamp: ' . $endTime->timestamp);
                        Log::info('Room ' . $room->room_number . ' - Cleaning time remaining: ' . json_encode($room->time_remaining));
                    }
                }
            }

            // No realizar ajustes automáticos al estado; respetar el estado de la base de datos
            Log::info('Room ' . $room->room_number . ' - Final Status: ' . $room->status);
            return $room;
        });

        return view('entradas.panel-control', compact('levels', 'rooms'));
    }

    // public function recepcion(Request $request, $room = null)
    // {
    //     $currentArqueo = Arqueo::whereNull('fecha_cierre')->first();

    //     if (!$currentArqueo) {
    //         return redirect()->route('entradas.panel-control')
    //             ->with('error', 'No se puede acceder al formulario de entrada. Debe aperturar la caja primero.')
    //             ->with('showCajaCerradaAlert', true);
    //     }

    //     if ($currentArqueo->usuario_id !== auth()->id()) {
    //         return redirect()->route('entradas.panel-control')
    //             ->with('error', 'No se puede acceder al formulario de entrada. La caja está aperturada por otro usuario.')
    //             ->with('showCajaOtroUsuarioAlert', true);
    //     }

    //     $room = $room ? Room::with('roomType')->findOrFail($room) : null;
    //     $clients = Client::all();
    //     $tipoDocumentos = \App\Models\TipoDocumento::orderBy('nombre', 'ASC')->get(); // Añadimos los tipos de documento
    //     $selectedClientId = $request->query('client_id'); // Obtener el client_id de la URL

    //     return view('entradas.recepcion', compact('room', 'clients', 'tipoDocumentos', 'selectedClientId'));
    // }

    public function recepcion(Request $request, $room = null)
    {
        $currentArqueo = Arqueo::whereNull('fecha_cierre')->first();

        if (!$currentArqueo) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede acceder al formulario de entrada. Debe aperturar la caja primero.')
                ->with('showCajaCerradaAlert', true);
        }

        if ($currentArqueo->usuario_id !== auth()->id()) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede acceder al formulario de entrada. La caja está aperturada por otro usuario.')
                ->with('showCajaOtroUsuarioAlert', true);
        }

        $room = $room ? Room::with(['roomType.roomTypeTariffs'])->findOrFail($room) : null;
        $clients = Client::all();
        $tipoDocumentos = \App\Models\TipoDocumento::orderBy('nombre', 'ASC')->get();
        $selectedClientId = $request->query('client_id');

        return view('entradas.recepcion', compact('room', 'clients', 'tipoDocumentos', 'selectedClientId'));
    }

    public function cancel(Request $request)
    {
        $reservationId = $request->input('reservation_id');

        try {
            $reservation = Reservation::findOrFail($reservationId);
            $reservation->delete();

            // Cambiar el estado de la habitación a Disponible
            $room = Room::findOrFail($reservationId);
            $room->status = 'Disponible';
            $room->save();

            return response()->json(['success' => true, 'message' => 'Reserva cancelada correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al cancelar la reserva: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo cancelar la reserva.'], 500);
        }
    }

    public function registros()
    {
        // Obtener las entradas con las relaciones necesarias, ordenadas por fecha de ingreso descendente
        $entries = Entry::with([
            'room',
            'client',
            'roomType',
            'renovations',
            'consumo.detalles',
            'servicioConsumo.detalles'
        ])
            ->orderBy('check_in', 'desc')
            ->get();

        // Calcular total, deuda, fechas y tipo de entrada para cada entrada
        $entries->each(function ($entry) {
            // Total inicial de la entrada (sin incluir consumos ni servicios aún)
            $total = $entry->total;

            // Sumar el total de las renovaciones (renovations)
            $totalRenovaciones = $entry->renovations->sum('total');
            $total += $totalRenovaciones;

            // Sumar el total de los consumos
            $totalConsumos = $entry->consumo ? $entry->consumo->total : 0;
            $total += $totalConsumos;

            // Sumar el total de los servicios
            $totalServicios = $entry->servicioConsumo ? $entry->servicioConsumo->total : 0;
            $total += $totalServicios;

            // Restar el descuento
            $total -= $entry->discount;

            // Calcular pagos recibidos
            $pagosRecibidos = $entry->payment_received; // Pagos de la entrada inicial

            // Sumar pagos de renovaciones
            $pagosRenovaciones = $entry->renovations->sum(function ($renovation) {
                return $renovation->efectivo + $renovation->mercadopago + $renovation->tarjeta + $renovation->transferencia;
            });
            $pagosRecibidos += $pagosRenovaciones;

            // Sumar pagos de consumos basados en los detalles pagados
            $pagosConsumos = 0;
            if ($entry->consumo && $entry->consumo->detalles) {
                $pagosConsumos = $entry->consumo->detalles
                    ->where('estado', 'Pagado')
                    ->sum('subtotal');
            }
            $pagosRecibidos += $pagosConsumos;

            // Sumar pagos de servicios basados en los detalles pagados
            $pagosServicios = 0;
            if ($entry->servicioConsumo && $entry->servicioConsumo->detalles) {
                $pagosServicios = $entry->servicioConsumo->detalles
                    ->where('estado', 'Pagado')
                    ->sum('subtotal');
            }
            $pagosRecibidos += $pagosServicios;

            // Calcular deuda
            $deuda = $total - $pagosRecibidos;

            // Asignar valores calculados a la entrada
            $entry->total_calculado = $total;
            $entry->deuda_calculada = $deuda < 0 ? 0 : $deuda; // Evitar deudas negativas

            // Calcular fechas ajustadas teniendo en cuenta las renovaciones
            $entry->fecha_ingreso = $entry->check_in; // La fecha de ingreso es siempre la inicial

            // Si hay renovaciones, la fecha de salida final es la última check_out de las renovaciones
            if ($entry->renovations->isNotEmpty()) {
                $entry->fecha_salida = $entry->renovations->sortByDesc('check_out')->first()->check_out;
            } else {
                $entry->fecha_salida = $entry->check_out; // Si no hay renovaciones, usamos la check_out de la entrada
            }

            // Calcular el tipo de entrada dinámico en función del tiempo total
            $inicio = $entry->fecha_ingreso;
            $fin = $entry->fecha_salida;
            $horasTotales = $inicio->diffInHours($fin);
            $diasTotales = $inicio->diffInDays($fin);
            $mesesTotales = $inicio->diffInMonths($fin);

            if ($horasTotales < 24) {
                // Menos de 24 horas: Mostrar en horas (ej. "15hs")
                $entry->tipo_entrada_calculado = "{$horasTotales}hs";
            } elseif ($mesesTotales < 1) {
                // Menos de 1 mes pero más de 24 horas: Mostrar en días (ej. "2días")
                $entry->tipo_entrada_calculado = "{$diasTotales}días";
            } else {
                // 1 mes o más: Mostrar en meses (ej. "2meses")
                $entry->tipo_entrada_calculado = "{$mesesTotales}meses";
            }
        });

        return view('entradas.registros', compact('entries'));
    }

    public function checkCajaForCleaning($roomId)
    {
        $currentArqueo = Arqueo::whereNull('fecha_cierre')->first();

        if (!$currentArqueo) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede proceder con la limpieza. Debe aperturar la caja primero.')
                ->with('showCajaCerradaAlert', true);
        }

        if ($currentArqueo->usuario_id !== auth()->id()) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede proceder con la limpieza. La caja está aperturada por otro usuario.')
                ->with('showCajaOtroUsuarioAlert', true);
        }

        // Si la caja está abierta y pertenece al usuario, redirigimos con el parámetro para abrir el modal
        return redirect()->route('entradas.panel-control')->with('openModal', "cleaning-modal-{$roomId}");
    }

    public function checkCajaForReservation($roomId)
    {
        $currentArqueo = Arqueo::whereNull('fecha_cierre')->first();

        if (!$currentArqueo) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede proceder con la reserva. Debe aperturar la caja primero.')
                ->with('showCajaCerradaAlert', true);
        }

        if ($currentArqueo->usuario_id !== auth()->id()) {
            return redirect()->route('entradas.panel-control')
                ->with('error', 'No se puede proceder con la reserva. La caja está aperturada por otro usuario.')
                ->with('showCajaOtroUsuarioAlert', true);
        }

        // Si la caja está abierta y pertenece al usuario, redirigimos con el parámetro para abrir el modal
        return redirect()->route('entradas.panel-control')->with('openModal', "reservation-options-modal-{$roomId}");
    }

    public function ticket($entryId)
    {
        try {
            // Buscar la entrada con sus relaciones
            $entry = Entry::with(['client.tipoDocumento', 'room', 'roomType'])->findOrFail($entryId);

            // Obtener la configuración del hotel
            $hotel = HotelSetting::first();

            // Buscar el usuario que creó la entrada
            $user = User::find($entry->created_by ?? Auth::id());

            // Preparar datos para la vista
            $simboloMonetario = $hotel->simbolo_monetario ?? '$';

            // Formatear las fechas de check_in y check_out
            $fechaEntrada = Carbon::parse($entry->check_in)->format('d/m/Y H:i');
            $fechaSalida = Carbon::parse($entry->check_out)->format('d/m/Y H:i');

            // Obtener la fecha y hora actual en el formato solicitado
            $fechaActual = Carbon::now()->format('l d \d\e F \d\e Y H:i:s');
            $dias = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $meses = [
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            ];
            $fechaActual = str_replace(
                array_keys($dias),
                array_values($dias),
                $fechaActual
            );
            $fechaActual = str_replace(
                array_keys($meses),
                array_values($meses),
                $fechaActual
            );

            // Determinar los métodos de pago usados
            $metodosPago = [];
            if ($entry->efectivo > 0) {
                $metodosPago[] = "Efectivo: {$simboloMonetario}" . number_format($entry->efectivo, 2, '.', '');
            }
            if ($entry->mercadopago > 0) {
                $metodosPago[] = "MercadoPago: {$simboloMonetario}" . number_format($entry->mercadopago, 2, '.', '');
            }
            if ($entry->tarjeta > 0) {
                $metodosPago[] = "Tarjeta: {$simboloMonetario}" . number_format($entry->tarjeta, 2, '.', '');
            }
            if ($entry->transferencia > 0) {
                $metodosPago[] = "Transferencia: {$simboloMonetario}" . number_format($entry->transferencia, 2, '.', '');
            }
            $metodosPagoTexto = !empty($metodosPago) ? implode("\n", $metodosPago) : 'No registrado';

            // Calcular el total de "Pago Recibido"
            $pagoRecibido = $entry->efectivo + $entry->mercadopago + $entry->tarjeta + $entry->transferencia;

            // Obtener la ruta absoluta del logo (si existe) desde public/uploads
            $logoPath = null;
            if ($hotel && $hotel->logo) {
                $logoPath = public_path('uploads/' . $hotel->logo);
                if (!file_exists($logoPath)) {
                    Log::warning('Logo file not found: ' . $logoPath);
                    $logoPath = null;
                }
            }

            // Cargar la vista y generar el PDF con DomPDF
            $pdf = Pdf::loadView('entradas.ticket', compact(
                'entry',
                'hotel',
                'user',
                'simboloMonetario',
                'fechaEntrada',
                'fechaSalida',
                'fechaActual',
                'metodosPagoTexto',
                'pagoRecibido',
                'logoPath'
            ));

            // Configurar el tamaño del papel y los márgenes
            $pdf->setPaper([0, 0, 170, 567], 'portrait'); // 60mm x 200mm (170pt x 567pt)
            $pdf->setOption('margin-top', 0); // Eliminar margen superior
            $pdf->setOption('margin-bottom', 0);
            $pdf->setOption('margin-left', 0); // Eliminar margen izquierdo
            $pdf->setOption('margin-right', 0); // Eliminar margen derecho

            // Habilitar la carga de recursos remotos (por si acaso)
            $pdf->setOption('isRemoteEnabled', true);

            // Mostrar el PDF en el navegador
            return $pdf->stream('ticket_entrada_' . $entryId . '.pdf');
        } catch (\Exception $e) {
            // Registrar el error y devolver un mensaje
            Log::error('Error al generar el ticket: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo generar el ticket. Revisa los logs para más detalles.'], 500);
        }
    }

    public function detalleGeneral($entryId)
    {
        try {
            // Buscar la entrada con sus relaciones
            $entry = Entry::with([
                'client.tipoDocumento',
                'room',
                'roomType',
                'renovations',
                'consumo.detalles.producto',
                'servicioConsumo.detalles.servicio'
            ])->findOrFail($entryId);

            // Obtener la configuración del hotel
            $hotel = HotelSetting::first();

            // Buscar el usuario que creó la entrada
            $user = User::find($entry->created_by ?? Auth::id());

            // Preparar datos para la vista
            $simboloMonetario = $hotel->simbolo_monetario ?? '$';

            // Formatear las fechas de check_in y check_out
            $fechaEntrada = Carbon::parse($entry->check_in)->format('d/m/Y H:i');
            $fechaSalidaPrevista = Carbon::parse($entry->check_out)->format('d/m/Y H:i');
            $fechaSalidaReal = $entry->status === 'Finished' ? Carbon::parse($entry->updated_at)->format('d/m/Y H:i') : null;

            // Obtener la fecha y hora actual para el ticket
            $fechaActual = Carbon::now()->format('l d \d\e F \d\e Y H:i:s');
            $dias = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $meses = [
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            ];
            $fechaActual = str_replace(array_keys($dias), array_values($dias), $fechaActual);
            $fechaActual = str_replace(array_keys($meses), array_values($meses), $fechaActual);

            // Calcular el total pagado del alquiler inicial (solo la entrada, sin renovaciones)
            $pagoRecibidoAlquiler = $entry->efectivo + $entry->mercadopago + $entry->tarjeta + $entry->transferencia;

            // Calcular el total pagado de las renovaciones
            $pagoRecibidoRenovaciones = 0;
            foreach ($entry->renovations as $renovation) {
                $pagoRecibidoRenovaciones += $renovation->efectivo + $renovation->mercadopago + $renovation->tarjeta + $renovation->transferencia;
            }

            // Calcular el total pagado de consumos y servicios
            $pagosConsumos = Pago::where('room_id', $entry->room_id)
                ->where('clase', 'Consumo')
                ->sum('monto');
            $pagosServicios = Pago::where('room_id', $entry->room_id)
                ->where('clase', 'Servicio')
                ->sum('monto');
            $pagoRecibidoAdicionales = $pagosConsumos + $pagosServicios;

            // Calcular el total pagado general (alquiler + renovaciones + consumos/servicios)
            $pagoRecibido = $pagoRecibidoAlquiler + $pagoRecibidoRenovaciones + $pagoRecibidoAdicionales;

            // Calcular el total de la tarifa de la habitación (incluyendo renovaciones)
            $totalTarifa = $entry->total;
            $totalRenovaciones = 0;
            foreach ($entry->renovations as $renovation) {
                $totalRenovaciones += $renovation->total;
                $totalTarifa += $renovation->total;
            }

            // Calcular el total de consumos y servicios
            $totalConsumos = 0;
            $consumosYServicios = [];

            // Manejar el único Consumo (si existe)
            if ($entry->consumo) {
                foreach ($entry->consumo->detalles as $detalle) {
                    $totalConsumos += $detalle->subtotal;
                    $consumosYServicios[] = [
                        'tipo' => 'Consumo',
                        'nombre' => $detalle->producto->producto ?? 'Consumo Desconocido',
                        'estado' => $detalle->estado == 'Pagado' ? 'Pagado' : 'Falta Pagar',
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio,
                        'subtotal' => $detalle->subtotal,
                    ];
                }
            }

            // Manejar el único ServicioConsumo (si existe)
            if ($entry->servicioConsumo) {
                foreach ($entry->servicioConsumo->detalles as $detalle) {
                    $totalConsumos += $detalle->subtotal;
                    $consumosYServicios[] = [
                        'tipo' => 'Servicio',
                        'nombre' => $detalle->servicio->nombre ?? 'Servicio Desconocido',
                        'estado' => $detalle->estado == 'Pagado' ? 'Pagado' : 'Falta Pagar',
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal,
                    ];
                }
            }

            // Calcular el total a pagar (alquiler + renovaciones + consumos/servicios - descuento)
            $totalAPagar = $totalTarifa + $totalConsumos - $entry->discount;

            // Calcular las deudas
            // Deuda del alquiler inicial
            $totalAlquilerConDescuento = $entry->total - $entry->discount;
            $porPagarAlquiler = max(0, $totalAlquilerConDescuento - $pagoRecibidoAlquiler);

            // Deuda de las renovaciones
            $porPagarRenovaciones = max(0, $totalRenovaciones - $pagoRecibidoRenovaciones);

            // Deuda total
            $deuda = $totalAPagar - $pagoRecibido;

            // Obtener la ruta absoluta del logo desde public/uploads
            $logoPath = null;
            if ($hotel && $hotel->logo) {
                $logoPath = public_path('uploads/' . $hotel->logo);
                if (!file_exists($logoPath)) {
                    Log::warning('Logo file not found: ' . $logoPath);
                    $logoPath = null;
                }
            }

            // Generar un número de ticket
            $numeroTicket = $entry->id;

            // Cargar la vista y generar el PDF con DomPDF
            $pdf = Pdf::loadView('entradas.detalle_general', compact(
                'entry',
                'hotel',
                'user',
                'simboloMonetario',
                'fechaEntrada',
                'fechaSalidaPrevista',
                'fechaSalidaReal',
                'fechaActual',
                'pagoRecibido',
                'pagoRecibidoAlquiler',
                'pagoRecibidoRenovaciones',
                'totalTarifa',
                'totalRenovaciones',
                'totalConsumos',
                'consumosYServicios',
                'totalAPagar',
                'deuda',
                'porPagarAlquiler',
                'porPagarRenovaciones',
                'logoPath',
                'numeroTicket'
            ));

            // Configurar el tamaño del papel y los márgenes
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);
            $pdf->setOption('margin-right', 10);

            // Habilitar la carga de recursos remotos
            $pdf->setOption('isRemoteEnabled', true);

            // Mostrar el PDF en el navegador
            return $pdf->stream('detalle_general_' . $entryId . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el detalle general: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo generar el detalle general. Revisa los logs para más detalles.'], 500);
        }
    }
}
