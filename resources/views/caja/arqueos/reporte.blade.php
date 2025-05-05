<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Arqueos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-warning {
            color: #ffc107;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>
    <h1>Reporte de Arqueos</h1>
    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>Fecha Apertura</th>
                <th>Monto Inicial</th>
                <th>Fecha Cierre</th>
                <th>Monto Final</th>
                <th>Efectivo</th>
                <th>Tarjetas</th>
                <th>Mercado Pago</th>
                <th>Descripción</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Diferencia</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($arqueos as $arqueo)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($arqueo->fecha_apertura)->format('d/m/Y H:i') }}</td>
                    <td class="text-right">$ {{ number_format($arqueo->monto_inicial, 2, ',', '.') }}</td>
                    <td>{{ $arqueo->fecha_cierre ? \Carbon\Carbon::parse($arqueo->fecha_cierre)->format('d/m/Y H:i') : '' }}
                    </td>
                    <td class="text-right">
                        {{ $arqueo->monto_final ? '$ ' . number_format($arqueo->monto_final, 2, ',', '.') : '' }}</td>
                    <td class="text-right">
                        {{ $arqueo->ventas_efectivo ? '$ ' . number_format($arqueo->ventas_efectivo, 2, ',', '.') : '' }}
                    </td>
                    <td class="text-right">
                        {{ $arqueo->ventas_tarjeta ? '$ ' . number_format($arqueo->ventas_tarjeta, 2, ',', '.') : '' }}
                    </td>
                    <td class="text-right">
                        {{ $arqueo->ventas_mercadopago ? '$ ' . number_format($arqueo->ventas_mercadopago, 2, ',', '.') : '' }}
                    </td>
                    <td class="text-left">{{ $arqueo->descripcion }}</td>
                    <td class="text-right text-success">$ {{ number_format($arqueo->total_ingresos, 2, ',', '.') }}</td>
                    <td class="text-right text-danger">$ {{ number_format($arqueo->total_egresos, 2, ',', '.') }}</td>
                    <td class="text-right text-warning">
                        <?php $diferencia = $arqueo->monto_inicial + $arqueo->total_ingresos - $arqueo->total_egresos - ($arqueo->monto_final ?? 0); ?>
                        $ {{ number_format($diferencia, 2, ',', '.') }}
                    </td>
                    <td>{{ $arqueo->usuario->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | {{ config('footer.company_name') }} &copy;
            {{ date('Y') }}</p>
    </div>
</body>

</html>
