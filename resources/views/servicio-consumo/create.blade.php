@extends('adminlte::page')

@section('title', 'Servicio | Atención al Huésped')

@section('content_header')
    <h1>Servicio | Atención al Huésped</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('servicio-consumo.index') }}">Servicio</a></li>
        <li class="breadcrumb-item active">Agregar Servicio</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de la Habitación</h3>
                </div>
                <div class="card-body">
                    <p><strong>Número de Habitación:</strong> {{ $entry->room->room_number ?? 'N/A' }}</p>
                    <p><strong>Tipo de Habitación:</strong> {{ $entry->roomType->name ?? 'PERSONALIZADO' }}</p>
                    <p><strong>Cliente:</strong>
                        {{ ($entry->client->name ?? 'N/A') . ' ' . ($entry->client->lastname ?? '') }}</p>
                    <p><strong>Fecha de Entrada:</strong> {{ \Carbon\Carbon::parse($entry->check_in)->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Agregar Servicio</h3>
                </div>
                <div class="card-body">
                    <form id="add-servicio-form" action="{{ route('servicio-consumo.addServicio', $entry->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="entry_id" value="{{ $entry->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="servicio_id">Servicio</label>
                                    <select name="servicio_id" id="servicio_id" class="form-control select2" required>
                                        <option value="">Seleccione un servicio</option>
                                        @foreach ($servicios as $servicio)
                                            <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}">
                                                {{ $servicio->nombre }} - $ {{ number_format($servicio->precio, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" value="1"
                                        min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="submit" class="btn btn-success btn-block">Agregar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h4>Lista de Servicios</h4>
                        <div id="servicios-container">
                            @if ($detalles->isEmpty())
                                <p id="no-servicios">No hay servicios registrados.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detalles-table">
                                        <thead>
                                            <tr>
                                                <th>Servicio</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Subtotal</th>
                                                <th>Estado</th>
                                                <th>Forma de Pago</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($detalles as $detalle)
                                                @if ($detalle->servicio)
                                                    <tr data-detalle-id="{{ $detalle->id }}"
                                                        data-vendido="{{ $detalle->vendido ? 'true' : 'false' }}">
                                                        <td>{{ $detalle->servicio->nombre }}</td>
                                                        <td>{{ $detalle->cantidad }}</td>
                                                        <td>$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                        <td>$ {{ number_format($detalle->subtotal, 2) }}</td>
                                                        <td>
                                                            <select class="form-control payment-status"
                                                                data-detalle-id="{{ $detalle->id }}"
                                                                {{ $detalle->vendido ? 'disabled' : '' }}>
                                                                <option value="Pendiente"
                                                                    {{ $detalle->estado == 'Pendiente' ? 'selected' : '' }}>
                                                                    Pendiente</option>
                                                                <option value="Pagado"
                                                                    {{ $detalle->estado == 'Pagado' ? 'selected' : '' }}>
                                                                    Pagado</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            @if ($detalle->estado == 'Pagado')
                                                                <select class="form-control forma-pago"
                                                                    data-detalle-id="{{ $detalle->id }}"
                                                                    {{ $detalle->vendido ? 'disabled' : '' }}>
                                                                    <option value="Efectivo"
                                                                        {{ $detalle->forma_pago == 'Efectivo' ? 'selected' : '' }}>
                                                                        Efectivo</option>
                                                                    <option value="MercadoPago"
                                                                        {{ $detalle->forma_pago == 'MercadoPago' ? 'selected' : '' }}>
                                                                        MercadoPago</option>
                                                                    <option value="Tarjeta"
                                                                        {{ $detalle->forma_pago == 'Tarjeta' ? 'selected' : '' }}>
                                                                        Tarjeta</option>
                                                                    <option value="Transferencia"
                                                                        {{ $detalle->forma_pago == 'Transferencia' ? 'selected' : '' }}>
                                                                        Transferencia</option>
                                                                </select>
                                                            @else
                                                                <span>N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($detalle->estado == 'Pendiente')
                                                                <button class="btn btn-danger btn-sm btn-quitar"
                                                                    data-detalle-id="{{ $detalle->id }}">Quitar</button>
                                                            @elseif ($detalle->estado == 'Pagado' && !$detalle->vendido && !$hasPagadosNoVendidos)
                                                                <button class="btn btn-danger btn-sm btn-eliminar"
                                                                    data-detalle-id="{{ $detalle->id }}">Eliminar</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"><strong>Total:</strong></td>
                                                <td id="total">$ {{ number_format($servicioConsumo->total, 2) }}</td>
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Total Pagado:</strong></td>
                                                <td id="totalPagado">$ {{ number_format($totalPagado, 2) }}</td>
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Saldo Deudor:</strong></td>
                                                <td id="saldoDeudor">$ {{ number_format($saldoDeudor, 2) }}</td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div id="action-buttons" class="mt-3">
                            @if ($hasPagadosNoVendidos)
                                <form id="vendido-button"
                                    action="{{ route('servicio-consumo.vendido', $servicioConsumo->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Vendido</button>
                                </form>
                            @else
                                <a href="{{ route('entradas.panel-control') }}" class="btn btn-secondary"
                                    id="volver-button">Volver</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        .swal2-select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #d9d9d9;
            font-size: 14px;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            function updateActionButtons() {
                let hasPagadoNoVendido = false;
                $('#detalles-table tbody tr').each(function() {
                    const $row = $(this);
                    const estado = $row.find('.payment-status').val();
                    const vendido = $row.data('vendido') === 'true';
                    if (estado === 'Pagado' && !vendido) {
                        hasPagadoNoVendido = true;
                    }
                });

                const $actionButtons = $('#action-buttons');
                $actionButtons.empty();

                if (hasPagadoNoVendido) {
                    const vendidoForm = `
                        <form id="vendido-button" action="{{ route('servicio-consumo.vendido', $servicioConsumo->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">Vendido</button>
                        </form>
                    `;
                    $actionButtons.append(vendidoForm);
                } else {
                    const volverButton = `
                        <a href="{{ route('entradas.panel-control') }}" class="btn btn-secondary" id="volver-button">Volver</a>
                    `;
                    $actionButtons.append(volverButton);
                }
            }

            $('#add-servicio-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            var detalle = response.detalle;
                            // Proteger contra detalle.servicio undefined
                            var nombreServicio = detalle.servicio || 'Servicio no especificado';
                            var row = `
                    <tr data-detalle-id="${detalle.id}" data-vendido="false">
                        <td>${nombreServicio}</td>
                        <td>${detalle.cantidad}</td>
                        <td>$ ${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                        <td>$ ${parseFloat(detalle.subtotal).toFixed(2)}</td>
                        <td>
                            <select class="form-control payment-status" data-detalle-id="${detalle.id}">
                                <option value="Pendiente" selected>Pendiente</option>
                                <option value="Pagado">Pagado</option>
                            </select>
                        </td>
                        <td><span>N/A</span></td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-quitar" data-detalle-id="${detalle.id}">Quitar</button>
                        </td>
                    </tr>`;

                            // Verificar si la tabla existe
                            if ($('#detalles-table').length === 0) {
                                // Si no existe, crear la tabla
                                var tableHtml = `
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detalles-table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Estado</th>
                                        <th>Forma de Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${row}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3"><strong>Total:</strong></td>
                                        <td id="total">$ ${parseFloat(response.total).toFixed(2)}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><strong>Total Pagado:</strong></td>
                                        <td id="totalPagado">$ 0.00</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><strong>Saldo Deudor:</strong></td>
                                        <td id="saldoDeudor">$ ${parseFloat(response.total).toFixed(2)}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                                $('#servicios-container').html(tableHtml);
                            } else {
                                // Si la tabla ya existe, agregar la fila
                                $('#detalles-table tbody').append(row);
                                $('#detalles-table #total').text('$ ' + parseFloat(response
                                    .total).toFixed(2));
                            }

                            // Eliminar el mensaje "No hay servicios registrados" si existe
                            $('#no-servicios').remove();

                            // Actualizar Total Pagado y Saldo Deudor
                            $.ajax({
                                url: '{{ route('servicio-consumo.create', $entry->id) }}',
                                method: 'GET',
                                success: function(data) {
                                    var totalPagado = $(data).find('#totalPagado')
                                        .text().replace('$ ', '');
                                    var saldoDeudor = $(data).find('#saldoDeudor')
                                        .text().replace('$ ', '');
                                    $('#detalles-table #totalPagado').text('$ ' +
                                        totalPagado);
                                    $('#detalles-table #saldoDeudor').text('$ ' +
                                        saldoDeudor);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Servicio agregado correctamente.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });

                            // Resetear el formulario
                            form[0].reset();
                            $('#servicio_id').val('').trigger('change');

                            // Actualizar los botones de acción
                            updateActionButtons();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Error al agregar el servicio.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message ||
                                'Error al agregar el servicio.',
                            timer: 2500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            });

            $(document).off('change', '.payment-status').on('change', '.payment-status', function() {
                var detalleId = $(this).data('detalle-id');
                var estado = $(this).val();
                var $row = $(this).closest('tr');
                var $formaPagoCell = $row.find('td:eq(5)');
                var $actionCell = $row.find('td:eq(6)');

                // Bloquear el cambio si el servicio ya está vendido
                if ($row.data('vendido') === 'true') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede modificar el estado de un servicio que ya ha sido vendido.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    $(this).val($(this).data('original-value'));
                    return;
                }

                // Bloquear el cambio de "Pagado" a "Pendiente"
                if ($(this).data('original-value') === 'Pagado') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede cambiar el estado de "Pagado" a "Pendiente". Si hubo un error, elimine el servicio.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    $(this).val('Pagado');
                    return;
                }

                // Guardar el valor original para restaurarlo si el usuario cancela
                $(this).data('original-value', estado);

                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se actualizará el estado de pago de este servicio.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Actualizar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    html: `
                        <div>
                            <p>Se actualizará el estado de pago de este servicio.</p>
                            ${estado === 'Pagado' ? `
                                                    <label for="swal-forma-pago">Forma de Pago:</label>
                                                    <select id="swal-forma-pago" class="swal2-select">
                                                        <option value="Efectivo">Efectivo</option>
                                                        <option value="MercadoPago">MercadoPago</option>
                                                        <option value="Tarjeta">Tarjeta</option>
                                                        <option value="Transferencia">Transferencia</option>
                                                    </select>
                                                ` : ''}
                        </div>
                    `,
                    didOpen: () => {
                        if (estado === 'Pagado') {
                            $('#swal-forma-pago').select2({
                                dropdownParent: $('.swal2-container'),
                                width: '100%'
                            });
                        }
                    },
                    preConfirm: () => {
                        if (estado === 'Pagado') {
                            const formaPago = $('#swal-forma-pago').val();
                            if (!formaPago) {
                                Swal.showValidationMessage(
                                    'Por favor, seleccione una forma de pago.');
                                return false;
                            }
                            return formaPago;
                        }
                        return null;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formaPago = estado === 'Pagado' ? result.value : null;

                        $.ajax({
                            url: '{{ route('servicio-consumo.updatePaymentStatus') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                detalle_id: detalleId,
                                estado: estado,
                                forma_pago: formaPago
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Estado de pago actualizado correctamente. Usa "Vendido" para registrar la venta.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });

                                    if (estado === 'Pagado') {
                                        $formaPagoCell.html(`
                                            <select class="form-control forma-pago" data-detalle-id="${detalleId}">
                                                <option value="Efectivo" ${formaPago === 'Efectivo' ? 'selected' : ''}>Efectivo</option>
                                                <option value="MercadoPago" ${formaPago === 'MercadoPago' ? 'selected' : ''}>MercadoPago</option>
                                                <option value="Tarjeta" ${formaPago === 'Tarjeta' ? 'selected' : ''}>Tarjeta</option>
                                                <option value="Transferencia" ${formaPago === 'Transferencia' ? 'selected' : ''}>Transferencia</option>
                                            </select>
                                        `);

                                        $actionCell.html('');
                                    } else {
                                        $formaPagoCell.html('<span>N/A</span>');
                                        $actionCell.html(`
                                            <button class="btn btn-danger btn-sm btn-quitar" data-detalle-id="${detalleId}">Quitar</button>
                                        `);
                                    }

                                    $.ajax({
                                        url: '{{ route('servicio-consumo.create', $entry->id) }}',
                                        method: 'GET',
                                        success: function(data) {
                                            var totalPagado = $(data).find(
                                                    '#totalPagado').text()
                                                .replace('$ ', '');
                                            var saldoDeudor = $(data).find(
                                                    '#saldoDeudor').text()
                                                .replace('$ ', '');
                                            $('#detalles-table #totalPagado')
                                                .text('$ ' + totalPagado);
                                            $('#detalles-table #saldoDeudor')
                                                .text('$ ' + saldoDeudor);
                                        }
                                    });

                                    // Actualizar los botones de acción
                                    updateActionButtons();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message ||
                                        'Error al actualizar el estado.',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                                $(this).val($(this).data('original-value'));
                            }
                        });
                    } else {
                        $(this).val($(this).data('original-value'));
                    }
                });
            });

            $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
                var detalleId = $(this).data('detalle-id');
                var $row = $(this).closest('tr');
                var estado = $row.find('.payment-status').val();
                var vendido = $row.data('vendido') === 'true';

                // Verificar si el servicio ya está vendido
                if (vendido) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede eliminar un servicio que ya ha sido vendido.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                // Verificar si el servicio está pagado
                if (estado !== 'Pagado') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede eliminar un servicio que no está pagado. Cambie el estado a "Pagado" primero.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                // Verificar si hay servicios pagados y no vendidos
                let hasPagadoNoVendido = false;
                $('#detalles-table tbody tr').each(function() {
                    const $row = $(this);
                    const estado = $row.find('.payment-status').val();
                    const vendido = $row.data('vendido') === 'true';
                    if (estado === 'Pagado' && !vendido) {
                        hasPagadoNoVendido = true;
                    }
                });

                if (hasPagadoNoVendido) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede eliminar un servicio mientras haya servicios pagados y no vendidos. Registre la venta primero.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                // Confirmar la eliminación
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se eliminará este servicio de la lista.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Eliminar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('servicio-consumo/remove-servicio') }}/' +
                                detalleId,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Eliminar la fila de la tabla
                                    $('tr[data-detalle-id="' + detalleId + '"]')
                                        .remove();

                                    // Actualizar el total
                                    $('#detalles-table #total').text('$ ' + parseFloat(
                                        response.total).toFixed(2));

                                    // Si no quedan filas, mostrar mensaje "No hay servicios registrados"
                                    if ($('#detalles-table tbody tr').length === 0) {
                                        $('#servicios-container').html(
                                            '<p id="no-servicios">No hay servicios registrados.</p>'
                                        );
                                    } else {
                                        // Actualizar Total Pagado y Saldo Deudor
                                        $.ajax({
                                            url: '{{ route('servicio-consumo.create', $entry->id) }}',
                                            method: 'GET',
                                            success: function(data) {
                                                var totalPagado = $(data)
                                                    .find('#totalPagado')
                                                    .text().replace('$ ',
                                                        '');
                                                var saldoDeudor = $(data)
                                                    .find('#saldoDeudor')
                                                    .text().replace('$ ',
                                                        '');
                                                $('#detalles-table #totalPagado')
                                                    .text('$ ' +
                                                        totalPagado);
                                                $('#detalles-table #saldoDeudor')
                                                    .text('$ ' +
                                                        saldoDeudor);
                                            },
                                            error: function(xhr) {
                                                console.error(
                                                    'Error al actualizar Total Pagado y Saldo Deudor:',
                                                    xhr);
                                            }
                                        });
                                    }

                                    // Mostrar mensaje de éxito
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Servicio eliminado correctamente.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });

                                    // Actualizar los botones de acción
                                    updateActionButtons();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message ||
                                            'Error al eliminar el servicio.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = xhr.responseJSON && xhr.responseJSON
                                    .message ?
                                    xhr.responseJSON.message :
                                    'Ocurrió un error al intentar eliminar el servicio. Por favor, intenta de nuevo.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage,
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            $(document).off('click', '.btn-quitar').on('click', '.btn-quitar', function() {
                var detalleId = $(this).data('detalle-id');
                var $row = $(this).closest('tr');
                var estado = $row.find('.payment-status').val();

                if (estado !== 'Pendiente') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede quitar un servicio que está pagado. Use "Eliminar" si desea proceder.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se quitará este servicio de la lista.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Quitar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('servicio-consumo/quitar') }}/' + detalleId,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('tr[data-detalle-id="' + detalleId + '"]')
                                        .remove();
                                    $('#detalles-table #total').text('$ ' + parseFloat(
                                        response.total).toFixed(2));

                                    // Si no quedan filas, mostrar mensaje "No hay servicios registrados"
                                    if ($('#detalles-table tbody tr').length === 0) {
                                        $('#servicios-container').html(
                                            '<p id="no-servicios">No hay servicios registrados.</p>'
                                        );
                                    } else {
                                        $.ajax({
                                            url: '{{ route('servicio-consumo.create', $entry->id) }}',
                                            method: 'GET',
                                            success: function(data) {
                                                var totalPagado = $(data)
                                                    .find('#totalPagado')
                                                    .text().replace('$ ',
                                                        '');
                                                var saldoDeudor = $(data)
                                                    .find('#saldoDeudor')
                                                    .text().replace('$ ',
                                                        '');
                                                $('#detalles-table #totalPagado')
                                                    .text('$ ' +
                                                        totalPagado);
                                                $('#detalles-table #saldoDeudor')
                                                    .text('$ ' +
                                                        saldoDeudor);
                                            }
                                        });
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Servicio quitado correctamente.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });

                                    // Actualizar los botones de acción
                                    updateActionButtons();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message ||
                                            'Error al quitar el servicio.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message ||
                                        'Error al quitar el servicio.',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@endsection
