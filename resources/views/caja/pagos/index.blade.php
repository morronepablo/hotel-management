@extends('adminlte::page')

@section('content_header')
    <h1><b>Últimos Pagos</b></h1>
    <hr>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <!-- Card para el botón Agregar -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h2 class="card-title my-1" style="font-size: 30px">Pagos</h2>
                    <div class="card-tools">
                        <a href="{{ route('caja.pagos.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card para la tabla -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Últimos Pagos</h3>
                </div>

                <div class="card-body">
                    <table id="pagos-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">Fecha</th>
                                <th scope="col" class="text-center">Descripción</th>
                                <th scope="col" class="text-center">Habitación</th>
                                <th scope="col" class="text-center">Método de Pago</th>
                                <th scope="col" class="text-center">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pagos as $pago)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $pago->fecha->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $pago->descripcion }}</td>
                                    <td class="text-center">
                                        {{ $pago->room ? $pago->room->room_number : 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($pago->efectivo > 0)
                                            Efectivo
                                        @elseif ($pago->mercadopago > 0)
                                            MercadoPago
                                        @elseif ($pago->tarjeta > 0)
                                            Tarjeta
                                        @elseif ($pago->transferencia > 0)
                                            Transferencia
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-right">$ {{ number_format($pago->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Ningún dato disponible en esta tabla</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

        .btn.rounded-sm {
            border-radius: 5px !important;
        }

        .btn.rounded-sm {
            margin-right: 10px;
        }

        .btn.rounded-sm:last-child {
            margin-right: 0;
        }

        #pagos-table_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .btn-secondary:hover,
        .btn-danger:hover,
        .btn-info:hover,
        .btn-success:hover,
        .btn-warning:hover {
            filter: brightness(90%);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            // Solo inicializamos DataTables si hay datos
            @if ($pagos->isNotEmpty())
                var table = $('#pagos-table').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "pageLength": 10,
                    "lengthMenu": [
                        [5, 10, 25, 50, 100, -1],
                        [5, 10, 25, 50, 100, "Todos"]
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                    },
                    "dom": '<"row"<"col-md-6"l><"col-md-6 text-right"f>><"row"<"col-md-12"B>>rt<"row"<"col-md-6"i><"col-md-6 text-right"p>>',
                    "columns": [{
                            "data": "iteration"
                        },
                        {
                            "data": "fecha"
                        },
                        {
                            "data": "descripcion"
                        },
                        {
                            "data": "habitacion"
                        },
                        {
                            "data": "metodo_pago"
                        },
                        {
                            "data": "monto"
                        }
                    ],
                    buttons: [{
                            text: '<i class="fas fa-copy"></i> Copiar',
                            extend: 'copy',
                            className: 'btn btn-secondary rounded-sm'
                        },
                        {
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            extend: 'pdf',
                            className: 'btn btn-danger rounded-sm'
                        },
                        {
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            extend: 'csv',
                            className: 'btn btn-info rounded-sm'
                        },
                        {
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            extend: 'excel',
                            className: 'btn btn-success rounded-sm'
                        },
                        {
                            text: '<i class="fas fa-print"></i> Imprimir',
                            extend: 'print',
                            className: 'btn btn-warning rounded-sm'
                        }
                    ]
                });

                table.buttons().container().appendTo('#pagos-table_wrapper .col-md-12:eq(1)');
            @endif
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
