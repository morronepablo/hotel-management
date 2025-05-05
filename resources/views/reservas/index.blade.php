{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Reservas</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Reservas registradas</h3>
                    <div class="card-tools">
                        <a href="#" target="_blank" class="btn btn-secondary btn-sm">
                            <i class="fa fa-file-pdf"></i> Reporte
                        </a>
                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#createReservationModal">
                            <i class="fa fa-plus"></i> Crear nueva
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="reservations-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">Nro.</th>
                                <th scope="col" class="text-center">Cliente</th>
                                <th scope="col" class="text-center">Habitación</th>
                                <th scope="col" class="text-center">Fecha de Ingreso</th>
                                <th scope="col" class="text-center">Fecha de Salida</th>
                                <th scope="col" class="text-center">Total</th>
                                <th scope="col" class="text-center">Estado</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                    <td>{{ $reservation->client->name ?? 'Cliente no encontrado' }}</td>
                                    <td>{{ $reservation->room->room_number ?? 'Habitación no encontrada' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
                                    <td class="text-right">{{ '$ ' . number_format($reservation->total, 2, ',', '.') }}</td>
                                    <td>{{ $reservation->status }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Ver Reserva">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Editar Reserva">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                            <form action="{{ route('reservas.destroy', $reservation->id) }}" method="POST"
                                                onclick="preguntar{{ $reservation->id }}(event)"
                                                id="miFormulario{{ $reservation->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Eliminar Reserva"
                                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <script>
                                                function preguntar{{ $reservation->id }}(event) {
                                                    event.preventDefault();
                                                    Swal.fire({
                                                        title: "Estas seguro?",
                                                        text: "¡No podrás revertir esto!",
                                                        icon: "warning",
                                                        showCancelButton: true,
                                                        confirmButtonColor: "#3085d6",
                                                        cancelButtonColor: "#d33",
                                                        confirmButtonText: "¡Sí, bórralo!"
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            var form = $('#miFormulario{{ $reservation->id }}');
                                                            form.submit();
                                                        }
                                                    });
                                                }
                                            </script>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Reserva -->
    <div class="modal fade" id="createReservationModal" tabindex="-1" role="dialog"
        aria-labelledby="createReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createReservationModalLabel">Nueva Reserva</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('reservas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="client_id">Cliente</label>
                            <select class="form-control select2" id="client_id" name="client_id" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_id">Habitación</label>
                            <select class="form-control select2" id="room_id" name="room_id" required>
                                <option value="">Seleccione una habitación</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->room_number }} ({{ $room->type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="check_in">Fecha de Ingreso</label>
                            <input type="date" class="form-control" id="check_in" name="check_in" required>
                        </div>
                        <div class="form-group">
                            <label for="check_out">Fecha de Salida</label>
                            <input type="date" class="form-control" id="check_out" name="check_out" required>
                        </div>
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input type="number" step="0.01" class="form-control" id="total" name="total"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="">Seleccione un estado</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Versión 3.0
        </div>
        <strong>Copyright © {{ date('Y') }} <a href="#">Hotel Management</a>.</strong> Todos los derechos
        reservados.
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
            margin-right: 10px;
        }

        .btn.rounded-sm:last-child {
            margin-right: 0;
        }

        #reservations-table_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .btn-secondary:hover {
            filter: brightness(90%);
        }

        .btn-danger:hover {
            filter: brightness(90%);
        }

        .btn-info:hover {
            filter: brightness(90%);
        }

        .btn-success:hover {
            filter: brightness(90%);
        }

        .btn-warning:hover {
            filter: brightness(90%);
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
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
            var table = $('#reservations-table').DataTable({
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
                "pagingType": "full_numbers",
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "dom": '<"row"<"col-md-6"l><"col-md-6 text-right"f>><"row"<"col-md-12"B>>rt<"row"<"col-md-6"i><"col-md-6 text-right"p>>',
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
                ],
                "columnDefs": [{
                    "targets": -1,
                    "orderable": false,
                    "searchable": false
                }]
            });

            // Colocar los botones dentro del contenedor correcto
            table.buttons().container().appendTo('#reservations-table_wrapper .col-md-12:eq(1)');

            // Inicializar Select2 para los dropdowns
            $('.select2').select2({
                placeholder: 'Seleccione una opción',
                allowClear: true,
                minimumResultsForSearch: 1,
                width: '100%'
            });

            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop --}}




@extends('adminlte::page')

@section('content_header')
    <h1><b>Lista de Reservas</b></h1>
    <hr>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Reservas registradas</h3>
                    <div class="card-tools d-flex align-items-center">
                        <a href="#" class="btn btn-secondary btn-sm mr-2"><i class="fas fa-file-pdf"></i> Reporte</a>
                        <a href="{{ route('reservas.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i>
                            Crear nueva</a>
                        <div class="ml-2">
                            <label class="mr-2">Buscar:</label>
                            <input type="search" class="form-control d-inline-block w-auto" id="searchInput">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="reservas-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">Nro.</th>
                                <th scope="col" class="text-center">Cliente</th>
                                <th scope="col" class="text-center">Habitación</th>
                                <th scope="col" class="text-center">Check-In</th>
                                <th scope="col" class="text-center">Check-Out</th>
                                <th scope="col" class="text-center">Monto Total</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reserva)
                                <tr>
                                    <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                    <td class="text-center">{{ $reserva->client->name }}</td>
                                    <td class="text-center">{{ $reserva->room->room_number }}</td>
                                    <td class="text-center">{{ $reserva->check_in->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">{{ $reserva->check_out->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">{{ number_format($reserva->total_amount, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-info btn-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Reserva">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-success btn-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Reserva">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST"
                                                onclick="preguntar{{ $reserva->id }}(event)"
                                                id="miFormulario{{ $reserva->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Eliminar Reserva">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <script>
                                                function preguntar{{ $reserva->id }}(event) {
                                                    event.preventDefault();
                                                    Swal.fire({
                                                        title: "Estas seguro?",
                                                        text: "¡No podrás revertir esto!",
                                                        icon: "warning",
                                                        showCancelButton: true,
                                                        confirmButtonColor: "#3085d6",
                                                        cancelButtonColor: "#d33",
                                                        confirmButtonText: "¡Sí, bórralo!"
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            var form = $('#miFormulario{{ $reserva->id }}');
                                                            form.submit();
                                                        }
                                                    });
                                                }
                                            </script>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn.rounded-sm {
            border-radius: 5px !important;
            margin-right: 10px;
        }

        .btn.rounded-sm:last-child {
            margin-right: 0;
        }

        #reservas-table_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .btn-secondary:hover,
        .btn-danger:hover,
        .btn-info:hover,
        .btn-success:hover,
        .btn-warning:hover {
            filter: brightness(90%);
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
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
            var table = $('#reservas-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "pagingType": "full_numbers",
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "dom": '<"row"<"col-md-6"l><"col-md-6 text-right"f>><"row"<"col-md-12"B>>rt<"row"<"col-md-6"i><"col-md-6 text-right"p>>',
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
                ],
                "columnDefs": [{
                    "targets": -1,
                    "orderable": false,
                    "searchable": false
                }]
            });

            $('#searchInput').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Mostrar mensaje de éxito con SweetAlert2 sin botón y con temporizador
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@stop
