<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        $reservations = Reservation::all();
        $clients = Client::all();
        return view('facturas.index', compact('invoices', 'reservations', 'clients'));
    }
}
