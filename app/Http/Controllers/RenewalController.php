<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Room;
use App\Models\Client;
use App\Models\Renewal;
use App\Models\Arqueo;
use App\Models\MovimientoCaja;
use App\Models\Pago;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RenewalController extends Controller
{

    // public function index()
    // {
    //     // Configurar idioma y zona horaria
    //     Carbon::setLocale('es');
    //     date_default_timezone_set('America/Argentina/Buenos_Aires');

    //     $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');

    //     // Obtener entradas activas con las relaciones necesarias
    //     $entries = Entry::with(['room', 'client', 'roomType', 'renovations'])
    //         ->where('status', 'Active')
    //         ->get();

    //     // Mapear las entradas para calcular la fecha de salida más reciente
    //     $entries = $entries->map(function ($entry) use ($currentDateTime) {
    //         // Calcular la fecha de salida más reciente considerando renovaciones
    //         $latestCheckOut = $entry->check_out; // Fecha de salida inicial de la entrada
    //         if ($entry->renovations->isNotEmpty()) {
    //             // Ordenar renovaciones por check_out descendente y tomar la más reciente
    //             $latestRenovation = $entry->renovations->sortByDesc('check_out')->first();
    //             $latestCheckOut = $latestRenovation->check_out;
    //             Log::info("Room {$entry->room->room_number} - Latest check_out from renovations: {$latestCheckOut}");
    //         } else {
    //             Log::info("Room {$entry->room->room_number} - No renovations found, using entry check_out: {$latestCheckOut}");
    //         }

    //         // Verificar si $latestCheckOut es válido antes de parsearlo
    //         try {
    //             $entry->latest_check_out = !empty($latestCheckOut) ? Carbon::parse($latestCheckOut)->setTimezone('America/Argentina/Buenos_Aires') : null;
    //         } catch (\Exception $e) {
    //             Log::error("Room {$entry->room->room_number} - Error parsing check_out: {$latestCheckOut}. Error: " . $e->getMessage());
    //             $entry->latest_check_out = null;
    //         }

    //         return $entry;
    //     });

    //     return view('entradas.renovaciones', compact('entries'));
    // }

    public function index()
    {
        // Configurar idioma y zona horaria
        Carbon::setLocale('es');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        $currentDateTime = Carbon::now()->setTimezone('America/Argentina/Buenos_Aires');

        // Obtener renovaciones con las relaciones necesarias
        $renewals = Renewal::with(['entry.room', 'entry.client', 'entry.roomType'])
            ->whereHas('entry', function ($query) {
                $query->where('status', 'Active'); // Solo entradas activas
            })
            ->get();

        // Mapear las renovaciones para agregar información adicional si es necesario
        $renewals = $renewals->map(function ($renewal) use ($currentDateTime) {
            try {
                $renewal->latest_check_out = !empty($renewal->check_out)
                    ? Carbon::parse($renewal->check_out)->setTimezone('America/Argentina/Buenos_Aires')
                    : null;
                Log::info("Renewal for Room {$renewal->entry->room->room_number} - Check_out: {$renewal->check_out}");
            } catch (\Exception $e) {
                Log::error("Renewal for Room {$renewal->entry->room->room_number} - Error parsing check_out: {$renewal->check_out}. Error: " . $e->getMessage());
                $renewal->latest_check_out = null;
            }

            return $renewal;
        });

        return view('entradas.renovaciones', compact('renewals'));
    }

    // Mostrar el formulario de renovación para una entrada específica
    // public function create($entryId)
    // {
    //     $entry = Entry::with(['room.roomType', 'client'])->findOrFail($entryId);
    //     $clients = Client::all();

    //     // Verificar que la entrada esté activa
    //     if ($entry->status !== 'Active') {
    //         return redirect()->route('entradas.renovaciones')
    //             ->with('error', 'No se puede renovar una entrada que no está activa.');
    //     }

    //     return view('entradas.renewal-create', compact('entry', 'clients'));
    // }

    public function create($entryId)
    {
        $entry = Entry::with(['room.roomType', 'client'])->findOrFail($entryId);
        $clients = Client::all();
        $tariffs = \App\Models\RoomTypeTariff::where('room_type_id', $entry->room_type_id)->get();

        // Verificar que la entrada esté activa
        if ($entry->status !== 'Active') {
            return redirect()->route('entradas.renovaciones')
                ->with('error', 'No se puede renovar una entrada que no está activa.');
        }

        return view('entradas.renewal-create', compact('entry', 'clients', 'tariffs'));
    }

    // public function store(Request $request)
    // {
    //     Log::info('Iniciando creación de renovación', ['request' => $request->all()]);

    //     $request->validate([
    //         'entry_id' => 'required|exists:entries,id',
    //         'room_id' => 'required|exists:rooms,id',
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'client_id' => 'required|exists:clients,id',
    //         'entry_type' => 'required|in:4_hours,full_night,month',
    //         'quantity' => 'required|integer|min:1',
    //         'check_in_date' => 'required|date',
    //         'check_in_time' => 'required|date_format:H:i',
    //         'check_out_date' => 'required|date',
    //         'check_out_time' => 'required|date_format:H:i',
    //         'efectivo' => 'nullable|numeric|min:0',
    //         'mercadopago' => 'nullable|numeric|min:0',
    //         'tarjeta' => 'nullable|numeric|min:0',
    //         'transferencia' => 'nullable|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0',
    //         'total' => 'required|numeric|min:0',
    //         'debt' => 'required|numeric|min:0',
    //         'observations' => 'nullable|string',
    //     ]);

    //     $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
    //     $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

    //     Log::info('Validaciones pasadas', [
    //         'totalPagado' => $totalPagado,
    //         'pagoStatus' => $pagoStatus,
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $currentArqueo = Arqueo::where('status', 'Abierto')->first();
    //         if (!$currentArqueo) {
    //             Log::error('No hay arqueo abierto');
    //             throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una renovación.');
    //         }

    //         $entry = Entry::findOrFail($request->entry_id);
    //         if ($entry->status !== 'Active') {
    //             Log::error('Entrada no activa', ['entry_id' => $entry->id, 'status' => $entry->status]);
    //             throw new \Exception('La entrada no está activa y no puede ser renovada.');
    //         }

    //         $room = Room::findOrFail($request->room_id);
    //         if ($room->status !== 'Ocupada') {
    //             Log::error('Habitación no ocupada', ['room_id' => $room->id, 'status' => $room->status]);
    //             throw new \Exception('La habitación no está ocupada. Estado actual: ' . $room->status);
    //         }

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

    //         $existingReservation = Reservation::where('room_id', $request->room_id)
    //             ->where('status', 'Confirmada')
    //             ->where(function ($query) use ($checkIn, $checkOut) {
    //                 $query->where(function ($q) use ($checkIn, $checkOut) {
    //                     $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
    //                         ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
    //                 })
    //                     ->orWhere(function ($q) use ($checkIn, $checkOut) {
    //                         $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkIn->toDateTimeString()])
    //                             ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
    //                     })
    //                     ->orWhere(function ($q) use ($checkIn, $checkOut) {
    //                         $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
    //                             ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkOut->toDateTimeString()]);
    //                     });
    //             })
    //             ->exists();

    //         if ($existingReservation) {
    //             Log::error('Conflicto con reserva existente');
    //             throw new \Exception('No se puede renovar: hay una reserva confirmada para la habitación en el período solicitado.');
    //         }

    //         $renewal = Renewal::create([
    //             'entry_id' => $request->entry_id,
    //             'room_id' => $request->room_id,
    //             'room_type_id' => $request->room_type_id,
    //             'client_id' => $request->client_id,
    //             'entry_type' => $request->entry_type,
    //             'check_in' => $checkIn,
    //             'check_out' => $checkOut,
    //             'quantity' => $request->quantity,
    //             'discount' => $request->discount ?? 0,
    //             'efectivo' => $request->efectivo ?? 0,
    //             'mercadopago' => $request->mercadopago ?? 0,
    //             'tarjeta' => $request->tarjeta ?? 0,
    //             'transferencia' => $request->transferencia ?? 0,
    //             'total' => $request->total,
    //             'debt' => $request->debt,
    //             'pago' => $pagoStatus,
    //             'observations' => $request->observations,
    //             'status' => 'Active',
    //         ]);

    //         Log::info('Renovación creada', ['renewal_id' => $renewal->id]);

    //         // Actualizar la entrada (solo check_out, debt y pago)
    //         Log::info('Datos antes de actualizar la entrada', [
    //             'entry_id' => $entry->id,
    //             'check_out' => $entry->check_out,
    //             'total' => $entry->total,
    //             'debt' => $entry->debt,
    //             'pago' => $entry->pago,
    //         ]);

    //         $entry->update([
    //             'check_out' => $checkOut,
    //             'debt' => $entry->debt + $request->debt,
    //             'pago' => ($entry->debt + $request->debt) == 0 ? 'Pagado' : 'Falta Pagar',
    //         ]);

    //         Log::info('Datos después de actualizar la entrada', [
    //             'entry_id' => $entry->id,
    //             'check_out' => $entry->check_out,
    //             'total' => $entry->total,
    //             'debt' => $entry->debt,
    //             'pago' => $entry->pago,
    //         ]);

    //         if ($totalPagado > 0) {
    //             // Registrar el pago en la tabla pagos
    //             $descripcionPago = "Pago de renovación de habitación {$room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
    //             Pago::create([
    //                 'entry_id' => $request->entry_id,
    //                 'room_id' => $request->room_id,
    //                 'fecha' => now(),
    //                 'descripcion' => $descripcionPago,
    //                 'clase' => 'Renovación',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'usuario_id' => Auth::id(),
    //                 'arqueo_id' => $currentArqueo->id,
    //             ]);

    //             // Registrar el movimiento en la tabla movimiento_cajas
    //             MovimientoCaja::create([
    //                 'arqueo_id' => $currentArqueo->id,
    //                 'tipo' => 'Ingreso',
    //                 'clase' => 'Renovación',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'descripcion' => "Ingreso por renovación de habitación {$room->room_number}",
    //                 'usuario_id' => Auth::id(),
    //             ]);
    //         }

    //         DB::commit();

    //         // Redirigir al panel de control con un mensaje de éxito
    //         return redirect()->route('entradas.panel-control')
    //             ->with('success', 'Renovación registrada con éxito.');
    //     } catch (\Exception $e) {
    //         Log::error('Error al crear renovación', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         DB::rollBack();
    //         return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
    //     }
    // }

    // public function store(Request $request)
    // {
    //     Log::info('Iniciando creación de renovación', ['request' => $request->all()]);

    //     $request->validate([
    //         'entry_id' => 'required|exists:entries,id',
    //         'room_id' => 'required|exists:rooms,id',
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'client_id' => 'required|exists:clients,id',
    //         'room_type_tariff_id' => 'required|exists:room_type_tariffs,id',
    //         'quantity' => 'required|integer|min:1',
    //         'check_in_date' => 'required|date',
    //         'check_in_time' => 'required|date_format:H:i',
    //         'check_out_date' => 'required|date',
    //         'check_out_time' => 'required|date_format:H:i',
    //         'efectivo' => 'nullable|numeric|min:0',
    //         'mercadopago' => 'nullable|numeric|min:0',
    //         'tarjeta' => 'nullable|numeric|min:0',
    //         'transferencia' => 'nullable|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0',
    //         'total' => 'required|numeric|min:0',
    //         'debt' => 'required|numeric|min:0',
    //         'observations' => 'nullable|string',
    //     ]);

    //     $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
    //     $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

    //     Log::info('Validaciones pasadas', [
    //         'totalPagado' => $totalPagado,
    //         'pagoStatus' => $pagoStatus,
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $currentArqueo = Arqueo::where('status', 'Abierto')->first();
    //         if (!$currentArqueo) {
    //             Log::error('No hay arqueo abierto');
    //             throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una renovación.');
    //         }

    //         $entry = Entry::findOrFail($request->entry_id);
    //         if ($entry->status !== 'Active') {
    //             Log::error('Entrada no activa', ['entry_id' => $entry->id, 'status' => $entry->status]);
    //             throw new \Exception('La entrada no está activa y no puede ser renovada.');
    //         }

    //         $room = Room::findOrFail($request->room_id);
    //         if ($room->status !== 'Ocupada') {
    //             Log::error('Habitación no ocupada', ['room_id' => $room->id, 'status' => $room->status]);
    //             throw new \Exception('La habitación no está ocupada. Estado actual: ' . $room->status);
    //         }

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

    //         $existingReservation = Reservation::where('room_id', $request->room_id)
    //             ->where('status', 'Confirmada')
    //             ->where(function ($query) use ($checkIn, $checkOut) {
    //                 $query->where(function ($q) use ($checkIn, $checkOut) {
    //                     $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
    //                         ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
    //                 })
    //                     ->orWhere(function ($q) use ($checkIn, $checkOut) {
    //                         $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkIn->toDateTimeString()])
    //                             ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
    //                     })
    //                     ->orWhere(function ($q) use ($checkIn, $checkOut) {
    //                         $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
    //                             ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkOut->toDateTimeString()]);
    //                     });
    //             })
    //             ->exists();

    //         if ($existingReservation) {
    //             Log::error('Conflicto con reserva existente');
    //             throw new \Exception('No se puede renovar: hay una reserva confirmada para la habitación en el período solicitado.');
    //         }

    //         $renewal = Renewal::create([
    //             'entry_id' => $request->entry_id,
    //             'room_id' => $request->room_id,
    //             'room_type_id' => $request->room_type_id,
    //             'client_id' => $request->client_id,
    //             'room_type_tariff_id' => $request->room_type_tariff_id,
    //             'check_in' => $checkIn,
    //             'check_out' => $checkOut,
    //             'quantity' => $request->quantity,
    //             'discount' => $request->discount ?? 0,
    //             'efectivo' => $request->efectivo ?? 0,
    //             'mercadopago' => $request->mercadopago ?? 0,
    //             'tarjeta' => $request->tarjeta ?? 0,
    //             'transferencia' => $request->transferencia ?? 0,
    //             'total' => $request->total,
    //             'debt' => $request->debt,
    //             'pago' => $pagoStatus,
    //             'observations' => $request->observations,
    //             'status' => 'Active',
    //         ]);

    //         Log::info('Renovación creada', ['renewal_id' => $renewal->id]);

    //         // Actualizar la entrada (solo check_out, debt y pago)
    //         Log::info('Datos antes de actualizar la entrada', [
    //             'entry_id' => $entry->id,
    //             'check_out' => $entry->check_out,
    //             'total' => $entry->total,
    //             'debt' => $entry->debt,
    //             'pago' => $entry->pago,
    //         ]);

    //         $entry->update([
    //             'check_out' => $checkOut,
    //             'debt' => $entry->debt + $request->debt,
    //             'pago' => ($entry->debt + $request->debt) == 0 ? 'Pagado' : 'Falta Pagar',
    //         ]);

    //         Log::info('Datos después de actualizar la entrada', [
    //             'entry_id' => $entry->id,
    //             'check_out' => $entry->check_out,
    //             'total' => $entry->total,
    //             'debt' => $entry->debt,
    //             'pago' => $entry->pago,
    //         ]);

    //         if ($totalPagado > 0) {
    //             // Registrar el pago en la tabla pagos
    //             $descripcionPago = "Pago de renovación de habitación {$room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
    //             Pago::create([
    //                 'entry_id' => $request->entry_id,
    //                 'room_id' => $request->room_id,
    //                 'fecha' => now(),
    //                 'descripcion' => $descripcionPago,
    //                 'clase' => 'Renovación',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'usuario_id' => Auth::id(),
    //                 'arqueo_id' => $currentArqueo->id,
    //             ]);

    //             // Registrar el movimiento en la tabla movimiento_cajas
    //             MovimientoCaja::create([
    //                 'arqueo_id' => $currentArqueo->id,
    //                 'tipo' => 'Ingreso',
    //                 'clase' => 'Renovación',
    //                 'monto' => $totalPagado,
    //                 'efectivo' => $request->efectivo ?? 0,
    //                 'mercadopago' => $request->mercadopago ?? 0,
    //                 'tarjeta' => $request->tarjeta ?? 0,
    //                 'transferencia' => $request->transferencia ?? 0,
    //                 'descripcion' => "Ingreso por renovación de habitación {$room->room_number}",
    //                 'usuario_id' => Auth::id(),
    //             ]);
    //         }

    //         DB::commit();

    //         // Redirigir al panel de control con un mensaje de éxito
    //         return redirect()->route('entradas.panel-control')
    //             ->with('success', 'Renovación registrada con éxito.');
    //     } catch (\Exception $e) {
    //         Log::error('Error al crear renovación', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         DB::rollBack();
    //         return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
    //     }
    // }

    public function store(Request $request)
    {
        Log::info('Iniciando creación de renovación', ['request' => $request->all()]);

        $request->validate([
            'entry_id' => 'required|exists:entries,id',
            'room_id' => 'required|exists:rooms,id',
            'room_type_id' => 'required|exists:room_types,id',
            'client_id' => 'required|exists:clients,id',
            'room_type_tariff_id' => 'required|exists:room_type_tariffs,id',
            'quantity' => 'required|integer|min:1',
            'check_in_date' => 'required|date',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_date' => 'required|date',
            'check_out_time' => 'required|date_format:H:i',
            'efectivo' => 'nullable|numeric|min:0',
            'mercadopago' => 'nullable|numeric|min:0',
            'tarjeta' => 'nullable|numeric|min:0',
            'transferencia' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'debt' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        $totalPagado = ($request->efectivo ?? 0) + ($request->mercadopago ?? 0) + ($request->tarjeta ?? 0) + ($request->transferencia ?? 0);
        $pagoStatus = $request->debt == 0 ? 'Pagado' : 'Falta Pagar';

        Log::info('Validaciones pasadas', [
            'totalPagado' => $totalPagado,
            'pagoStatus' => $pagoStatus,
            'room_type_tariff_id' => $request->room_type_tariff_id, // Depuración adicional
        ]);

        DB::beginTransaction();
        try {
            $currentArqueo = Arqueo::where('status', 'Abierto')->first();
            if (!$currentArqueo) {
                Log::error('No hay arqueo abierto');
                throw new \Exception('No hay un arqueo abierto. Por favor, abra un arqueo antes de registrar una renovación.');
            }

            $entry = Entry::findOrFail($request->entry_id);
            if ($entry->status !== 'Active') {
                Log::error('Entrada no activa', ['entry_id' => $entry->id, 'status' => $entry->status]);
                throw new \Exception('La entrada no está activa y no puede ser renovada.');
            }

            $room = Room::findOrFail($request->room_id);
            if ($room->status !== 'Ocupada') {
                Log::error('Habitación no ocupada', ['room_id' => $room->id, 'status' => $room->status]);
                throw new \Exception('La habitación no está ocupada. Estado actual: ' . $room->status);
            }

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

            $existingReservation = Reservation::where('room_id', $request->room_id)
                ->where('status', 'Confirmada')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
                            ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
                    })
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkIn->toDateTimeString()])
                                ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkIn->toDateTimeString()]);
                        })
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->whereRaw("CONCAT(check_in, ' ', check_in_time) <= ?", [$checkOut->toDateTimeString()])
                                ->whereRaw("CONCAT(check_out, ' ', check_out_time) >= ?", [$checkOut->toDateTimeString()]);
                        });
                })
                ->exists();

            if ($existingReservation) {
                Log::error('Conflicto con reserva existente');
                throw new \Exception('No se puede renovar: hay una reserva confirmada para la habitación en el período solicitado.');
            }

            // Verificar que room_type_tariff_id no sea null antes de crear
            if (!$request->room_type_tariff_id) {
                Log::error('room_type_tariff_id es null o no válido', ['request' => $request->all()]);
                throw new \Exception('El ID de la tarifa no fue proporcionado o es inválido.');
            }

            $renewal = Renewal::create([
                'entry_id' => $request->entry_id,
                'room_id' => $request->room_id,
                'room_type_id' => $request->room_type_id,
                'client_id' => $request->client_id,
                'room_type_tariff_id' => $request->room_type_tariff_id, // Asegurar que se guarde
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'quantity' => $request->quantity,
                'discount' => $request->discount ?? 0,
                'efectivo' => $request->efectivo ?? 0,
                'mercadopago' => $request->mercadopago ?? 0,
                'tarjeta' => $request->tarjeta ?? 0,
                'transferencia' => $request->transferencia ?? 0,
                'total' => $request->total,
                'debt' => $request->debt,
                'pago' => $pagoStatus,
                'observations' => $request->observations,
                'status' => 'Active',
            ]);

            Log::info('Renovación creada', ['renewal_id' => $renewal->id]);

            // Actualizar la entrada (solo check_out, debt y pago)
            Log::info('Datos antes de actualizar la entrada', [
                'entry_id' => $entry->id,
                'check_out' => $entry->check_out,
                'total' => $entry->total,
                'debt' => $entry->debt,
                'pago' => $entry->pago,
            ]);

            $entry->update([
                'check_out' => $checkOut,
                'debt' => $entry->debt + $request->debt,
                'pago' => ($entry->debt + $request->debt) == 0 ? 'Pagado' : 'Falta Pagar',
            ]);

            Log::info('Datos después de actualizar la entrada', [
                'entry_id' => $entry->id,
                'check_out' => $entry->check_out,
                'total' => $entry->total,
                'debt' => $entry->debt,
                'pago' => $entry->pago,
            ]);

            if ($totalPagado > 0) {
                // Registrar el pago en la tabla pagos
                $descripcionPago = "Pago de renovación de habitación {$room->room_number} (Cliente: {$entry->client->name} {$entry->client->lastname})";
                Pago::create([
                    'entry_id' => $request->entry_id,
                    'room_id' => $request->room_id,
                    'fecha' => now(),
                    'descripcion' => $descripcionPago,
                    'clase' => 'Renovación',
                    'monto' => $totalPagado,
                    'efectivo' => $request->efectivo ?? 0,
                    'mercadopago' => $request->mercadopago ?? 0,
                    'tarjeta' => $request->tarjeta ?? 0,
                    'transferencia' => $request->transferencia ?? 0,
                    'usuario_id' => Auth::id(),
                    'arqueo_id' => $currentArqueo->id,
                ]);

                // Registrar el movimiento en la tabla movimiento_cajas
                MovimientoCaja::create([
                    'arqueo_id' => $currentArqueo->id,
                    'tipo' => 'Ingreso',
                    'clase' => 'Renovación',
                    'monto' => $totalPagado,
                    'efectivo' => $request->efectivo ?? 0,
                    'mercadopago' => $request->mercadopago ?? 0,
                    'tarjeta' => $request->tarjeta ?? 0,
                    'transferencia' => $request->transferencia ?? 0,
                    'descripcion' => "Ingreso por renovación de habitación {$room->room_number}",
                    'usuario_id' => Auth::id(),
                ]);
            }

            DB::commit();

            // Redirigir al panel de control con un mensaje de éxito
            return redirect()->route('entradas.panel-control')
                ->with('success', 'Renovación registrada con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al crear renovación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    private function getFormaPago(Request $request)
    {
        if ($request->efectivo > 0) return 'Efectivo';
        if ($request->mercadopago > 0) return 'MercadoPago';
        if ($request->tarjeta > 0) return 'Tarjeta';
        if ($request->transferencia > 0) return 'Transferencia';
        return 'Desconocida';
    }
}
