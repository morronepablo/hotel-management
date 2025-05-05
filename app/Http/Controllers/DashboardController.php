<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Entry;
use App\Models\Renewal;
use App\Models\ConsumoDetalle;
use App\Models\ServicioConsumoDetalle;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas para los cards
        $roomsRequiringAttention = Room::whereIn('status', [
            'Para la Limpieza',
            'En Limpieza',
            'Limpieza Profunda',
            'Limpieza Rápida'
        ])->count();

        $availableRooms = Room::where('status', 'Disponible')->count();

        $confirmedReservations = Reservation::where('status', 'Confirmada')->count();

        $occupiedRooms = Room::where('status', 'Ocupada')->count();

        // Datos para el gráfico anual (por mes)
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $monthsInYear = 12;
        $mes_actual = ucfirst(Carbon::now()->locale('es')->monthName);
        $mes_actual_numero = Carbon::now()->month;

        // Alquileres (entradas) - Sumar montos de efectivo, mercadopago, tarjeta, transferencia
        $monthlyAlquileres = Entry::whereYear('created_at', $currentYear)
            ->where('pago', 'Pagado')
            ->selectRaw('MONTH(created_at) as month, SUM(efectivo + mercadopago + tarjeta + transferencia) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Renovaciones - Sumar montos de efectivo, mercadopago, tarjeta, transferencia
        $monthlyRenovaciones = Renewal::whereYear('created_at', $currentYear)
            ->where('pago', 'Pagado')
            ->selectRaw('MONTH(created_at) as month, SUM(efectivo + mercadopago + tarjeta + transferencia) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Consumos (Productos) - Sumar subtotal
        $monthlyConsumos = ConsumoDetalle::whereYear('created_at', $currentYear)
            ->where('estado', 'Pagado')
            ->selectRaw('MONTH(created_at) as month, SUM(subtotal) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Servicios - Sumar subtotal
        $monthlyServicios = ServicioConsumoDetalle::whereYear('created_at', $currentYear)
            ->where('estado', 'Pagado')
            ->selectRaw('MONTH(created_at) as month, SUM(subtotal) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Preparar arrays para los datos por mes
        $alquileresByMonth = array_fill(1, $monthsInYear, 0);
        $renovacionesByMonth = array_fill(1, $monthsInYear, 0);
        $consumosByMonth = array_fill(1, $monthsInYear, 0);
        $serviciosByMonth = array_fill(1, $monthsInYear, 0);

        foreach ($monthlyAlquileres as $month => $total) {
            $alquileresByMonth[$month] = floatval($total);
        }
        foreach ($monthlyRenovaciones as $month => $total) {
            $renovacionesByMonth[$month] = floatval($total);
        }
        foreach ($monthlyConsumos as $month => $total) {
            $consumosByMonth[$month] = floatval($total);
        }
        foreach ($monthlyServicios as $month => $total) {
            $serviciosByMonth[$month] = floatval($total);
        }

        // Productos más Vendidos del mes actual (por defecto)
        $topProductos = ConsumoDetalle::select('producto_id')
            ->selectRaw('SUM(subtotal) as total_vendido')
            ->where('estado', 'Pagado')
            ->whereHas('producto')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->with('producto')
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->producto ? $item->producto->producto : "Producto Desconocido",
                    'total_vendido' => $item->total_vendido
                ];
            });

        // Log para depuración
        Log::info('Datos iniciales para el mes actual (Abril)', [
            'labels' => $topProductos->pluck('nombre')->toArray(),
            'data' => $topProductos->pluck('total_vendido')->toArray(),
        ]);

        return view('dashboard', compact(
            'roomsRequiringAttention',
            'availableRooms',
            'confirmedReservations',
            'occupiedRooms',
            'alquileresByMonth',
            'renovacionesByMonth',
            'consumosByMonth',
            'serviciosByMonth',
            'monthsInYear',
            'mes_actual',
            'mes_actual_numero',
            'topProductos'
        ));
    }

    public function getDataByMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = Carbon::now()->year;

        // Productos más Vendidos para el mes seleccionado
        $topProductos = ConsumoDetalle::select('producto_id')
            ->selectRaw('SUM(subtotal) as total_vendido')
            ->where('estado', 'Pagado')
            ->whereHas('producto')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->with('producto')
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->producto ? $item->producto->producto : "Producto Desconocido",
                    'total_vendido' => $item->total_vendido
                ];
            });

        $labels = $topProductos->pluck('nombre')->toArray();
        $data = $topProductos->pluck('total_vendido')->toArray();

        // Log para depuración
        Log::info('Datos devueltos para el mes ' . $month, [
            'labels' => $labels,
            'data' => $data,
        ]);

        // Evitar caché en la respuesta
        return response()->json([
            'top_productos_labels' => $labels ?: ['Sin datos'],
            'top_productos_data' => $data ?: [0],
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
