@extends('adminlte::page')

@section('content_header')
    <h1><b>Añadir Pago - Consumos</b></h1>
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
        <!-- Columna Izquierda: Consumos -->
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Consumos</h3>
                </div>
                <div class="card-body">
                    <table id="consumo-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">Tipo</th>
                                <th scope="col" class="text-center">Nombre</th>
                                <th scope="col" class="text-center">Cant.</th>
                                <th scope="col" class="text-center">Precio</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($entry->consumo)
                                @foreach ($entry->consumo->detalles as $detalle)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">Bebidas</td>
                                        <td class="text-center">
                                            {{ $detalle->producto ? $detalle->producto->producto : 'N/A' }}</td>
                                        <td class="text-center">{{ $detalle->cantidad }}</td>
                                        <td class="text-center">$ {{ number_format($detalle->precio, 2) }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $detalle->estado === 'Falta Pagar' ? 'badge-danger' : 'badge-success' }}"
                                                data-id="{{ $detalle->id }}" data-tipo="consumo">
                                                {{ $detalle->estado }}
                                            </span>
                                        </td>
                                        <td class="text-center">$ {{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Resumen -->
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title my-1">Deuda Total</h3>
                    <span class="badge badge-danger">$
                        {{ number_format($entry->consumo ? $entry->consumo->total : 0, 2) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Costo:</strong></p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p class="mb-1">$ {{ number_format($entry->consumo ? $entry->consumo->total : 0, 2) }}</p>
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
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
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

        .badge-success {
            background-color: #28a745;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {
            // Inicializar DataTable para Consumos
            var table = $('#consumo-table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                }
            });

            // Manejar el clic en los badges de "Falta Pagar"
            $(document).on('click', '.badge-danger', function() {
                var detalleId = $(this).data('id');
                Swal.fire({
                    title: '¿Está seguro en pagar este producto?',
                    text: '¡No podrás revertir esto!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '¡Sí, continuar!',
                    cancelButtonText: 'Cancelar',
                    html: `
                        <div class="form-group mt-3">
                            <label>Método de Pago</label>
                            <select id="swal-metodo-pago" class="form-control">
                                <option value="efectivo">Efectivo</option>
                                <option value="mercadopago">MercadoPago</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                    `
                }).then((result) => {
                    if (result.isConfirmed) {
                        var metodoPago = $('#swal-metodo-pago').val();
                        $.ajax({
                            url: '{{ url('consumo/pagar-detalle') }}/' + detalleId,
                            method: 'POST',
                            data: {
                                metodo_pago: metodoPago,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al procesar el pago.',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        });
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
