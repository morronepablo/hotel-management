@extends('adminlte::page')

@section('title', 'Añadir Consumo')

@section('content_header')
    <h1>
        Añadir Consumo a la Habitación N°: {{ $entry->room->room_number }} - {{ $entry->roomType->name ?? 'PERSONALIZADO' }}
        | Cliente: {{ $entry->client->nro_documento }} | {{ $entry->client->name }} {{ $entry->client->lastname }}
    </h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Tarifa:</strong> {{ $entry->roomType->name ?? 'PERSONALIZADO' }}<br>
                    <strong>F. Entrada:</strong> {{ \Carbon\Carbon::parse($entry->check_in)->format('d/m/Y H:i') }}<br>
                    <strong>F. Prevista de Salida:</strong>
                    {{ \Carbon\Carbon::parse($entry->check_out)->format('d/m/Y H:i') }}
                </div>
            </div>

            <hr>

            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label>Producto</label>
                    <select class="form-control select2" id="producto_id" name="producto_id" style="width: 100%;">
                        <option value="" disabled selected>Seleccionar producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}"
                                data-stock="{{ $producto->stock }}">
                                {{ $producto->producto }} (Stock: {{ $producto->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" value="1" min="1">
                </div>
                <div class="col-md-2">
                    <label>Stock</label>
                    <input type="text" class="form-control" id="stock" readonly value="">
                </div>
                <div class="col-md-2">
                    <label>Precio</label>
                    <input type="text" class="form-control" id="precio" readonly value="">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block" id="agregarProducto">Agregar</button>
                </div>
            </div>

            <table id="detalles-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Opciones</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Pagado</th>
                        <th>Forma de Pago</th>
                    </tr>
                </thead>
                <tbody id="detalles-tbody">
                    @forelse ($detalles as $detalle)
                        <tr data-id="{{ $detalle->id }}">
                            <td class="text-center">
                                @if ($detalle->vendido)
                                    <button class="btn btn-warning btn-sm devolver-product" data-id="{{ $detalle->id }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @else
                                    <button class="btn btn-danger btn-sm remove-product" data-id="{{ $detalle->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                            <td>{{ $detalle->producto ? $detalle->producto->producto : 'Producto no encontrado' }}</td>
                            <td class="text-right">{{ $detalle->cantidad }}</td>
                            <td class="text-right">$ {{ number_format($detalle->precio, 2, '.', ',') }}</td>
                            <td class="text-right">$ {{ number_format($detalle->subtotal, 2, '.', ',') }}</td>
                            <td class="text-center">
                                <select class="form-control pago-status" data-id="{{ $detalle->id }}"
                                    data-subtotal="{{ $detalle->subtotal }}" {{ $detalle->vendido ? 'disabled' : '' }}>
                                    <option value="Pendiente" {{ $detalle->estado == 'Pendiente' ? 'selected' : '' }}>
                                        Pendiente</option>
                                    <option value="Pagado" {{ $detalle->estado == 'Pagado' ? 'selected' : '' }}>Pagar ahora
                                    </option>
                                </select>
                            </td>
                            <td class="text-center forma-pago-cell">
                                @if ($detalle->estado == 'Pagado')
                                    <select class="form-control select2 forma-pago" data-id="{{ $detalle->id }}"
                                        {{ $detalle->vendido ? 'disabled' : '' }}>
                                        <option value="Efectivo"
                                            {{ $detalle->forma_pago == 'Efectivo' ? 'selected' : '' }}>
                                            Efectivo</option>
                                        <option value="MercadoPago"
                                            {{ $detalle->forma_pago == 'MercadoPago' ? 'selected' : '' }}>MercadoPago
                                        </option>
                                        <option value="Tarjeta" {{ $detalle->forma_pago == 'Tarjeta' ? 'selected' : '' }}>
                                            Tarjeta</option>
                                        <option value="Transferencia"
                                            {{ $detalle->forma_pago == 'Transferencia' ? 'selected' : '' }}>Transferencia
                                        </option>
                                    </select>
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay productos agregados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="text-right">
                <h3 class="mt-3">Total: <span id="totalConsumo">$
                        {{ number_format($consumo->total, 2, '.', ',') }}</span></h3>
                <h3>Pagado: $ <span id="pagado">{{ number_format($totalPagado, 2, '.', ',') }}</span></h3>
                <h3>Saldo Deudor: $ <span id="saldo">{{ number_format($saldoDeudor, 2, '.', ',') }}</span></h3>
                <hr>
            </div>

            <div class="mt-3">
                <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger"
                    title="Regresa al panel de control">Volver</a>
                <form action="{{ route('consumo.markAsPaid', $consumo->id) }}" method="POST" style="display:inline;"
                    id="form-mark-as-paid">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="venderBtn">Vender</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            height: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
        }

        #detalles-table th,
        #detalles-table td {
            vertical-align: middle;
        }

        #detalles-table th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        #stock,
        #precio {
            text-align: right;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .swal2-select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #d9d9d9;
            font-size: 14px;
        }

        /* Ajuste del ancho de los select en la tabla */
        #detalles-table .pago-status,
        #detalles-table .forma-pago {
            width: 140px !important;
            font-size: 14px;
            padding: 2px 5px;
            display: inline-block;
            /* Hacer que el select sea inline-block para centrarlo */
            margin: 0 auto;
            /* Centrar horizontalmente dentro de la celda */
        }

        /* Ajustar el contenedor de Select2 para que respete el ancho y se centre */
        #detalles-table .select2-container {
            width: 140px !important;
            display: inline-block !important;
            /* Asegurar que Select2 también se comporte como inline-block */
            margin: 0 auto;
            /* Centrar horizontalmente */
        }

        /* Asegurar que el texto dentro del select no se desborde */
        #detalles-table .select2-selection__rendered {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Función para formatear números como $ 1,200.00
            function formatCurrency(value) {
                return '$ ' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            // Inicializar Select2
            $('#producto_id').select2({
                placeholder: "Seleccionar producto",
                allowClear: true
            });

            $('.forma-pago').select2();

            // Actualizar precio y stock al seleccionar un producto
            $('#producto_id').on('change', function() {
                let selectedOption = $(this).find('option[value="' + $(this).val() + '"]');
                let precio = parseFloat(selectedOption.attr('data-precio')) || 0;
                let stock = parseInt(selectedOption.attr('data-stock')) || 0;

                if ($(this).val()) {
                    $('#precio').val(formatCurrency(precio));
                    $('#stock').val(stock);
                } else {
                    $('#precio').val('');
                    $('#stock').val('');
                }
            }).trigger('change');

            // Agregar producto
            $('#agregarProducto').on('click', function(e) {
                e.preventDefault();
                let productoId = $('#producto_id').val();
                let cantidad = parseInt($('#cantidad').val());
                let stock = parseInt($('#stock').val());

                if (!productoId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Por favor, seleccione un producto.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    return;
                }

                if (cantidad <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'La cantidad debe ser mayor a 0.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    return;
                }

                if (cantidad > stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'No hay suficiente stock disponible para este producto.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('consumo.addProduct', $consumo->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        producto_id: productoId,
                        cantidad: cantidad,
                    },
                    success: function(response) {
                        if (response.success) {
                            let detalle = response.detalle;

                            // Limpiar el mensaje "No hay productos agregados"
                            if ($('#detalles-tbody tr').length === 1 && $(
                                    '#detalles-tbody tr td').attr('colspan')) {
                                $('#detalles-tbody').empty();
                            }

                            // Agregar la nueva fila manualmente
                            let newRow = `
                                <tr data-id="${detalle.id}">
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm remove-product" data-id="${detalle.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>${detalle.producto || 'Producto no encontrado'}</td>
                                    <td class="text-right">${detalle.cantidad}</td>
                                    <td class="text-right">${formatCurrency(detalle.precio)}</td>
                                    <td class="text-right">${formatCurrency(detalle.subtotal)}</td>
                                    <td class="text-center">
                                        <select class="form-control pago-status" data-id="${detalle.id}" data-subtotal="${detalle.subtotal}">
                                            <option value="Pendiente" selected>Pendiente</option>
                                            <option value="Pagado">Pagar ahora</option>
                                        </select>
                                    </td>
                                    <td class="text-center forma-pago-cell">
                                        <span>N/A</span>
                                    </td>
                                </tr>
                            `;
                            $('#detalles-tbody').append(newRow);

                            // Actualizar el total
                            $('#totalConsumo').text(formatCurrency(response.total));
                            updateSummary();
                            $('#producto_id').val('').trigger('change'); // Limpiar el select
                            $('#cantidad').val(1); // Resetear cantidad
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al agregar el producto.',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                    }
                });
            });

            // Eliminar producto
            $(document).on('click', '.remove-product', function() {
                let detalleId = $(this).data('id');
                let row = $(this).closest('tr');

                $.ajax({
                    url: '{{ url('consumo/remove-product') }}/' + detalleId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            $(row).remove();
                            if ($('#detalles-tbody tr').length === 0) {
                                $('#detalles-tbody').html(
                                    '<tr><td colspan="7" class="text-center">No hay productos agregados.</td></tr>'
                                );
                            }
                            $('#totalConsumo').text(formatCurrency(response.total));
                            updateSummary();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al eliminar el producto.',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                    }
                });
            });

            // Devolver producto
            $(document).on('click', '.devolver-product', function() {
                let detalleId = $(this).data('id');
                let row = $(this).closest('tr');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se registrará una devolución y se eliminará el ítem.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, devolver',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('consumo/devolver') }}/' + detalleId,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(row).remove();
                                    if ($('#detalles-tbody tr').length === 0) {
                                        $('#detalles-tbody').html(
                                            '<tr><td colspan="7" class="text-center">No hay productos agregados.</td></tr>'
                                        );
                                    }
                                    $('#totalConsumo').text(formatCurrency(response
                                        .total));
                                    updateSummary();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Producto devuelto correctamente.',
                                        showConfirmButton: false,
                                        timer: 2500,
                                        timerProgressBar: true
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2500,
                                        timerProgressBar: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al devolver el producto.',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                            }
                        });
                    }
                });
            });

            // Manejar cambio en el estado de pago
            $(document).on('change', '.pago-status', function() {
                var detalleId = $(this).data('id');
                var estado = $(this).val();
                var $row = $(this).closest('tr');
                var $formaPagoCell = $row.find('.forma-pago-cell');
                var subtotal = $(this).data('subtotal');

                // Guardar el valor original para restaurarlo si el usuario cancela
                $(this).data('original-value', $(this).val());

                if (estado === 'Pagado') {
                    Swal.fire({
                        title: '¿Está seguro?',
                        text: 'Se actualizará el estado de pago de este producto.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Actualizar',
                        cancelButtonText: 'No',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        html: `
                            <div>
                                <p>Seleccione la forma de pago:</p>
                                <label for="swal-forma-pago">Forma de Pago:</label>
                                <select id="swal-forma-pago" class="swal2-select">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="MercadoPago">MercadoPago</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        `,
                        didOpen: () => {
                            $('#swal-forma-pago').select2({
                                dropdownParent: $('.swal2-container'),
                                width: '100%'
                            });
                        },
                        preConfirm: () => {
                            const formaPago = $('#swal-forma-pago').val();
                            if (!formaPago) {
                                Swal.showValidationMessage(
                                    'Por favor, seleccione una forma de pago.');
                                return false;
                            }
                            return formaPago;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formaPago = result.value;

                            $.ajax({
                                url: '{{ route('consumo.updatePaymentStatus') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    detalle_id: detalleId,
                                    estado: estado,
                                    forma_pago: formaPago,
                                    monto: subtotal,
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $formaPagoCell.html(`
                                            <select class="form-control select2 forma-pago" data-id="${detalleId}">
                                                <option value="Efectivo" ${formaPago === 'Efectivo' ? 'selected' : ''}>Efectivo</option>
                                                <option value="MercadoPago" ${formaPago === 'MercadoPago' ? 'selected' : ''}>MercadoPago</option>
                                                <option value="Tarjeta" ${formaPago === 'Tarjeta' ? 'selected' : ''}>Tarjeta</option>
                                                <option value="Transferencia" ${formaPago === 'Transferencia' ? 'selected' : ''}>Transferencia</option>
                                            </select>
                                        `);
                                        $formaPagoCell.find('.forma-pago').select2();

                                        updateSummary();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Éxito',
                                            text: 'Estado de pago actualizado.',
                                            showConfirmButton: false,
                                            timer: 2500,
                                            timerProgressBar: true
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2500,
                                            timerProgressBar: true
                                        });
                                        $(this).val('Pendiente');
                                        $formaPagoCell.html('<span>N/A</span>');
                                    }
                                },
                                error: function(xhr) {
                                    console.log(
                                        'Error en updatePaymentStatus (Pagado):',
                                        xhr.responseText);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo actualizar el estado de pago.',
                                        showConfirmButton: false,
                                        timer: 2500,
                                        timerProgressBar: true
                                    });
                                    $(this).val('Pendiente');
                                    $formaPagoCell.html('<span>N/A</span>');
                                }
                            });
                        } else {
                            $(this).val($(this).data('original-value'));
                        }
                    });
                } else {
                    $.ajax({
                        url: '{{ route('consumo.updatePaymentStatus') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            detalle_id: detalleId,
                            estado: estado,
                        },
                        success: function(response) {
                            if (response.success) {
                                $formaPagoCell.html('<span>N/A</span>');
                                updateSummary();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: 'Estado de pago actualizado.',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log('Error en updatePaymentStatus (Pendiente):', xhr
                                .responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo actualizar el estado de pago.',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            });
                            $(this).val($(this).data('original-value'));
                        }
                    });
                }
            });

            // Actualizar el resumen de Pagado y Saldo Deudor
            function updateSummary() {
                var pagado = 0;
                $('.pago-status').each(function() {
                    if ($(this).val() === 'Pagado') {
                        pagado += parseFloat($(this).data('subtotal')) || 0;
                    }
                });

                var totalText = $('#totalConsumo').text().replace('$ ', '').replace(/,/g, '');
                var total = parseFloat(totalText) || 0;

                var saldo = total - pagado;
                saldo = Math.max(saldo, 0);

                console.log('updateSummary - Total:', total, 'Pagado:', pagado, 'Saldo Deudor:', saldo);

                $('#pagado').text(formatNumber(pagado));
                $('#saldo').text(formatNumber(saldo));
            }

            // Función para formatear números sin el prefijo $
            function formatNumber(value) {
                return parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
        });
    </script>
@endsection
