<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Entrada</title>
    <style>
        * {
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-family: 'Courier', monospace;
            font-size: 10px;
            line-height: 1.2;
            margin: 0 !important;
            padding: 0 !important;
            color: #000;
            background-color: #fff;
            width: 95%;
            /* Asegurarnos de que ocupe todo el ancho */
            box-sizing: border-box;
        }

        .text-center {
            text-align: center;
            margin: 10px !important;
            padding: 10px !important;
        }

        .text-left {
            text-align: left;
            margin: 8px !important;
            padding: 8px !important;
        }

        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
            margin-top: 0 !important;
        }

        .total {
            border-top: 1px dashed #000;
            padding-top: 3px;
            margin-top: 3px;
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 3px 10px !important;
        }

        .signature-space {
            height: 40px;
        }

        .pre-signature-space {
            height: 60px;
        }

        .bold {
            font-weight: bold;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin: 0 auto;
            /* Centra horizontalmente */
            display: block;
        }

        div {
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
</head>

<body>
    <!-- Contenedor principal -->
    <div style="width: 100%; max-width: 60mm; margin: 0; padding: 0;">

        <!-- Logo del hotel -->
        @if ($logoPath)
            <div style="text-align: center; width: 100%;">
                <img src="{{ $logoPath }}" alt="Logo del Hotel" class="logo">
            </div>
        @endif

        <div class="header text-center">
            <div>{{ strtoupper($hotel->nombre ?? 'HOTEL') }}</div>
            <div>CUIT Nro.: {{ $hotel->cuit ?? '0270738012' }}</div>
            <div>JR. LIMA 450 - TALAVERA</div>
            <div>Teléfono: {{ $hotel->telefono ?? '+5194804173' }}</div>
            <div>{{ $fechaActual }}</div>
        </div>

        <div class="text-left">
            <div>Noches: 1 de 1</div>
            <div><span class="bold">{{ $entry->client->tipoDocumento->nombre ?? 'DNI' }}</span>:
                {{ $entry->client->nro_documento ?? 'N/A' }}</div>
            <div><span class="bold">Huésped</span>: {{ $entry->client->name . ' ' . $entry->client->lastname }}</div>
            <div><span class="bold">Hab. Nro.</span>: {{ $entry->room->room_number ?? 'N/A' }}</div>
            <div><span class="bold">Tipo</span>: {{ $entry->roomType->name ?? 'PERSONALIZADO' }}</div>
            <div><span class="bold">F. Entrada</span>: {{ $fechaEntrada }}</div>
            <div><span class="bold">F. Salida</span>: {{ $fechaSalida }}</div>
            <div><span class="bold">Método de Pago:</span></div>
            <div style="white-space: pre;">{{ $metodosPagoTexto }}</div>
        </div>

        <div class="line"></div>

        <div class="text-left">
            <div><span class="bold">Habitación</span>:
                {{ $simboloMonetario }}{{ number_format($entry->total, 2, '.', '') }}</div>
            <div><span class="bold">Descuento</span>: -
                {{ $simboloMonetario }}{{ number_format($entry->discount, 2, '.', '') }}</div>
        </div>

        <div class="total text-left">
            <div>Total a Pagar:
                {{ $simboloMonetario }}{{ number_format($entry->total - $entry->discount, 2, '.', '') }}
            </div>
            <div>Pago Recibido: {{ $simboloMonetario }}{{ number_format($pagoRecibido, 2, '.', '') }}</div>
            @if ($entry->debt > 0)
                <div>Deuda: {{ $simboloMonetario }}{{ number_format($entry->debt, 2, '.', '') }}</div>
            @endif
        </div>

        <div class="text-center">
            <div class="pre-signature-space"></div>
            <div>--------------------------</div>
            <div class="bold">Huésped:</div>
            <div class="signature-space"></div>
            <div>--------------------------</div>
            <div class="bold">{{ strtoupper($user->roles->first()->name ?? 'Recepcionista') }}:
                {{ strtoupper($user->name ?? 'Wilfredo Vargas Cardenas') }}</div>
        </div>
    </div>
</body>

</html>
