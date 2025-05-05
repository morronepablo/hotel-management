@extends('adminlte::page')

@section('content_header')
    <h1><b>Añadir Pago</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row">
        <!-- Columna Izquierda -->
        <div class="col-md-8">
            <!-- Selección de Habitación -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Habitación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <select id="room_select" name="room_id" class="form-control select2" required>
                            <option value="">Seleccionar</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    data-client="{{ $room->client_name }} {{ $room->client_lastname }}"
                                    data-room-number="{{ $room->room_number }}" data-debt="{{ $room->debt }}"
                                    {{ isset($selectedRoomId) && $selectedRoomId == $room->id ? 'selected' : '' }}>
                                    Habitación: {{ $room->room_number }} - {{ $room->client_name }}
                                    {{ $room->client_lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card de Consumos/Servicios -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Consumo / Servicio</h3>
                </div>
                <div class="card-body">
                    <table id="consumos-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Precio</th>
                                <th class="text-center">Estado</th>
                                <th class="text-right">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody id="consumos-body">
                            <!-- Los datos se llenarán con JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Deuda:</th>
                                <th id="deuda-consumos-servicios" class="text-right">$ 0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div class="col-md-4">
            <!-- Deuda Total -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Deuda Total</h3>
                </div>
                <div class="card-body">
                    <h4 id="deuda-total">0.00</h4>
                </div>
            </div>

            <!-- Card de Alquiler -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Alquiler</h3>
                </div>
                <div class="card-body">
                    <form id="alquiler-form" action="{{ route('caja.pagos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="room_id" id="alquiler-room-id">
                        <input type="hidden" name="entry_id" id="alquiler-entry-id">
                        <input type="hidden" id="deuda-alquiler-value" value="0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Costo Alquiler:</label>
                                    <input type="text" class="form-control" id="costo-alquiler" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descuento Alquiler:</label>
                                    <input type="text" class="form-control" id="descuento-alquiler" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pagado Alquiler:</label>
                                    <input type="text" class="form-control" id="pagado-alquiler" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Deuda Alquiler:</label>
                                    <input type="text" class="form-control" id="deuda-alquiler" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Costo Renovación:</label>
                                    <input type="text" class="form-control" id="costo-renovacion" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descuento Renovación:</label>
                                    <input type="text" class="form-control" id="descuento-renovacion" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pagado Renovación:</label>
                                    <input type="text" class="form-control" id="pagado-renovacion" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Deuda Renovación:</label>
                                    <input type="text" class="form-control" id="deuda-renovacion" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Pago</label>
                            <select class="form-control" name="payment_type" id="payment-type-select" required>
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Método</label>
                            <select class="form-control" name="metodo_pago" required>
                                <option value="">Seleccionar</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="mercadopago">MercadoPago</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Importe</label>
                            <input type="number" class="form-control" name="importe" step="0.01" min="0"
                                required>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('caja.pagos') }}" class="btn btn-secondary">Volver</a>
                            </div>
                            <div class="col-md-6 text-right" id="registrar-button-container">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Versión {{ config('footer.version') }}
        </div>
        <strong>Copyright © {{ date('Y') }} <a
                href="{{ config('footer.company_url') }}">{{ config('footer.company_name') }}</a>.</strong> Todos los
        derechos reservados.
    </footer>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .select2-container .select2-selection--single {
            height: auto !important;
            padding: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            white-space: normal !important;
            overflow: visible !important;
            padding-right: 30px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .badge-falta-pagar {
            background-color: #dc3545;
            color: white;
            cursor: pointer;
        }

        .badge-pagado {
            background-color: #28a745;
            color: white;
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
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "timeOut": "5000",
            };

            function formatCurrency(value) {
                value = parseFloat(value) || 0;
                return '$ ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            $('#room_select').select2({
                placeholder: 'Seleccionar habitación',
                allowClear: true,
                minimumResultsForSearch: Infinity,
                width: '100%',
                templateResult: function(data) {
                    if (!data.element) {
                        return data.text;
                    }
                    let $element = $(data.element);
                    let roomNumber = $element.data('room-number') || 'N/A';
                    let client = $element.data('client') || 'N/A';
                    return 'Habitación: ' + roomNumber + ' - ' + client;
                },
                templateSelection: function(data) {
                    if (!data.element) {
                        return data.text;
                    }
                    let $element = $(data.element);
                    let roomNumber = $element.data('room-number') || 'N/A';
                    let client = $element.data('client') || 'N/A';
                    return 'Habitación: ' + roomNumber + ' - ' + client;
                }
            }).on('select2:select', function(e) {
                $(this).trigger('change');
            });

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

            $('#room_select').on('change', function() {
                console.log('Evento change disparado para room_select');
                let roomId = $(this).val();
                let debt = $(this).find('option:selected').data('debt');
                let clientName = $(this).find('option:selected').data('client');

                $('#deuda-total').text(formatCurrency(debt));

                if (roomId) {
                    console.log('Enviando solicitud AJAX para room_id:', roomId);
                    $.ajax({
                        url: '{{ route('caja.pagos.getDetails') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            room_id: roomId
                        },
                        success: function(response) {
                            console.log('Respuesta AJAX:', response);

                            if (!response.entry) {
                                $('#deuda-total').text('0.00');
                                $('#costo-alquiler').val('');
                                $('#descuento-alquiler').val('');
                                $('#pagado-alquiler').val('');
                                $('#deuda-alquiler').val('');
                                $('#costo-renovacion').val('');
                                $('#descuento-renovacion').val('');
                                $('#pagado-renovacion').val('');
                                $('#deuda-renovacion').val('');
                                $('#deuda-alquiler-value').val(0);
                                $('#deuda-consumos-servicios').text(formatCurrency(0));
                                consumosTable.clear().draw();
                                consumosTable.row.add([
                                    '', '', 'No se encontró una entrada activa', '', '',
                                    '', ''
                                ]).draw();

                                // Limpiar y resetear el select de tipo de pago
                                $('#payment-type-select').html(
                                    '<option value="">Seleccionar</option>');
                                // Habilitar el formulario
                                $('#alquiler-form select, #alquiler-form input[name="importe"]')
                                    .prop('disabled', false);
                                $('#registrar-button-container').show();
                                return;
                            }

                            // Actualizar datos de Alquiler
                            $('#alquiler-room-id').val(roomId);
                            $('#alquiler-entry-id').val(response.entry.id);
                            $('#costo-alquiler').val(formatCurrency(response.entry.total));
                            $('#descuento-alquiler').val(formatCurrency(response.entry
                                .discount));
                            $('#pagado-alquiler').val(formatCurrency(response.entry
                                .payment_received_entry));
                            $('#deuda-alquiler').val(formatCurrency(response.entry.debt_entry));
                            $('#costo-renovacion').val(formatCurrency(response.entry
                                .renovation_total));
                            $('#descuento-renovacion').val(formatCurrency(response.entry
                                .renovation_discount));
                            $('#pagado-renovacion').val(formatCurrency(response.entry
                                .payment_received_renovation));
                            $('#deuda-renovacion').val(formatCurrency(response.entry
                                .debt_renovation));
                            $('#deuda-alquiler-value').val(response.entry.total_debt);

                            // Actualizar el select de tipo de pago dinámicamente
                            let paymentTypeSelect = $('#payment-type-select');
                            paymentTypeSelect.html('<option value="">Seleccionar</option>');
                            let debtEntry = parseFloat(response.entry.debt_entry) || 0;
                            let debtRenovation = parseFloat(response.entry.debt_renovation) ||
                                0;

                            // Mostrar opciones solo si tienen deuda
                            if (debtEntry > 0) {
                                paymentTypeSelect.append(
                                    '<option value="alquiler">Alquiler</option>');
                            }
                            if (debtRenovation > 0 && response.entry.has_renovations) {
                                paymentTypeSelect.append(
                                    '<option value="renovacion">Renovación</option>');
                            }

                            // Si no hay opciones disponibles (ambas deudas son 0), deshabilitar el formulario
                            if (debtEntry == 0 && debtRenovation == 0) {
                                $('#alquiler-form select, #alquiler-form input[name="importe"]')
                                    .prop('disabled', true);
                                $('#registrar-button-container').hide();
                            } else {
                                $('#alquiler-form select, #alquiler-form input[name="importe"]')
                                    .prop('disabled', false);
                                $('#registrar-button-container').show();
                            }

                            // Calcular la deuda de consumos y servicios
                            let consumos = response.consumos || [];
                            let servicios = response.servicios || [];
                            let items = consumos.concat(servicios);
                            let consumosDebt = 0;

                            items.forEach(item => {
                                if (item.estado === 'Falta Pagar') {
                                    consumosDebt += parseFloat(item.subtotal) || 0;
                                }
                            });

                            $('#deuda-consumos-servicios').text(formatCurrency(consumosDebt));

                            let alquilerDebt = parseFloat(response.entry.total_debt) || 0;
                            let totalDebt = alquilerDebt + consumosDebt;
                            $('#deuda-total').text(formatCurrency(totalDebt));

                            consumosTable.clear().draw();
                            console.log('Consumos:', consumos);
                            console.log('Servicios:', servicios);
                            console.log('Items combinados:', items);

                            if (items.length === 0) {
                                consumosTable.row.add([
                                    '', '', 'No hay consumos ni servicios registrados',
                                    '', '', '', ''
                                ]).draw();
                            } else {
                                items.forEach((item, index) => {
                                    console.log('Procesando item:', item);
                                    let badgeClass = item.estado === 'Falta Pagar' ?
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
                            console.error('Error en la solicitud AJAX:', error);
                            console.error('Estado:', status);
                            console.error('Respuesta:', xhr.responseText);
                            toastr.error('No se pudieron cargar los datos de la habitación.');
                            $('#deuda-consumos-servicios').text(formatCurrency(0));
                            consumosTable.clear().draw();
                            consumosTable.row.add([
                                '', '', 'Error al cargar los datos', '', '', '', ''
                            ]).draw();

                            // Limpiar y resetear el select de tipo de pago
                            $('#payment-type-select').html(
                                '<option value="">Seleccionar</option>');
                            // Habilitar el formulario
                            $('#alquiler-form select, #alquiler-form input[name="importe"]')
                                .prop('disabled', false);
                            $('#registrar-button-container').show();
                        }
                    });
                } else {
                    $('#deuda-total').text('0.00');
                    $('#costo-alquiler').val('');
                    $('#descuento-alquiler').val('');
                    $('#pagado-alquiler').val('');
                    $('#deuda-alquiler').val('');
                    $('#costo-renovacion').val('');
                    $('#descuento-renovacion').val('');
                    $('#pagado-renovacion').val('');
                    $('#deuda-renovacion').val('');
                    $('#deuda-alquiler-value').val(0);
                    $('#deuda-consumos-servicios').text(formatCurrency(0));
                    consumosTable.clear().draw();

                    // Limpiar y resetear el select de tipo de pago
                    $('#payment-type-select').html('<option value="">Seleccionar</option>');
                    // Habilitar el formulario
                    $('#alquiler-form select, #alquiler-form input[name="importe"]').prop('disabled',
                        false);
                    $('#registrar-button-container').show();
                }
            });

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
                            url: '{{ route('caja.pagos.pagarConsumoServicio') }}',
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
                                        $('#room_select').trigger('change');
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

            $('#alquiler-form').on('submit', function(e) {
                let importe = parseFloat($('input[name="importe"]').val()) || 0;
                let deuda = parseFloat($('#deuda-alquiler-value').val()) || 0;

                console.log('Importe:', importe, 'Deuda:', deuda);

                if (importe > deuda) {
                    e.preventDefault();
                    toastr.error('El importe no tiene que ser mayor a la deuda.');
                }
            });

            @if (isset($selectedRoomId) && $selectedRoomId)
                $('#room_select').val('{{ $selectedRoomId }}').trigger('change');
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    $('#room_select').trigger('change');
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
        });
    </script>
@stop
