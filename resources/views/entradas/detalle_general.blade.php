{{-- <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle General - Habitación {{ $entry->room->room_number }}</title>
    @php
        use Carbon\Carbon; // Importamos Carbon para evitar el error
    @endphp
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header {
            margin-bottom: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }

        .header-table .logo-cell {
            width: 33.33%;
            text-align: left;
            /* Alineado a la izquierda */
        }

        .header-table .name-cell {
            width: 33.33%;
            text-align: center;
        }

        .header-table .ticket-cell {
            width: 33.33%;
            text-align: right;
        }

        .header img {
            max-width: 100px;
            max-height: 100px;
            /* margin-bottom: 5px; */
        }

        .header h1 {
            font-size: 16px;
            margin: 5px 0;
        }

        .header .ticket-number {
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .info-block {
            margin-bottom: 10px;
        }

        .info-block p {
            margin: 2px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        .table th {
            background-color: #4a6984;
            color: #fff;
            text-align: center;
        }

        .table td {
            vertical-align: top;
        }

        /* Centrar columnas específicas en Detalle Hospedaje */
        .table .center {
            text-align: center;
        }

        /* Alinear a la derecha columnas monetarias en Renovaciones */
        .table .right {
            text-align: right;
        }

        .highlight {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .summary {
            margin-top: 10px;
        }

        .summary .total {
            font-weight: bold;
            text-align: right;
        }

        /* Ajuste para alinear Descuento con Valor Tarifa y TOTAL */
        .summary .total span {
            display: inline-block;
            width: 110px;
            /* Ajustado para alineación */
            text-align: left;
        }

        /* Alineación en el Resumen */
        .summary .total .label {
            width: 100px;
            text-align: left;
        }

        .summary .total .value {
            width: 120px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if ($logoPath)
                            <img src="{{ $logoPath }}" alt="Logo Hotel">
                        @endif
                    </td>
                    <td class="name-cell">
                        <h1>{{ $hotel->nombre }}</h1>
                    </td>
                    <td class="ticket-cell">
                        <p class="ticket-number">Nº: {{ str_pad($numeroTicket, 6, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 1. Detalle Hospedaje -->
        <div class="section-title" style="margin-bottom: 5px;">1. Detalle Hospedaje</div>
        <div class="info-block">
            <p><strong>{{ $hotel->direccion ?? 'JR. LIMA 450 - TALAVERA' }}</strong></p>
            <p><strong>Teléfono:</strong> {{ $hotel->telefono ?? '+51949046173' }}</p>
            <p><strong>Huésped:</strong> {{ $entry->client->name . ' ' . $entry->client->lastname }}</p>
            <p><strong>{{ $entry->client->tipoDocumento->nombre ?? '' }}:</strong> {{ $entry->client->nro_documento }}
            </p>
            <p style="margin-top: 5px;"><strong>{{ $fechaActual }}</strong></p>
        </div>
        <table class="table">
            <tr>
                <th>Habitación</th>
                <th>Tipo</th>
                <th>F. Entrada</th>
                <th>F. Prev Salida</th>
                <th>F. Salida</th>
                <th>E. Pago</th>
            </tr>
            <tr>
                <td class="center">{{ $entry->room->room_number }}</td>
                <td>{{ $entry->roomType->name ?? 'PERSONALIZADO' }}</td>
                <td class="center">{{ $fechaEntrada }}</td>
                <td class="center">{{ $fechaSalidaPrevista }}</td>
                <td class="center">{{ $fechaSalidaReal ?? '-' }}</td>
                <td>{{ $entry->pago }}</td>
            </tr>
        </table>
        <div class="summary">
            <table border="0" cellpadding="5">
                <tr style="font-weight: bold">
                    <td style="width: 528px; text-align: right;">Valor Tarifa:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->total, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 528px; text-align: right;">Descuento:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->discount, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 528px; text-align: right;">TOTAL:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->total - $entry->discount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- 2. Renovaciones -->
        <div class="section-title">2. Renovaciones</div>
        <table class="table">
            <tr>
                <th>N°</th>
                <th>F. Renovación</th>
                <th>F. Entrada</th>
                <th>F. Salida</th>
                <th>Descuento</th>
                <th>Valor</th>
                <th>Sub Total</th>
            </tr>
            @forelse ($entry->renovations as $index => $renovation)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ Carbon::parse($renovation->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="center">{{ Carbon::parse($renovation->check_in)->format('d/m/Y H:i') }}</td>
                    <td class="center">{{ Carbon::parse($renovation->check_out)->format('d/m/Y H:i') }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($renovation->discount, 2) }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($renovation->total, 2) }}</td>
                    <td class="right">
                        {{ $simboloMonetario }}{{ number_format($renovation->total - $renovation->discount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">TOTAL {{ $simboloMonetario }}0.00</td>
                </tr>
            @endforelse
        </table>

        <br>
        <!-- 3. Detalle de Consumo/Servicio -->
        <div class="section-title">3. Detalle de Consumo/Servicio</div>
        <table class="table">
            <tr>
                <th>N°</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Sub Total</th>
            </tr>
            @php
                $totalConsumos = 0;
                $porPagarConsumos = 0;
            @endphp
            @forelse ($consumosYServicios as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['tipo'] }}</td>
                    <td>{{ $item['nombre'] }}</td>
                    <td class="center {{ $item['estado'] == 'Falta Pagar' ? 'highlight' : 'success' }}">
                        {{ $item['estado'] }}
                    </td>
                    <td class="center">{{ $item['cantidad'] }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($item['precio'], 2) }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($item['subtotal'], 2) }}</td>
                </tr>
                @php
                    $totalConsumos += $item['subtotal'];
                    if ($item['estado'] == 'Falta Pagar') {
                        $porPagarConsumos += $item['subtotal'];
                    }
                @endphp
            @empty
                <tr>
                    <td colspan="7">TOTAL {{ $simboloMonetario }}0.00</td>
                </tr>
            @endforelse
        </table>
        <div class="summary">
            <table border="0" cellpadding="5">
                <tr style="font-weight: bold">
                    <td style="width: 528px; text-align: right;">Total:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($totalConsumos, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 528px; text-align: right;">Por Pagar:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($porPagarConsumos, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- 4. Observación -->
        <div class="section-title">4. Observación</div>
        <div class="info-block">
            <p>{{ $entry->observations ?? '' }}</p>
        </div>

        <!-- 5. Resumen -->
        <div class="section-title">5. Resumen</div>
        <table class="table">
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Monto</th>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="summary">
                        <p class="total"><span class="label">Total a Pagar:</span> <span
                                class="value">{{ $simboloMonetario }}{{ number_format($totalAPagar, 2) }}</span></p>
                        <p class="total"><span class="label">Pago Recibido:</span> <span
                                class="value">{{ $simboloMonetario }}{{ number_format($pagoRecibido, 2) }}</span></p>
                        <p class="total"><span class="label">Deuda:</span> <span
                                class="value">{{ $simboloMonetario }}{{ number_format($deuda, 2) }}</span></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html> --}}































<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle General - Habitación {{ $entry->room->room_number }}</title>
    @php
        use Carbon\Carbon; // Importamos Carbon para evitar el error
    @endphp
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header {
            margin-bottom: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }

        .header-table .logo-cell {
            width: 33.33%;
            text-align: left;
        }

        .header-table .name-cell {
            width: 33.33%;
            text-align: center;
        }

        .header-table .ticket-cell {
            width: 33.33%;
            text-align: right;
        }

        .header img {
            max-width: 100px;
            max-height: 100px;
        }

        .header h1 {
            font-size: 16px;
            margin: 5px 0;
        }

        .header .ticket-number {
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .info-block {
            margin-bottom: 10px;
        }

        .info-block p {
            margin: 2px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        .table th {
            background-color: #4a6984;
            color: #fff;
            text-align: center;
        }

        .table td {
            vertical-align: top;
        }

        .table .center {
            text-align: center;
        }

        .table .right {
            text-align: right;
        }

        .highlight {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .summary {
            margin-top: 10px;
        }

        .summary .total {
            font-weight: bold;
            text-align: right;
        }

        .summary .total span {
            display: inline-block;
            width: 110px;
            text-align: left;
        }

        .summary .total .label {
            width: 100px;
            text-align: left;
        }

        .summary .total .value {
            width: 120px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if ($logoPath)
                            <img src="{{ $logoPath }}" alt="Logo Hotel">
                        @endif
                    </td>
                    <td class="name-cell">
                        <h1>{{ $hotel->nombre }}</h1>
                    </td>
                    <td class="ticket-cell">
                        <p class="ticket-number">Nº: {{ str_pad($numeroTicket, 6, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 1. Detalle Hospedaje -->
        <div class="section-title" style="margin-bottom: 5px;">1. Detalle Hospedaje</div>
        <div class="info-block">
            <p><strong>{{ $hotel->direccion ?? 'JR. LIMA 450 - TALAVERA' }}</strong></p>
            <p><strong>Teléfono:</strong> {{ $hotel->telefono ?? '+51949046173' }}</p>
            <p><strong>Huésped:</strong> {{ $entry->client->name . ' ' . $entry->client->lastname }}</p>
            <p><strong>{{ $entry->client->tipoDocumento->nombre ?? '' }}:</strong> {{ $entry->client->nro_documento }}
            </p>
            <p style="margin-top: 5px;"><strong>{{ $fechaActual }}</strong></p>
        </div>
        <table class="table">
            <tr>
                <th>Habitación</th>
                <th>Tipo</th>
                <th>F. Entrada</th>
                <th>F. Prev Salida</th>
                <th>F. Salida</th>
                <th>E. Pago</th>
            </tr>
            <tr>
                <td class="center">{{ $entry->room->room_number }}</td>
                <td>{{ $entry->roomType->name ?? 'PERSONALIZADO' }}</td>
                <td class="center">{{ $fechaEntrada }}</td>
                <td class="center">{{ $fechaSalidaPrevista }}</td>
                <td class="center">{{ $fechaSalidaReal ?? '-' }}</td>
                <td>{{ $entry->pago }}</td>
            </tr>
        </table>
        <div class="summary">
            <table border="0" cellpadding="2">
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Valor Tarifa:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->total, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Descuento:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->discount, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">TOTAL:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($entry->total - $entry->discount, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Total Pagado:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($pagoRecibidoAlquiler, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Por Pagar:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($porPagarAlquiler, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- 2. Renovaciones -->
        <div class="section-title">2. Renovaciones</div>
        <table class="table">
            <tr>
                <th>N°</th>
                <th>F. Renovación</th>
                <th>F. Entrada</th>
                <th>F. Salida</th>
                <th>Descuento</th>
                <th>Valor</th>
                <th>Sub Total</th>
            </tr>
            @forelse ($entry->renovations as $index => $renovation)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ Carbon::parse($renovation->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="center">{{ Carbon::parse($renovation->check_in)->format('d/m/Y H:i') }}</td>
                    <td class="center">{{ Carbon::parse($renovation->check_out)->format('d/m/Y H:i') }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($renovation->discount, 2) }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($renovation->total, 2) }}</td>
                    <td class="right">
                        {{ $simboloMonetario }}{{ number_format($renovation->total - $renovation->discount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">TOTAL {{ $simboloMonetario }}0.00</td>
                </tr>
            @endforelse
        </table>
        <div class="summary">
            <table border="0" cellpadding="2">
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Total Renovaciones:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($totalRenovaciones, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Total Pagado:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($pagoRecibidoRenovaciones, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Por Pagar:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($porPagarRenovaciones, 2) }}</td>
                </tr>
            </table>
        </div>

        <br>
        <!-- 3. Detalle de Consumo/Servicio -->
        <div class="section-title">3. Detalle de Consumo/Servicio</div>
        <table class="table">
            <tr>
                <th>N°</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Sub Total</th>
            </tr>
            @php
                $totalConsumos = 0;
                $porPagarConsumos = 0;
            @endphp
            @forelse ($consumosYServicios as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['tipo'] }}</td>
                    <td>{{ $item['nombre'] }}</td>
                    <td class="center {{ $item['estado'] == 'Falta Pagar' ? 'highlight' : 'success' }}">
                        {{ $item['estado'] }}
                    </td>
                    <td class="center">{{ $item['cantidad'] }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($item['precio'], 2) }}</td>
                    <td class="right">{{ $simboloMonetario }}{{ number_format($item['subtotal'], 2) }}</td>
                </tr>
                @php
                    $totalConsumos += $item['subtotal'];
                    if ($item['estado'] == 'Falta Pagar') {
                        $porPagarConsumos += $item['subtotal'];
                    }
                @endphp
            @empty
                <tr>
                    <td colspan="7">TOTAL {{ $simboloMonetario }}0.00</td>
                </tr>
            @endforelse
        </table>
        <div class="summary">
            <table border="0" cellpadding="2">
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Total:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($totalConsumos, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Por Pagar:</td>
                    <td style="width: 150px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($porPagarConsumos, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- 4. Observación -->
        <div class="section-title">4. Observación</div>
        <div class="info-block">
            <p>{{ $entry->observations ?? '' }}</p>
        </div>

        <!-- 5. Resumen -->
        <div class="section-title">5. Resumen</div>
        <div class="summary" style="border: 1px solid #000;">
            <table border="0" cellpadding="2" style="margin: 10px; 0">
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Total a Pagar:</td>
                    <td style="width: 130px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($totalAPagar, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Pago Recibido:</td>
                    <td style="width: 130px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($pagoRecibido, 2) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td style="width: 540px; text-align: right;">Deuda:</td>
                    <td style="width: 130px; text-align: right;">{{ $simboloMonetario }}
                        {{ number_format($deuda, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
