<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_permission:ver-reservas')->only(['index', 'show', 'calendario']);
        $this->middleware('check_permission:crear-reservas')->only(['create', 'store']);
        $this->middleware('check_permission:editar-reservas')->only(['edit', 'update']);
        $this->middleware('check_permission:eliminar-reservas')->only(['destroy']);
    }

    public function index()
    {
        $reservations = Reservation::with(['client', 'room'])->orderBy('id', 'DESC')->get();
        $clients = Client::all();
        $rooms = Room::all();
        return view('reservas.index', compact('reservations', 'clients', 'rooms'));
    }

    public function create()
    {
        $clients = Client::orderBy('name', 'ASC')->get();
        $rooms = Room::orderBy('room_number', 'ASC')->get();
        return view('reservas.create', compact('clients', 'rooms'));
    }

    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'client_id' => 'required|exists:clients,id',
            'check_in_date' => 'required|date',
            'check_in_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'check_out_date' => 'required|date',
            'check_out_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        // Normalizar check_in_time y check_out_time (quitar segundos si los hay)
        $checkInTime = preg_match('/^\d{2}:\d{2}:\d{2}$/', $request->check_in_time)
            ? substr($request->check_in_time, 0, 5)
            : $request->check_in_time;
        $checkOutTime = preg_match('/^\d{2}:\d{2}:\d{2}$/', $request->check_out_time)
            ? substr($request->check_out_time, 0, 5)
            : $request->check_out_time;

        // Combinar fecha y hora para check_in y check_out
        $checkIn = Carbon::createFromFormat('Y-m-d H:i', $request->check_in_date . ' ' . $checkInTime);
        $checkOut = Carbon::createFromFormat('Y-m-d H:i', $request->check_out_date . ' ' . $checkOutTime);

        // Validar que check_out sea posterior a check_in
        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return redirect()->route('reservas.calendario')
                ->with('error', 'La fecha y hora de salida deben ser posteriores a la fecha y hora de entrada.')
                ->withInput();
        }

        // Verificar si ya existe una reserva para el rango de fechas
        $existingReservations = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $request->reservation_id ?? 0) // Excluir la reserva actual si es una actualización
            ->where('status', 'Confirmada') // Solo considerar reservas confirmadas
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        if ($existingReservations) {
            return redirect()->route('reservas.calendario')
                ->with('error', 'La habitación no está disponible en el rango de fechas seleccionado.')
                ->withInput();
        }

        // Crear la reserva
        Reservation::create([
            'room_id' => $request->room_id,
            'client_id' => $request->client_id,
            'check_in' => $checkIn,
            'check_in_time' => $checkInTime,
            'check_out' => $checkOut,
            'check_out_time' => $checkOutTime,
            'status' => 'Confirmada',
        ]);

        // Cambiar de estado a la habitación a Reservada
        Room::where('id', $request->room_id)->update(['status' => 'Reservada']);

        return redirect()->route('reservas.calendario')
            ->with('success', 'Se registró la reserva satisfactoriamente.');
    }

    public function show($id)
    {
        $reserva = Reservation::with(['client', 'room'])->findOrFail($id);
        return view('reservas.show', compact('reserva'));
    }

    public function edit($id)
    {
        $reserva = Reservation::with(['client', 'room'])->findOrFail($id);
        $clients = Client::orderBy('name', 'ASC')->get();
        $rooms = Room::orderBy('room_number', 'ASC')->get();
        return view('reservas.edit', compact('reserva', 'clients', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $reserva = Reservation::findOrFail($id);

        // Validar los datos de la solicitud
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'client_id' => 'required|exists:clients,id',
            'check_in_date' => 'required|date',
            'check_in_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'check_out_date' => 'required|date',
            'check_out_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        // Normalizar check_in_time y check_out_time (quitar segundos si los hay)
        $checkInTime = preg_match('/^\d{2}:\d{2}:\d{2}$/', $request->check_in_time)
            ? substr($request->check_in_time, 0, 5)
            : $request->check_in_time;
        $checkOutTime = preg_match('/^\d{2}:\d{2}:\d{2}$/', $request->check_out_time)
            ? substr($request->check_out_time, 0, 5)
            : $request->check_out_time;

        // Combinar fecha y hora para check_in y check_out
        $checkIn = Carbon::createFromFormat('Y-m-d H:i', $request->check_in_date . ' ' . $checkInTime);
        $checkOut = Carbon::createFromFormat('Y-m-d H:i', $request->check_out_date . ' ' . $checkOutTime);

        // Validar que check_out sea posterior a check_in
        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return redirect()->route('reservas.calendario')
                ->with('error', 'La fecha y hora de salida deben ser posteriores a la fecha y hora de entrada.')
                ->withInput();
        }

        // Verificar si ya existe una reserva para el rango de fechas
        $existingReservations = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $reserva->id) // Excluir la reserva actual
            ->where('status', 'Confirmada') // Solo considerar reservas confirmadas
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        if ($existingReservations) {
            return redirect()->route('reservas.calendario')
                ->with('error', 'La habitación no está disponible en el rango de fechas seleccionado.')
                ->withInput();
        }

        // Actualizar la reserva
        $reserva->update([
            'room_id' => $request->room_id,
            'client_id' => $request->client_id,
            'check_in' => $checkIn,
            'check_in_time' => $checkInTime,
            'check_out' => $checkOut,
            'check_out_time' => $checkOutTime,
            'status' => 'Confirmada',
        ]);

        return redirect()->route('reservas.calendario')
            ->with('success', 'Se actualizó la reserva satisfactoriamente.');
    }

    public function destroy($id)
    {
        // Nota: Este método ya no se usa en el calendario, ya que ahora cancelamos las reservas en lugar de eliminarlas.
        // Si no se usa en ninguna otra parte, podrías eliminarlo más adelante.
        $reserva = Reservation::findOrFail($id);
        $reserva->delete();

        return redirect()->route('reservas.calendario')
            ->with('success', 'Se eliminó la reserva satisfactoriamente.');
    }

    public function calendario()
    {
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        // Mes actual y el siguiente
        $startOfFirstMonth = Carbon::createFromDate($year, $month, 1);
        $endOfSecondMonth = $startOfFirstMonth->copy()->addMonth()->endOfMonth();

        // Cargar las habitaciones con sus reservas y relaciones
        $rooms = Room::with(['level', 'roomType', 'reservations' => function ($query) use ($startOfFirstMonth, $endOfSecondMonth) {
            $query->with('client')
                ->where('status', 'Confirmada')
                ->where(function ($q) use ($startOfFirstMonth, $endOfSecondMonth) {
                    $q->whereBetween('check_in', [$startOfFirstMonth, $endOfSecondMonth])
                        ->orWhereBetween('check_out', [$startOfFirstMonth, $endOfSecondMonth])
                        ->orWhere(function ($subQuery) use ($startOfFirstMonth, $endOfSecondMonth) {
                            $subQuery->where('check_in', '<', $startOfFirstMonth)
                                ->where('check_out', '>', $endOfSecondMonth);
                        });
                });
        }])
            ->orderBy('room_number', 'ASC')
            ->get();

        // Obtener las entradas activas (habitaciones ocupadas) con su rango de fechas
        $occupiedEntries = \App\Models\Entry::with('room')
            ->where('salida', 0) // Entradas activas (sin salida)
            ->where('status', 'Active') // Asegurar que la entrada esté activa
            ->where(function ($query) use ($startOfFirstMonth, $endOfSecondMonth) {
                $query->whereBetween('check_in', [$startOfFirstMonth, $endOfSecondMonth])
                    ->orWhereBetween('check_out', [$startOfFirstMonth, $endOfSecondMonth])
                    ->orWhere(function ($subQuery) use ($startOfFirstMonth, $endOfSecondMonth) {
                        $subQuery->where('check_in', '<', $startOfFirstMonth)
                            ->where('check_out', '>', $endOfSecondMonth);
                    });
            })
            ->get()
            ->groupBy('room_id')
            ->map(function ($entries, $roomId) use ($startOfFirstMonth, $endOfSecondMonth) {
                return $entries->map(function ($entry) use ($startOfFirstMonth, $endOfSecondMonth) {
                    $checkIn = Carbon::parse($entry->check_in);
                    $checkOut = Carbon::parse($entry->check_out);

                    $start = $checkIn->lt($startOfFirstMonth) ? $startOfFirstMonth : $checkIn;
                    $end = $checkOut->gt($endOfSecondMonth) ? $endOfSecondMonth : $checkOut;

                    return [
                        'check_in' => $start->format('Y-m-d'),
                        'check_out' => $end->format('Y-m-d'),
                    ];
                })->values()->toArray();
            })
            ->toArray();

        // Log para depuración
        Log::info('Habitaciones ocupadas con rangos:', $occupiedEntries);

        $clients = Client::orderBy('name', 'ASC')->get();
        $tipoDocumentos = \App\Models\TipoDocumento::orderBy('nombre', 'ASC')->get();

        // Generar los días para ambos meses
        $days = [];
        $currentDate = $startOfFirstMonth->copy();
        while ($currentDate <= $endOfSecondMonth) {
            $days[] = [
                'day' => $currentDate->day,
                'month' => $currentDate->month,
                'year' => $currentDate->year,
                'date' => $currentDate->format('Y-m-d'),
            ];
            $currentDate->addDay();
        }

        return view('reservas.calendario', compact('rooms', 'clients', 'tipoDocumentos', 'days', 'month', 'year', 'occupiedEntries'));
    }

    public function cancel(Request $request)
    {
        try {
            // Validar que se haya enviado el ID de la reserva
            $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,id'
            ]);

            // Buscar la reserva
            $reservation = Reservation::findOrFail($request->reservation_id);

            // Verificar si la reserva ya está cancelada
            if ($reservation->status === 'Cancelada') {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva ya está cancelada.'
                ], 400);
            }

            // Actualizar el estado de la reserva a "Cancelada"
            $reservation->status = 'Cancelada';
            $reservation->save();

            // Registrar la acción en los logs
            Log::info('Reserva cancelada: ID ' . $reservation->id);

            // Cambiar de estado a la habitacion en Disponible
            $room = Room::find($reservation->room_id);
            $room->status = 'Disponible';
            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente.'
            ]);
        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error('Error al cancelar la reserva: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cancelar la reserva.'
            ], 500);
        }
    }

    public function loadMonth(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $direction = $request->input('direction');

        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Cargar las habitaciones con sus reservas para el mes solicitado
        $rooms = Room::with(['level', 'roomType', 'reservations' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->with('client')
                ->where('status', 'Confirmada')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('check_in', [$startOfMonth, $endOfMonth])
                        ->orWhereBetween('check_out', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($subQuery) use ($startOfMonth, $endOfMonth) {
                            $subQuery->where('check_in', '<', $startOfMonth)
                                ->where('check_out', '>', $endOfMonth);
                        });
                });
        }])
            ->orderBy('room_number', 'ASC')
            ->get();

        // Obtener las entradas activas (habitaciones ocupadas) para el mes solicitado
        $occupiedEntries = \App\Models\Entry::with('room')
            ->where('salida', 0)
            ->where('status', 'Active')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('check_in', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('check_out', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($subQuery) use ($startOfMonth, $endOfMonth) {
                        $subQuery->where('check_in', '<', $startOfMonth)
                            ->where('check_out', '>', $endOfMonth);
                    });
            })
            ->get()
            ->groupBy('room_id')
            ->map(function ($entries, $roomId) use ($startOfMonth, $endOfMonth) {
                return $entries->map(function ($entry) use ($startOfMonth, $endOfMonth) {
                    $checkIn = Carbon::parse($entry->check_in);
                    $checkOut = Carbon::parse($entry->check_out);

                    $start = $checkIn->lt($startOfMonth) ? $startOfMonth : $checkIn;
                    $end = $checkOut->gt($endOfMonth) ? $endOfMonth : $checkOut;

                    return [
                        'check_in' => $start->format('Y-m-d'),
                        'check_out' => $end->format('Y-m-d'),
                    ];
                })->values()->toArray();
            })
            ->toArray();

        // Generar los días del mes solicitado
        $days = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $days[] = [
                'day' => $currentDate->day,
                'month' => $currentDate->month,
                'year' => $currentDate->year,
                'date' => $currentDate->format('Y-m-d'),
            ];
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'days' => $days,
            'rooms' => $rooms,
            'occupiedEntries' => $occupiedEntries,
        ]);
    }
}
