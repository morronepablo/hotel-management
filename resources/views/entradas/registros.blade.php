@extends('adminlte::page')

@section('content_header')
    <h1><b>Registro de Entradas y Salidas</b></h1>
    <hr>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <!-- Card para el título -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h2 class="card-title my-1" style="font-size: 30px">Entradas y Salidas</h2>
                </div>
            </div>

            <!-- Card para la tabla -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Listado de Entradas y Salidas</h3>
                </div>

                <div class="card-body">
                    <table id="entries-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">Fecha Ingreso</th>
                                <th scope="col" class="text-center">Fecha Salida</th>
                                <th scope="col" class="text-center">Habitación</th>
                                <th scope="col" class="text-center">Cliente</th>
                                <th scope="col" class="text-center">Tipo de Entrada</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Total</th>
                                <th scope="col" class="text-center">Deuda</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entries as $entry)
                                <tr>
                                    <td class="text-center">{{ $entry->id }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($entry->fecha_ingreso)->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        @if ($entry->status === 'Finished' && $entry->salida == 1)
                                            {{ \Carbon\Carbon::parse($entry->updated_at)->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($entry->fecha_salida)->format('d/m/Y H:i') }}
                                            (Estimada)
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $entry->room ? $entry->room->room_number : 'N/A' }}</td>
                                    <td class="text-center">
                                        {{ $entry->client ? $entry->client->name . ' ' . $entry->client->lastname : 'N/A' }}
                                    </td>
                                    <td class="text-center">{{ $entry->tipo_entrada_calculado }}</td>
                                    <td class="text-center">
                                        @if ($entry->status === 'Active')
                                            <span class="badge badge-success">Activa</span>
                                        @elseif ($entry->status === 'Finished')
                                            <span class="badge badge-secondary">Finalizada</span>
                                        @else
                                            <span class="badge badge-warning">{{ $entry->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">$ {{ number_format($entry->total_calculado, 2) }}</td>
                                    <td class="text-right">$ {{ number_format($entry->deuda_calculada, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('entradas.ticket', $entry->id) }}" class="btn btn-sm btn-info"
                                            title="Reimprimir Ticket" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('entradas.detalle-general', $entry->id) }}"
                                            class="btn btn-sm btn-primary" title="Ver Detalle General" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Ningún dato disponible en esta tabla</td>
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

        #entries-table_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .btn-secondary:hover,
        .btn-danger:hover,
        .btn-info:hover,
        .btn-success:hover,
        .btn-warning:hover {
            filter: brightness(90%);
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
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
            @if ($entries->isNotEmpty())
                var table = $('#entries-table').DataTable({
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
                            "data": "id"
                        },
                        {
                            "data": "check_in"
                        },
                        {
                            "data": "check_out"
                        },
                        {
                            "data": "room"
                        },
                        {
                            "data": "client"
                        },
                        {
                            "data": "entry_type"
                        },
                        {
                            "data": "status"
                        },
                        {
                            "data": "total"
                        },
                        {
                            "data": "debt"
                        },
                        {
                            "data": "actions",
                            "orderable": false,
                            "searchable": false
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

                table.buttons().container().appendTo('#entries-table_wrapper .col-md-12:eq(1)');
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
