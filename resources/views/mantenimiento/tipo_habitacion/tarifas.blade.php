{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Gestionar Tarifas</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Tarifas del tipo de habitación: {{ $roomType->name }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTariffModal">
                            <i class="fas fa-plus"></i> Agregar tarifa
                        </button>
                        <a href="{{ route('mantenimiento.tipo_habitacion.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="tariffs-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">Nro.</th>
                                <th scope="col" class="text-center">Nombre</th>
                                <th scope="col" class="text-center">Tipo</th>
                                <th scope="col" class="text-center">Duración (Horas)</th>
                                <th scope="col" class="text-center">Hora de Salida</th>
                                <th scope="col" class="text-center">Precio</th>
                                <th scope="col" class="text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roomType->roomTypeTariffs as $roomTypeTariff)
                                <tr>
                                    <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                    <td>{{ $roomTypeTariff->name }}</td>
                                    <td class="text-center">{{ $roomTypeTariff->type }}</td>
                                    <td class="text-center">{{ $roomTypeTariff->duration ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $roomTypeTariff->hour_checkout ?? 'N/A' }}</td>
                                    <td class="text-right">$ {{ number_format($roomTypeTariff->price, 2) }}</td>
                                    <td class="text-center">
                                        <!-- Botón de Modificar -->
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#editTariffModal{{ $roomTypeTariff->id }}"
                                            title="Modificar Tarifa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Botón de Eliminar -->
                                        <form
                                            action="{{ route('mantenimiento.tipo_habitacion.tarifas.destroy', $roomTypeTariff->id) }}"
                                            method="POST" onclick="deleteTariff{{ $roomTypeTariff->id }}(event)"
                                            id="deleteForm{{ $roomTypeTariff->id }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Eliminar Tarifa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <script>
                                            function deleteTariff{{ $roomTypeTariff->id }}(event) {
                                                event.preventDefault();
                                                Swal.fire({
                                                    title: "¿Estás seguro?",
                                                    text: "¡No podrás revertir esto!",
                                                    icon: "warning",
                                                    showCancelButton: true,
                                                    confirmButtonColor: "#3085d6",
                                                    cancelButtonColor: "#d33",
                                                    confirmButtonText: "¡Sí, elimínalo!",
                                                    cancelButtonText: "Cancelar"
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById('deleteForm{{ $roomTypeTariff->id }}').submit();
                                                    }
                                                });
                                            }
                                        </script>

                                        <!-- Modal para editar tarifa -->
                                        <div class="modal fade" id="editTariffModal{{ $roomTypeTariff->id }}"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="editTariffModalLabel{{ $roomTypeTariff->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editTariffModalLabel{{ $roomTypeTariff->id }}">Modificar
                                                            Tarifa</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <form
                                                        action="{{ route('mantenimiento.tipo_habitacion.tarifas.update', $roomTypeTariff->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="name{{ $roomTypeTariff->id }}">Nombre
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="name"
                                                                            id="name{{ $roomTypeTariff->id }}"
                                                                            class="form-control @error('name') is-invalid @enderror"
                                                                            value="{{ old('name', $roomTypeTariff->name) }}"
                                                                            required>
                                                                        @error('name')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="type{{ $roomTypeTariff->id }}">Tipo
                                                                            <span class="text-danger">*</span></label>
                                                                        <select name="type"
                                                                            id="type{{ $roomTypeTariff->id }}"
                                                                            class="form-control @error('type') is-invalid @enderror"
                                                                            required>
                                                                            <option value="">Seleccione un tipo
                                                                            </option>
                                                                            <option value="HORA"
                                                                                {{ old('type', $roomTypeTariff->type) == 'HORA' ? 'selected' : '' }}>
                                                                                HORA</option>
                                                                            <option value="DIA"
                                                                                {{ old('type', $roomTypeTariff->type) == 'DIA' ? 'selected' : '' }}>
                                                                                DIA</option>
                                                                        </select>
                                                                        @error('type')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group"
                                                                        id="durationField{{ $roomTypeTariff->id }}"
                                                                        style="display: {{ $roomTypeTariff->type == 'HORA' ? 'block' : 'none' }};">
                                                                        <label
                                                                            for="duration{{ $roomTypeTariff->id }}">Duración
                                                                            (Horas)
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number" name="duration"
                                                                            id="duration{{ $roomTypeTariff->id }}"
                                                                            class="form-control @error('duration') is-invalid @enderror"
                                                                            value="{{ old('duration', $roomTypeTariff->duration) }}"
                                                                            min="1">
                                                                        @error('duration')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="form-group"
                                                                        id="hourCheckoutField{{ $roomTypeTariff->id }}"
                                                                        style="display: {{ $roomTypeTariff->type == 'DIA' ? 'block' : 'none' }};">
                                                                        <label
                                                                            for="hour_checkout{{ $roomTypeTariff->id }}">Hora
                                                                            de Salida <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="time" name="hour_checkout"
                                                                            id="hour_checkout{{ $roomTypeTariff->id }}"
                                                                            class="form-control @error('hour_checkout') is-invalid @enderror"
                                                                            value="{{ old('hour_checkout', $roomTypeTariff->hour_checkout) }}">
                                                                        @error('hour_checkout')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="price{{ $roomTypeTariff->id }}">Precio
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number" name="price"
                                                                            id="price{{ $roomTypeTariff->id }}"
                                                                            class="form-control @error('price') is-invalid @enderror"
                                                                            value="{{ old('price', $roomTypeTariff->price) }}"
                                                                            step="0.01" min="0" required>
                                                                        @error('price')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary"><i
                                                                    class="fas fa-save"></i> Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
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

    <!-- Modal para agregar tarifa -->
    <div class="modal fade" id="addTariffModal" tabindex="-1" role="dialog" aria-labelledby="addTariffModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTariffModalLabel">Agregar Nueva Tarifa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('mantenimiento.tipo_habitacion.tarifas.store', $roomType->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipo <span class="text-danger">*</span></label>
                                    <select name="type" id="type"
                                        class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="HORA" {{ old('type') == 'HORA' ? 'selected' : '' }}>HORA</option>
                                        <option value="DIA" {{ old('type') == 'DIA' ? 'selected' : '' }}>DIA</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="durationField" style="display: none;">
                                    <label for="duration">Duración (Horas) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="duration"
                                        class="form-control @error('duration') is-invalid @enderror"
                                        value="{{ old('duration') }}" min="1">
                                    @error('duration')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group" id="hourCheckoutField" style="display: none;">
                                    <label for="hour_checkout">Hora de Salida <span class="text-danger">*</span></label>
                                    <input type="time" name="hour_checkout" id="hour_checkout"
                                        class="form-control @error('hour_checkout') is-invalid @enderror"
                                        value="{{ old('hour_checkout') }}">
                                    @error('hour_checkout')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Precio <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price') }}" step="0.01" min="0" required>
                                    @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar
                            Tarifa</button>
                    </div>
                </form>
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

        #tariffs-table_wrapper .dt-buttons {
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
                // Inicializar DataTable y guardar la referencia en una variable
                var table = $('#tariffs-table').DataTable({
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

                // Añadir los botones al contenedor correcto
                table.buttons().container().appendTo('#tariffs-table_wrapper .col-md-12:eq(1)');

                // Función para alternar visibilidad de campos (para el modal de agregar)
                function toggleFieldsAdd() {
                    const type = document.getElementById('type').value;
                    const durationField = document.getElementById('durationField');
                    const hourCheckoutField = document.getElementById('hourCheckoutField');
                    const durationInput = document.getElementById('duration');
                    const hourCheckoutInput = document.getElementById('hour_checkout');

                    console.log('Tipo seleccionado (Agregar):', type);

                    if (type === 'HORA') {
                        console.log('Mostrando Duración, ocultando Hora de Salida (Agregar)');
                        durationField.style.display = 'block';
                        hourCheckoutField.style.display = 'none';
                        durationInput.setAttribute('required', 'required');
                        hourCheckoutInput.removeAttribute('required');
                        hourCheckoutInput.value = '';
                    } else if (type === 'DIA') {
                        console.log('Mostrando Hora de Salida, ocultando Duración (Agregar)');
                        durationField.style.display = 'none';
                        hourCheckoutField.style.display = 'block';
                        durationInput.removeAttribute('required');
                        hourhint: true
                        icon: '<i class="fa fa-arrow-right" />'
                    }
                });
        }

        // Asignar el evento change al select del modal de agregar
        const typeSelectAdd = document.getElementById('type');
        typeSelectAdd.addEventListener('change', function() {
            console.log('Evento change disparado (Agregar)');
            toggleFieldsAdd();
        });

        // Inicializar visibilidad de campos al abrir el modal de agregar
        $('#addTariffModal').on('shown.bs.modal', function() {
            console.log('Modal de agregar abierto, inicializando campos');
            toggleFieldsAdd();
        });

        // Inicializar visibilidad de campos al cargar la página (para agregar)
        toggleFieldsAdd();

        // Función para alternar visibilidad de campos (para cada modal de editar)
        @foreach ($roomType->roomTypeTariffs as $roomTypeTariff)
            function toggleFieldsEdit{{ $roomTypeTariff->id }}() {
                const type = document.getElementById('type{{ $roomTypeTariff->id }}').value;
                const durationField = document.getElementById('durationField{{ $roomTypeTariff->id }}');
                const hourCheckoutField = document.getElementById('hourCheckoutField{{ $roomTypeTariff->id }}');
                const durationInput = document.getElementById('duration{{ $roomTypeTariff->id }}');
                const hourCheckoutInput = document.getElementById('hour_checkout{{ $roomTypeTariff->id }}');

                console.log('Tipo seleccionado (Editar {{ $roomTypeTariff->id }}):', type);

                if (type === 'HORA') {
                    console.log('Mostrando Duración, ocultando Hora de Salida (Editar {{ $roomTypeTariff->id }})');
                    durationField.style.display = 'block';
                    hourCheckoutField.style.display = 'none';
                    durationInput.setAttribute('required', 'required');
                    hourCheckoutInput.removeAttribute('required');
                    hourCheckoutInput.value = '';
                } else if (type === 'DIA') {
                    console.log('Mostrando Hora de Salida, ocultando Duración (Editar {{ $roomTypeTariff->id }})');
                    durationField.style.display = 'none';
                    hourCheckoutField.style.display = 'block';
                    durationInput.removeAttribute('required');
                    hourCheckoutInput.setAttribute('required', 'required');
                    durationInput.value = '';
                } else {
                    console.log('Ocultando ambos campos (Editar {{ $roomTypeTariff->id }})');
                    durationField.style.display = 'none';
                    hourCheckoutField.style.display = 'none';
                    durationInput.removeAttribute('required');
                    hourCheckoutInput.removeAttribute('required');
                    durationInput.value = '';
                    hourCheckoutInput.value = '';
                }
            }

            // Asignar el evento change al select del modal de editar
            document.getElementById('type{{ $roomTypeTariff->id }}').addEventListener('change', function() {
                console.log('Evento change disparado (Editar {{ $roomTypeTariff->id }})');
                toggleFieldsEdit{{ $roomTypeTariff->id }}();
            });

            // Inicializar visibilidad de campos al abrir el modal de editar
            $('#editTariffModal{{ $roomTypeTariff->id }}').on('shown.bs.modal', function() {
                console.log('Modal de editar {{ $roomTypeTariff->id }} abierto, inicializando campos');
                toggleFieldsEdit{{ $roomTypeTariff->id }}();
            });
        @endforeach
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop --}}
















@extends('adminlte::page')

@section('content_header')
    <h1><b>Gestionar Tarifas</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Tarifas del tipo de habitación: {{ $roomType->name }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTariffModal">
                            <i class="fas fa-plus"></i> Agregar tarifa
                        </button>
                        <a href="{{ route('mantenimiento.tipo_habitacion.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="tariffs-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">Nro.</th>
                                <th scope="col" class="text-center">Nombre</th>
                                <th scope="col" class="text-center">Tipo</th>
                                <th scope="col" class="text-center">Duración (Horas)</th>
                                <th scope="col" class="text-center">Hora de Salida</th>
                                <th scope="col" class="text-center">Precio</th>
                                <th scope="col" class="text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($roomType->roomTypeTariffs && $roomType->roomTypeTariffs->count() > 0)
                                @foreach ($roomType->roomTypeTariffs as $roomTypeTariff)
                                    <tr>
                                        <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                        <td>{{ $roomTypeTariff->name }}</td>
                                        <td class="text-center">{{ $roomTypeTariff->type }}</td>
                                        <td class="text-center">{{ $roomTypeTariff->duration ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $roomTypeTariff->hour_checkout ?? 'N/A' }}</td>
                                        <td class="text-right">$ {{ number_format($roomTypeTariff->price, 2) }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <!-- Botón de Modificar -->
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editTariffModal{{ $roomTypeTariff->id }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Modificar Tarifa">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- Botón de Eliminar -->
                                                <form
                                                    action="{{ route('mantenimiento.tipo_habitacion.tarifas.destroy', $roomTypeTariff->id) }}"
                                                    method="POST" onclick="deleteTariff{{ $roomTypeTariff->id }}(event)"
                                                    id="deleteForm{{ $roomTypeTariff->id }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Eliminar Tarifa"
                                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    function deleteTariff{{ $roomTypeTariff->id }}(event) {
                                                        event.preventDefault();
                                                        Swal.fire({
                                                            title: "¿Estás seguro?",
                                                            text: "¡No podrás revertir esto!",
                                                            icon: "warning",
                                                            showCancelButton: true,
                                                            confirmButtonColor: "#3085d6",
                                                            cancelButtonColor: "#d33",
                                                            confirmButtonText: "¡Sí, elimínalo!",
                                                            cancelButtonText: "Cancelar"
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                document.getElementById('deleteForm{{ $roomTypeTariff->id }}').submit();
                                                            }
                                                        });
                                                    }
                                                </script>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No hay tarifas asociadas a este tipo de
                                        habitación.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar tarifa -->
    @foreach ($roomType->roomTypeTariffs as $roomTypeTariff)
        <div class="modal fade" id="editTariffModal{{ $roomTypeTariff->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editTariffModalLabel{{ $roomTypeTariff->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTariffModalLabel{{ $roomTypeTariff->id }}">Modificar Tarifa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('mantenimiento.tipo_habitacion.tarifas.update', $roomTypeTariff->id) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name{{ $roomTypeTariff->id }}">Nombre <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name{{ $roomTypeTariff->id }}"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $roomTypeTariff->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type{{ $roomTypeTariff->id }}">Tipo <span
                                                class="text-danger">*</span></label>
                                        <select name="type" id="type{{ $roomTypeTariff->id }}"
                                            class="form-control @error('type') is-invalid @enderror" required>
                                            <option value="">Seleccione un tipo</option>
                                            <option value="HORA"
                                                {{ old('type', $roomTypeTariff->type) == 'HORA' ? 'selected' : '' }}>HORA
                                            </option>
                                            <option value="DIA"
                                                {{ old('type', $roomTypeTariff->type) == 'DIA' ? 'selected' : '' }}>DIA
                                            </option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="durationField{{ $roomTypeTariff->id }}"
                                        style="display: {{ $roomTypeTariff->type == 'HORA' ? 'block' : 'none' }};">
                                        <label for="duration{{ $roomTypeTariff->id }}">Duración (Horas) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="duration" id="duration{{ $roomTypeTariff->id }}"
                                            class="form-control @error('duration') is-invalid @enderror"
                                            value="{{ old('duration', $roomTypeTariff->duration) }}" min="1">
                                        @error('duration')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="hourCheckoutField{{ $roomTypeTariff->id }}"
                                        style="display: {{ $roomTypeTariff->type == 'DIA' ? 'block' : 'none' }};">
                                        <label for="hour_checkout{{ $roomTypeTariff->id }}">Hora de Salida <span
                                                class="text-danger">*</span></label>
                                        <input type="time" name="hour_checkout"
                                            id="hour_checkout{{ $roomTypeTariff->id }}"
                                            class="form-control @error('hour_checkout') is-invalid @enderror"
                                            value="{{ old('hour_checkout', $roomTypeTariff->hour_checkout) }}">
                                        @error('hour_checkout')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price{{ $roomTypeTariff->id }}">Precio <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="price" id="price{{ $roomTypeTariff->id }}"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price', $roomTypeTariff->price) }}" step="0.01"
                                            min="0" required>
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar
                                Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal para agregar tarifa -->
    <div class="modal fade" id="addTariffModal" tabindex="-1" role="dialog" aria-labelledby="addTariffModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTariffModalLabel">Agregar Nueva Tarifa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('mantenimiento.tipo_habitacion.tarifas.store', $roomType->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipo <span class="text-danger">*</span></label>
                                    <select name="type" id="type"
                                        class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="HORA" {{ old('type') == 'HORA' ? 'selected' : '' }}>HORA</option>
                                        <option value="DIA" {{ old('type') == 'DIA' ? 'selected' : '' }}>DIA</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="durationField" style="display: none;">
                                    <label for="duration">Duración (Horas) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="duration"
                                        class="form-control @error('duration') is-invalid @enderror"
                                        value="{{ old('duration') }}" min="1">
                                    @error('duration')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group" id="hourCheckoutField" style="display: none;">
                                    <label for="hour_checkout">Hora de Salida <span class="text-danger">*</span></label>
                                    <input type="time" name="hour_checkout" id="hour_checkout"
                                        class="form-control @error('hour_checkout') is-invalid @enderror"
                                        value="{{ old('hour_checkout') }}">
                                    @error('hour_checkout')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Precio <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price') }}" step="0.01" min="0" required>
                                    @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar
                            Tarifa</button>
                    </div>
                </form>
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

        #tariffs-table_wrapper .dt-buttons {
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
            // Inicializar DataTable y guardar la referencia en una variable
            var table = $('#tariffs-table').DataTable({
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

            // Añadir los botones al contenedor correcto
            table.buttons().container().appendTo('#tariffs-table_wrapper .col-md-12:eq(1)');

            // Función para alternar visibilidad de campos (para el modal de agregar)
            function toggleFieldsAdd() {
                const type = document.getElementById('type').value;
                const durationField = document.getElementById('durationField');
                const hourCheckoutField = document.getElementById('hourCheckoutField');
                const durationInput = document.getElementById('duration');
                const hourCheckoutInput = document.getElementById('hour_checkout');

                console.log('Tipo seleccionado (Agregar):', type);

                if (type === 'HORA') {
                    console.log('Mostrando Duración, ocultando Hora de Salida (Agregar)');
                    durationField.style.display = 'block';
                    hourCheckoutField.style.display = 'none';
                    durationInput.setAttribute('required', 'required');
                    hourCheckoutInput.removeAttribute('required');
                    hourCheckoutInput.value = '';
                } else if (type === 'DIA') {
                    console.log('Mostrando Hora de Salida, ocultando Duración (Agregar)');
                    durationField.style.display = 'none';
                    hourCheckoutField.style.display = 'block';
                    durationInput.removeAttribute('required');
                    hourCheckoutInput.setAttribute('required', 'required');
                    durationInput.value = '';
                } else {
                    console.log('Ocultando ambos campos (Agregar)');
                    durationField.style.display = 'none';
                    hourCheckoutField.style.display = 'none';
                    durationInput.removeAttribute('required');
                    hourCheckoutInput.removeAttribute('required');
                    durationInput.value = '';
                    hourCheckoutInput.value = '';
                }
            }

            // Asignar el evento change al select del modal de agregar
            const typeSelectAdd = document.getElementById('type');
            typeSelectAdd.addEventListener('change', function() {
                console.log('Evento change disparado (Agregar)');
                toggleFieldsAdd();
            });

            // Inicializar visibilidad de campos al abrir el modal de agregar
            $('#addTariffModal').on('shown.bs.modal', function() {
                console.log('Modal de agregar abierto, inicializando campos');
                toggleFieldsAdd();
            });

            // Inicializar visibilidad de campos al cargar la página (para agregar)
            toggleFieldsAdd();

            // Función para alternar visibilidad de campos (para cada modal de editar)
            @foreach ($roomType->roomTypeTariffs as $roomTypeTariff)
                function toggleFieldsEdit{{ $roomTypeTariff->id }}() {
                    const type = document.getElementById('type{{ $roomTypeTariff->id }}').value;
                    const durationField = document.getElementById('durationField{{ $roomTypeTariff->id }}');
                    const hourCheckoutField = document.getElementById(
                    'hourCheckoutField{{ $roomTypeTariff->id }}');
                    const durationInput = document.getElementById('duration{{ $roomTypeTariff->id }}');
                    const hourCheckoutInput = document.getElementById('hour_checkout{{ $roomTypeTariff->id }}');

                    console.log('Tipo seleccionado (Editar {{ $roomTypeTariff->id }}):', type);

                    if (type === 'HORA') {
                        console.log(
                            'Mostrando Duración, ocultando Hora de Salida (Editar {{ $roomTypeTariff->id }})');
                        durationField.style.display = 'block';
                        hourCheckoutField.style.display = 'none';
                        durationInput.setAttribute('required', 'required');
                        hourCheckoutInput.removeAttribute('required');
                        hourCheckoutInput.value = '';
                    } else if (type === 'DIA') {
                        console.log(
                            'Mostrando Hora de Salida, ocultando Duración (Editar {{ $roomTypeTariff->id }})');
                        durationField.style.display = 'none';
                        hourCheckoutField.style.display = 'block';
                        durationInput.removeAttribute('required');
                        hourCheckoutInput.setAttribute('required', 'required');
                        durationInput.value = '';
                    } else {
                        console.log('Ocultando ambos campos (Editar {{ $roomTypeTariff->id }})');
                        durationField.style.display = 'none';
                        hourCheckoutField.style.display = 'none';
                        durationInput.removeAttribute('required');
                        hourCheckoutInput.removeAttribute('required');
                        durationInput.value = '';
                        hourCheckoutInput.value = '';
                    }
                }

                // Asignar el evento change al select del modal de editar
                document.getElementById('type{{ $roomTypeTariff->id }}').addEventListener('change', function() {
                    console.log('Evento change disparado (Editar {{ $roomTypeTariff->id }})');
                    toggleFieldsEdit{{ $roomTypeTariff->id }}();
                });

                // Inicializar visibilidad de campos al abrir el modal de editar
                $('#editTariffModal{{ $roomTypeTariff->id }}').on('shown.bs.modal', function() {
                    console.log('Modal de editar {{ $roomTypeTariff->id }} abierto, inicializando campos');
                    toggleFieldsEdit{{ $roomTypeTariff->id }}();
                });
            @endforeach
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop
