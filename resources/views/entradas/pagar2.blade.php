@extends('adminlte::page')

@section('content_header')
    <h1><b>Añadir Pago - Alquiler</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Habitación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                            value="{{ $entry->room->numero }} - {{ $entry->client->nombre }}" readonly>
                        <input type="hidden" id="entry-id" value="{{ $entry->id }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title my-1">Deuda Total</h3>
                    <span id="deuda-total" class="badge badge-danger">$ {{ number_format($entry->debt, 2) }}</span>
                </div>
                <div class="card-body">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title my-1">Alquiler</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Costo:</strong></p>
                                    <p class="mb-1"><strong>Descuento:</strong></p>
                                    <p class="mb-1"><strong>Pagado:</strong></p>
                                    <p class="mb-1"><strong>Deuda:</strong></p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p class="mb-1" id="costo-alquiler">$ {{ number_format($entry->total, 2) }}</p>
                                    <p class="mb-1" id="descuento-alquiler">$ {{ number_format($entry->discount, 2) }}</p>
                                    <p class="mb-1" id="pagado-alquiler">$
                                        {{ number_format($entry->payment_received, 2) }}</p>
                                    <p class="mb-1" id="deuda-alquiler">$ {{ number_format($entry->debt, 2) }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Método</label>
                                <select id="metodo-pago" name="metodo_pago" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="mercadopago">MercadoPago</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Importe</label>
                                <input type="number" id="importe-pago" name="importe" class="form-control" step="0.01"
                                    min="0">
                            </div>
                            <button id="registrar-pago" class="btn btn-primary btn-block">Registrar</button>
                        </div>
                    </div>
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
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .badge {
            font-size: 1rem;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {
            // Registrar el pago del alquiler
            $('#registrar-pago').on('click', function() {
                var entryId = $('#entry-id').val();
                var metodoPago = $('#metodo-pago').val();
                var importe = $('#importe-pago').val();

                if (!entryId || !metodoPago || !importe) {
                    toastr.error('Por favor, complete todos los campos.');
                    return;
                }

                $.ajax({
                    url: '{{ url('entradas/pagar-alquiler') }}/' + entryId,
                    method: 'POST',
                    data: {
                        metodo_pago: metodoPago,
                        importe: importe,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deuda-total').text('$ ' + parseFloat(response.deuda).toFixed(
                            2));
                            $('#deuda-alquiler').text('$ ' + parseFloat(response.deuda).toFixed(
                                2));
                            $('#pagado-alquiler').text('$ ' + parseFloat(parseFloat($(
                                    '#pagado-alquiler').text().replace('$ ', '')) +
                                parseFloat(importe)).toFixed(2));
                            $('#importe-pago').val('');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error al registrar el pago.');
                    }
                });
            });
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
    </script>
@stop
