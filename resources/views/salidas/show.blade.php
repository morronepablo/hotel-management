@extends('adminlte::page')

@section('title', 'Formulario de Salida')

@section('content_header')
    <h1><b>Estado de Habitación N°: {{ $entry->room->room_number }} - {{ $entry->roomType->name ?? 'PERSONALIZADO' }} |
            Cliente: {{ $entry->client->dni ?? $entry->client->nro_documento }} - {{ $entry->client->name }}
            {{ $entry->client->lastname }}</b></h1>
    <hr>
    <br>
@endsection

@section('content')
    <div class="row">
        <!-- Columna Principal -->
        <div class="col-md-12">
            <!-- Información General -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Información General</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Tarifa:</strong> {{ $entry->roomType->name ?? 'PERSONALIZADO' }} | {{ $tariffName }}
                        </div>
                        <div class="col-md-4">
                            <strong>F. Entrada:</strong> {{ \Carbon\Carbon::parse($entry->check_in)->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-4">
                            <strong>F. Prevista de Salida:</strong>
                            {{ \Carbon\Carbon::parse($entry->check_out)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Alquiler -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Detalles del Alquiler</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ALQUILER</th>
                                <th>RENOVACIÓN</th>
                                <th>TOTAL</th>
                                <th>PAGO RECIBIDO</th>
                                <th>DEUDA</th>
                                <th>MORA/PENALIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($entry->total, 2) }}</td>
                                <td>{{ number_format($entry->renovations->sum('total'), 2) }}</td>
                                <td>{{ number_format($entry->totalAlquiler, 2) }}</td>
                                <td>{{ number_format($entry->pagadoAlquiler, 2) }}</td>
                                <td>{{ number_format($entry->deudaAlquiler, 2) }}</td>
                                <td>
                                    <input type="number" name="mora_penalidad" id="mora_penalidad" class="form-control"
                                        value="0.00" step="0.01" min="0">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detalles de Consumo/Servicio -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Detalles de Consumo / Servicio</h3>
                </div>
                <div class="card-body">
                    <table id="consumos-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">TIPO</th>
                                <th class="text-center">NOMBRE</th>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">PRECIO U.</th>
                                <th class="text-center">ESTADO</th>
                                <th class="text-center">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody id="consumos-body">
                            <!-- Consumos -->
                            @php $hasConsumos = $entry->consumo && $entry->consumo->detalles && count($entry->consumo->detalles) > 0; @endphp
                            @if ($hasConsumos)
                                @foreach ($entry->consumo->detalles as $key => $detalle)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">Consumo</td>
                                        <td>
                                            @if ($detalle->producto)
                                                {{ $detalle->producto->producto }}
                                            @else
                                                Producto no encontrado (ID: {{ $detalle->producto_id ?? 'N/A' }})
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $detalle->cantidad ?? 'N/A' }}</td>
                                        <td class="text-right">{{ number_format($detalle->precio ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $detalle->estado == 'Pagado' ? 'badge-pagado' : 'badge-falta-pagar' }}"
                                                data-id="{{ $detalle->id }}" data-tipo="Consumo">
                                                {{ $detalle->estado ?? 'Desconocido' }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($detalle->subtotal ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No se encontraron detalles de consumos (consumo:
                                        {{ $entry->consumo ? 'Existe' : 'No existe' }})</td>
                                </tr>
                            @endif

                            <!-- Servicios -->
                            @php $hasServicios = $entry->servicioConsumo && $entry->servicioConsumo->detalles && count($entry->servicioConsumo->detalles) > 0; @endphp
                            @if ($hasServicios)
                                @foreach ($entry->servicioConsumo->detalles as $key => $detalle)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">Servicio</td>
                                        <td>
                                            @if ($detalle->servicio)
                                                {{ $detalle->servicio->nombre }}
                                            @else
                                                Servicio no encontrado (ID: {{ $detalle->servicio_id ?? 'N/A' }})
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $detalle->cantidad ?? 'N/A' }}</td>
                                        <td class="text-right">{{ number_format($detalle->precio_unitario ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $detalle->estado == 'Pagado' ? 'badge-pagado' : 'badge-falta-pagar' }}"
                                                data-id="{{ $detalle->id }}" data-tipo="Servicio">
                                                {{ $detalle->estado ?? 'Desconocido' }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($detalle->subtotal ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Deuda:</th>
                                <th id="deuda-consumos-servicios" class="text-right">$
                                    {{ number_format($deudaConsumosServicios, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Totales -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Totales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>TOTAL:</strong> {{ number_format($entry->totalAPagar, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Alquiler/Reserva Pagados:</strong> {{ number_format($entry->pagadoAlquiler, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Alquiler/Renovación Deuda:</strong> {{ number_format($entry->deudaAlquiler, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Consumos/Servicios Pagados:</strong>
                            {{ number_format($entry->totalPagadoAdicionales, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Consumos/Servicios Deuda:</strong>
                            <span id="deuda-consumos-total">{{ number_format($deudaConsumosServicios, 2) }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>TOTAL A PAGAR:</strong>
                            <span id="total_a_pagar">{{ number_format($entry->saldoDeudor, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario para registrar pagos -->
            <form id="checkout-form" action="{{ route('salidas.checkout', $entry) }}" method="POST">
                @csrf
                <input type="hidden" name="mora_penalidad" id="mora_penalidad_hidden" value="0.00">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title my-1">Pagar Deuda Pendiente</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Total a Pagar:</strong> <span
                                    id="total_a_pagar_form">{{ number_format($entry->saldoDeudor, 2) }}</span>
                                <input type="hidden" name="total_a_pagar" id="total_a_pagar_hidden"
                                    value="{{ $entry->saldoDeudor }}">
                            </div>
                            <div class="col-md-3">
                                <label for="metodo_pago">Método Pago:</label>
                                <select name="metodo_pago" id="metodo_pago" class="form-control">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="mercadopago">MercadoPago</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monto">Monto:</label>
                                <input type="number" name="monto" id="monto" class="form-control"
                                    value="{{ $entry->saldoDeudor }}" step="0.01" min="0">
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger">Volver</a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">Culminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }

        .card-title {
            font-weight: bold;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge-falta-pagar {
            background-color: #dc3545;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .badge-pagado {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
        }

        #consumos-table td:nth-child(6) {
            text-align: center;
            vertical-align: middle;
        }

        #consumos-table td:nth-child(5),
        #consumos-table td:nth-child(7) {
            text-align: right;
            vertical-align: middle;
        }

        #consumos-table tfoot th {
            font-weight: bold;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar el DataTable sin depender de la cantidad de filas
            let consumosTable = $('#consumos-table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                }
            });

            let baseDeuda = parseFloat({{ $entry->saldoDeudor }}) || 0;
            console.log('Deuda inicial (baseDeuda):', baseDeuda);

            let moraPenalidad = parseFloat(document.getElementById('mora_penalidad').value) || 0;

            let deudaConsumosServicios = parseFloat({{ $deudaConsumosServicios }}) || 0;
            console.log('Deuda inicial Consumos/Servicios:', deudaConsumosServicios);

            function formatCurrency(value) {
                value = parseFloat(value) || 0;
                return '$ ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function actualizarTotalAPagar() {
                moraPenalidad = parseFloat(document.getElementById('mora_penalidad').value) || 0;
                console.log('Mora/Penalidad ingresada:', moraPenalidad);
                console.log('Base Deuda antes de sumar mora:', baseDeuda);

                const deudaTotal = baseDeuda + moraPenalidad;
                console.log('Deuda Total calculada:', deudaTotal);

                document.getElementById('total_a_pagar').innerText = formatCurrency(deudaTotal);
                document.getElementById('total_a_pagar_form').innerText = formatCurrency(deudaTotal);

                document.getElementById('total_a_pagar_hidden').value = deudaTotal.toFixed(2);
                document.getElementById('mora_penalidad_hidden').value = moraPenalidad.toFixed(2);

                document.getElementById('monto').value = deudaTotal.toFixed(2);
            }

            $('#mora_penalidad').on('input', function() {
                actualizarTotalAPagar();
            });

            function actualizarTotales() {
                $.ajax({
                    url: '{{ route('salidas.getDetails', $entry->id) }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Respuesta de getDetails:', response);
                        baseDeuda = parseFloat(response.saldoDeudor) || 0;
                        console.log('Nueva baseDeuda después de pagar:', baseDeuda);
                        deudaConsumosServicios = parseFloat(response.deudaConsumosServicios) || 0;
                        console.log('Nueva deuda Consumos/Servicios:', deudaConsumosServicios);
                        const deudaTotal = baseDeuda + moraPenalidad;
                        console.log('Deuda Total después de actualizar:', deudaTotal);
                        $('#deuda-consumos-total').text(formatCurrency(deudaConsumosServicios));
                        $('#deuda-consumos-servicios').text(formatCurrency(deudaConsumosServicios));
                        $('#total_a_pagar').text(formatCurrency(deudaTotal));
                        $('#total_a_pagar_form').text(formatCurrency(deudaTotal));
                        $('#total_a_pagar_hidden').val(deudaTotal.toFixed(2));
                        $('#monto').val(deudaTotal.toFixed(2));

                        // Actualizar la tabla de consumos/servicios
                        consumosTable.clear().draw();
                        let items = response.items;
                        if (items.length === 0) {
                            consumosTable.row.add([
                                '', '', 'No hay consumos ni servicios registrados', '', '', '',
                                ''
                            ]).draw();
                        } else {
                            items.forEach((item, index) => {
                                let badgeClass = (item.estado === 'Falta Pagar' || item
                                        .estado === 'Pendiente') ?
                                    'badge-falta-pagar' : 'badge-pagado';
                                let row = [
                                    index + 1,
                                    item.tipo || 'N/A',
                                    item.nombre || 'N/A',
                                    item.cantidad || 0,
                                    formatCurrency(item.precio || 0),
                                    `<span class="badge ${badgeClass}" data-id="${item.id}" data-tipo="${item.tipo}">${item.estado}</span>`,
                                    formatCurrency(item.subtotal || 0)
                                ];
                                consumosTable.row.add(row).draw();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al actualizar totales:', error);
                        Swal.fire('Error', 'No se pudieron actualizar los totales.', 'error');
                    }
                });
            }

            $(document).on('click', '.badge-falta-pagar', function() {
                let id = $(this).data('id');
                let tipo = $(this).data('tipo');

                Swal.fire({
                    title: '¿Está seguro en pagar este producto?',
                    text: '¡No podrás revertir esto!',
                    icon: 'warning',
                    html: `
                        <select id="metodo-pago-swal" class="form-control">
                            <option value="">Seleccionar Método de Pago</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="mercadopago">MercadoPago</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, continuar!',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        let metodoPago = $('#metodo-pago-swal').val();
                        if (!metodoPago) {
                            Swal.showValidationMessage(
                                'Por favor, seleccione un método de pago');
                        }
                        return {
                            metodoPago: metodoPago
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('salidas.pagarConsumoServicio') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id,
                                tipo: tipo,
                                metodo_pago: result.value.metodoPago
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Pagado!',
                                        text: 'El producto ha sido pagado.',
                                        showConfirmButton: false,
                                        timer: 2500,
                                        timerProgressBar: true
                                    }).then(() => {
                                        actualizarTotales();
                                    });
                                } else {
                                    Swal.fire('Error', 'No se pudo procesar el pago.',
                                        'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al procesar el pago:', error);
                                Swal.fire('Error', 'No se pudo procesar el pago.',
                                    'error');
                            }
                        });
                    }
                });
            });

            $('#checkout-form').on('submit', function(e) {
                let totalAPagar = parseFloat($('#total_a_pagar_hidden').val()) || 0;
                let monto = parseFloat($('#monto').val()) || 0;

                if (totalAPagar > 0 && monto < totalAPagar) {
                    e.preventDefault();
                    Swal.fire('Error',
                        'El monto ingresado es insuficiente para cubrir la deuda pendiente y la mora/penalidad.',
                        'error');
                }
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif

            actualizarTotalAPagar();
        });
    </script>
@endsection
