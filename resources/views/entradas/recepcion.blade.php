{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Registrar Entrada {{ $room ? '- Habitación ' . $room->room_number : '' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('entradas.store') }}" method="POST" id="entry-form">
                @csrf
                @if ($room)
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="room_type_id" value="{{ $room->roomType->id }}">
                    <input type="hidden" id="price_4_hours" value="{{ $room->roomType->price_4_hours ?? 0 }}">
                    <input type="hidden" id="price_full_night" value="{{ $room->roomType->price_full_night ?? 0 }}">
                    <input type="hidden" id="price_month" value="{{ $room->roomType->price_month ?? 0 }}">
                @endif

                <!-- Selección de Habitación (si no se pasó una habitación) -->
                @if (!$room)
                    <div class="form-group">
                        <label for="room_id">Habitación</label>
                        <select class="form-control select2 @error('room_id') is-invalid @enderror" name="room_id"
                            id="room_id" required>
                            <option value="">Seleccionar Habitación</option>
                            @foreach (\App\Models\Room::with('roomType')->get() as $r)
                                <option value="{{ $r->id }}" data-room-type-id="{{ $r->roomType->id }}"
                                    data-price-4-hours="{{ $r->roomType->price_4_hours ?? 0 }}"
                                    data-price-full-night="{{ $r->roomType->price_full_night ?? 0 }}"
                                    data-price-month="{{ $r->roomType->price_month ?? 0 }}"
                                    {{ old('room_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->room_number }} ({{ $r->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- 1. Card Habitación -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Habitación {{ $room ? $room->room_number : 'N/A' }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($room && $room->roomType)
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Precios:</strong><br>
                                    4 Horas: $ {{ $room->roomType->price_4_hours ?? 'N/A' }}<br>
                                    Toda la Noche: $ {{ $room->roomType->price_full_night ?? 'N/A' }}<br>
                                    Mes: $ {{ $room->roomType->price_month ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Estado:</strong><br>
                                    <span class="badge badge-success">Disponible</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Descripción:</strong><br>
                                    {{ $room->roomType->description ?? 'N/A' }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No se ha seleccionado una habitación.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Cliente -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cliente</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Cliente</label>
                                    <select class="form-control select2 @error('client_id') is-invalid @enderror"
                                        name="client_id" id="client_id" required>
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $selectedClientId) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} {{ $client->lastname }} (Email:
                                                {{ $client->email ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="button" class="btn btn-primary btn-block"
                                        id="register-client-btn">Registrar Cliente</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_type">Tarifa / Precio</label>
                                    <select class="form-control select2 @error('entry_type') is-invalid @enderror"
                                        name="entry_type" id="entry_type" required>
                                        <option value="">Seleccionar Tarifa/Precio</option>
                                        <option value="4_hours" {{ old('entry_type') == '4_hours' ? 'selected' : '' }}>4
                                            Horas</option>
                                        <option value="full_night"
                                            {{ old('entry_type') == 'full_night' ? 'selected' : '' }}>Toda la Noche
                                        </option>
                                        <option value="month" {{ old('entry_type') == 'month' ? 'selected' : '' }}>Por Mes
                                        </option>
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_date">Fecha de Entrada</label>
                                    <input type="date" class="form-control @error('check_in_date') is-invalid @enderror"
                                        name="check_in_date" id="check_in_date"
                                        value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_in_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">Cantidad</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                                        required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_time">Hora de Entrada</label>
                                    <input type="time"
                                        class="form-control @error('check_in_time') is-invalid @enderror"
                                        name="check_in_time" id="check_in_time"
                                        value="{{ old('check_in_time', now()->format('H:i')) }}" required>
                                    @error('check_in_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_date">Fecha de Salida</label>
                                    <input type="date"
                                        class="form-control @error('check_out_date') is-invalid @enderror"
                                        name="check_out_date" id="check_out_date"
                                        value="{{ old('check_out_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_out_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_time">Hora de Salida</label>
                                    <input type="time"
                                        class="form-control @error('check_out_time') is-invalid @enderror"
                                        name="check_out_time" id="check_out_time"
                                        value="{{ old('check_out_time', now()->addHours(4)->format('H:i')) }}" required>
                                    @error('check_out_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Card Registrar Pago -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Pago</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="efectivo">Efectivo</label>
                                    <input type="number" class="form-control @error('efectivo') is-invalid @enderror"
                                        name="efectivo" id="efectivo" value="{{ old('efectivo', 0) }}" step="0.01"
                                        min="0">
                                    @error('efectivo')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mercadopago">Mercadopago</label>
                                    <input type="number" class="form-control @error('mercadopago') is-invalid @enderror"
                                        name="mercadopago" id="mercadopago" value="{{ old('mercadopago', 0) }}"
                                        step="0.01" min="0">
                                    @error('mercadopago')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tarjeta">Tarjeta</label>
                                    <input type="number" class="form-control @error('tarjeta') is-invalid @enderror"
                                        name="tarjeta" id="tarjeta" value="{{ old('tarjeta', 0) }}" step="0.01"
                                        min="0">
                                    @error('tarjeta')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transferencia">Transferencia</label>
                                    <input type="number"
                                        class="form-control @error('transferencia') is-invalid @enderror"
                                        name="transferencia" id="transferencia" value="{{ old('transferencia', 0) }}"
                                        step="0.01" min="0">
                                    @error('transferencia')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount">Descuento</label>
                                    <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                        name="discount" id="discount" value="{{ old('discount', 0) }}" step="0.01"
                                        min="0">
                                    @error('discount')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total">Total</label>
                                    <input type="number" class="form-control @error('total') is-invalid @enderror"
                                        name="total" id="total" value="{{ old('total', 0) }}" step="0.01"
                                        readonly>
                                    @error('total')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="debt">A Deuda</label>
                                    <input type="number" class="form-control @error('debt') is-invalid @enderror"
                                        name="debt" id="debt" value="{{ old('debt', 0) }}" step="0.01"
                                        readonly>
                                    @error('debt')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tercera fila -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observations">Observaciones</label>
                                    <textarea class="form-control @error('observations') is-invalid @enderror" name="observations" id="observations"
                                        placeholder="Escribe aquí algún detalle que desees registrar.">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Registrar Acompañante -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Acompañante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-companion-btn">
                                <i class="fas fa-plus"></i> Registrar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm" id="companions-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones finales -->
                <div class="row mb-5">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger">Volver</a>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Registrar Acompañante -->
    <div class="modal fade" id="companion-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Acompañante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_companion_name">Nombre</label>
                        <input type="text" class="form-control" id="modal_companion_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_lastname">Apellido</label>
                        <input type="text" class="form-control" id="modal_companion_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_dni">DNI</label>
                        <input type="text" class="form-control" id="modal_companion_dni" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_phone">Teléfono</label>
                        <input type="text" class="form-control" id="modal_companion_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_email">Email</label>
                        <input type="email" class="form-control" id="modal_companion_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-companion-btn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Cliente -->
    <div class="modal fade" id="client-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_client_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_lastname">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_tipo_id">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="modal_client_tipo_id" style="width: 100%" required>
                            <option value="">Seleccionar</option>
                            @foreach ($tipoDocumentos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_documento">N° de Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_nro_documento" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_matricula">N° de Matrícula</label>
                        <input type="text" class="form-control" id="modal_client_nro_matricula">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_phone">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_email">Email</label>
                        <input type="email" class="form-control" id="modal_client_email">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_address">Dirección</label>
                        <input type="text" class="form-control" id="modal_client_address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-client-btn">Guardar</button>
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
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .card {
            border-radius: 0;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        #debt.danger {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        #debt.success {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.45/builds/moment-timezone-with-data.min.js"></script>
    <script>
        $(document).ready(function() {
            // Verificar carga de Moment y Moment-Timezone
            console.log('Moment cargado:', typeof moment !== 'undefined' ? 'Sí' : 'No');
            console.log('Moment-Timezone cargado:', typeof moment.tz !== 'undefined' ? 'Sí' : 'No');

            // Inicializar Select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).attr('id') === 'client_id' ? 'Seleccionar Cliente' :
                        $(this).attr('id') === 'entry_type' ? 'Seleccionar Tarifa/Precio' :
                        $(this).attr('id') === 'room_id' ? 'Seleccionar Habitación' : 'Seleccionar';
                },
                allowClear: true,
                width: '100%'
            });

            // Lista de acompañantes
            let companions = [];

            // Variable para rastrear si el usuario ha modificado manualmente la fecha/hora de salida
            let isCheckOutModified = false;

            // Detectar cambios manuales en check_out_date y check_out_time
            $('#check_out_date, #check_out_time').on('change', function() {
                isCheckOutModified = true;
                console.log('Fecha u hora de salida modificada manualmente');
            });

            // Actualizar precios y fechas de salida
            function updatePricesAndDates() {
                try {
                    console.log('Iniciando updatePricesAndDates...');

                    const entryType = $('#entry_type').val();
                    console.log('Tarifa seleccionada:', entryType);

                    const quantity = parseInt($('#quantity').val()) || 1;
                    console.log('Cantidad:', quantity);

                    // Obtener precios (usar campos ocultos si $room está definido, sino usar el select)
                    let price4Hours, priceFullNight, priceMonth;
                    if ($('#price_4_hours').length) {
                        price4Hours = parseFloat($('#price_4_hours').val()) || 0;
                        priceFullNight = parseFloat($('#price_full_night').val()) || 0;
                        priceMonth = parseFloat($('#price_month').val()) || 0;
                    } else {
                        const selectedRoom = $('#room_id option:selected');
                        price4Hours = parseFloat(selectedRoom.data('price-4-hours')) || 0;
                        priceFullNight = parseFloat(selectedRoom.data('price-full-night')) || 0;
                        priceMonth = parseFloat(selectedRoom.data('price-month')) || 0;
                    }
                    console.log('Precios:', {
                        price4Hours,
                        priceFullNight,
                        priceMonth
                    });

                    // Obtener fechas y horas
                    const checkInDate = $('#check_in_date').val();
                    const checkInTime = $('#check_in_time').val();
                    console.log('Fecha y hora de entrada:', {
                        checkInDate,
                        checkInTime
                    });

                    // Validar entradas
                    if (!checkInDate || !checkInTime || !entryType) {
                        console.log('Faltan datos:', {
                            checkInDate,
                            checkInTime,
                            entryType
                        });
                        return;
                    }

                    const dateTimeString = `${checkInDate} ${checkInTime}`;
                    if (!moment(dateTimeString, 'YYYY-MM-DD HH:mm', true).isValid()) {
                        console.log('Formato de fecha/hora inválido:', dateTimeString);
                        return;
                    }

                    // Crear fecha de entrada
                    let checkIn;
                    if (typeof moment.tz !== 'undefined') {
                        // Usar moment-timezone si está disponible
                        checkIn = moment.tz(dateTimeString, 'YYYY-MM-DD HH:mm', 'America/Argentina/Buenos_Aires');
                    } else {
                        // Fallback: Usar moment con desplazamiento manual (UTC-3)
                        console.warn('Moment-Timezone no disponible, usando desplazamiento manual (UTC-3)...');
                        checkIn = moment(dateTimeString, 'YYYY-MM-DD HH:mm').utcOffset(-
                            180); // -180 minutos = UTC-3
                    }
                    console.log('Check-in (moment):', checkIn.format());

                    let checkOut;
                    let price = 0;

                    if (entryType === '4_hours') {
                        price = price4Hours * quantity;
                        checkOut = checkIn.clone().add(4 * quantity, 'hours');
                    } else if (entryType === 'full_night') {
                        price = priceFullNight * quantity;
                        checkOut = checkIn.clone().add(quantity, 'days').set({
                            hour: 11,
                            minute: 0
                        });
                    } else if (entryType === 'month') {
                        price = priceMonth * quantity;
                        checkOut = checkIn.clone().add(quantity, 'months');
                    }

                    // Actualizar campos de salida solo si no han sido modificados manualmente
                    if (checkOut && !isCheckOutModified) {
                        $('#check_out_date').val(checkOut.format('YYYY-MM-DD'));
                        $('#check_out_time').val(checkOut.format('HH:mm'));
                        console.log('Check-out actualizado automáticamente:', checkOut.format(), 'Precio:', price);
                    } else if (!checkOut) {
                        console.log('checkOut no definido para entryType:', entryType);
                    } else {
                        console.log('Check-out no actualizado (modificado manualmente)');
                    }

                    // Guardar el precio para updatePayment
                    $('#total').data('base-price', price);
                    updatePayment();
                } catch (error) {
                    console.error('Error en updatePricesAndDates:', error);
                }
            }

            // Actualizar cálculo de pago
            function updatePayment() {
                try {
                    console.log('Iniciando updatePayment...');

                    const discount = parseFloat($('#discount').val()) || 0;
                    const efectivo = parseFloat($('#efectivo').val()) || 0;
                    const mercadopago = parseFloat($('#mercadopago').val()) || 0;
                    const tarjeta = parseFloat($('#tarjeta').val()) || 0;
                    const transferencia = parseFloat($('#transferencia').val()) || 0;

                    const price = parseFloat($('#total').data('base-price')) || 0;

                    console.log('Datos de pago:', {
                        price,
                        discount,
                        efectivo,
                        mercadopago,
                        tarjeta,
                        transferencia
                    });

                    const total = price - discount;
                    const totalPagado = efectivo + mercadopago + tarjeta + transferencia;
                    const debt = total - totalPagado;

                    $('#total').val(total.toFixed(2));
                    $('#debt').val(debt.toFixed(2));

                    if (debt > 0) {
                        $('#debt').removeClass('success').addClass('danger');
                    } else {
                        $('#debt').removeClass('danger').addClass('success');
                    }
                } catch (error) {
                    console.error('Error en updatePayment:', error);
                }
            }

            // Escuchar cambios para actualizar precios y fechas
            $('#entry_type, #quantity, #check_in_date, #check_in_time').on('change', function() {
                console.log('Evento change disparado en:', this.id);
                updatePricesAndDates();
            });

            // Escuchar cambios en los campos de pago
            $('#discount, #efectivo, #mercadopago, #tarjeta, #transferencia').on('input', function() {
                console.log('Evento input disparado en:', this.id);
                updatePayment();
            });

            // Inicializar precios y fechas al cargar la página
            updatePricesAndDates();

            // Forzar actualización si entry_type ya tiene un valor
            if ($('#entry_type').val()) {
                console.log('Forzando actualización inicial de entry_type...');
                $('#entry_type').trigger('change');
            }

            // Abrir modal para registrar cliente
            $('#register-client-btn').click(function() {
                $('#client-modal').modal('show');
            });

            // Guardar cliente desde el modal
            $('#save-client-btn').click(function() {
                const client = {
                    name: $('#modal_client_name').val(),
                    lastname: $('#modal_client_lastname').val(),
                    tipo_id: $('#modal_client_tipo_id').val(),
                    nro_documento: $('#modal_client_nro_documento').val(),
                    nro_matricula: $('#modal_client_nro_matricula').val(),
                    phone: $('#modal_client_phone').val(),
                    email: $('#modal_client_email').val(),
                    address: $('#modal_client_address').val()
                };

                if (!client.name || !client.lastname || !client.tipo_id || !client.nro_documento || !client
                    .phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('clientes.storeAjax') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: client.name,
                        lastname: client.lastname,
                        tipo_id: client.tipo_id,
                        nro_documento: client.nro_documento,
                        nro_matricula: client.nro_matricula,
                        phone: client.phone,
                        email: client.email,
                        address: client.address
                    },
                    success: function(response) {
                        if (response.success) {
                            const newClient = response.client;
                            const option = new Option(
                                `${newClient.name} ${newClient.lastname} (Email: ${newClient.email ?? 'N/A'})`,
                                newClient.id,
                                true,
                                true
                            );
                            $('#client_id').append(option).trigger('change');
                            $('#client-modal').modal('hide');
                            $('#modal_client_name, #modal_client_lastname, #modal_client_tipo_id, #modal_client_nro_documento, #modal_client_nro_matricula, #modal_client_phone, #modal_client_email, #modal_client_address')
                                .val('');
                            $('#modal_client_tipo_id').val('').trigger(
                                'change'); // Limpiar Select2
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Cliente registrado correctamente.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'No se pudo registrar el cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            timer: 2500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Abrir modal para registrar acompañante
            $('#add-companion-btn').click(function() {
                $('#companion-modal').modal('show');
            });

            // Guardar acompañante desde el modal
            $('#save-companion-btn').click(function() {
                const companion = {
                    name: $('#modal_companion_name').val(),
                    lastname: $('#modal_companion_lastname').val(),
                    dni: $('#modal_companion_dni').val(),
                    phone: $('#modal_companion_phone').val(),
                    email: $('#modal_companion_email').val()
                };

                if (!companion.name || !companion.lastname || !companion.dni || !companion.phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                companions.push(companion);
                updateCompanionsTable();
                $('#companion-modal').modal('hide');
                $('#modal_companion_name, #modal_companion_lastname, #modal_companion_dni, #modal_companion_phone, #modal_companion_email')
                    .val('');
            });

            // Actualizar la tabla de acompañantes
            function updateCompanionsTable() {
                const tbody = $('#companions-table tbody');
                tbody.empty();
                companions.forEach((companion, index) => {
                    tbody.append(`
                        <tr>
                            <td>${companion.name}</td>
                            <td>${companion.lastname}</td>
                            <td>${companion.dni}</td>
                            <td>${companion.phone}</td>
                            <td>${companion.email || ''}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-companion" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                $('#entry-form').append('<input type="hidden" name="companions" id="companions-data">');
                $('#companions-data').val(JSON.stringify(companions));
            }

            // Eliminar acompañante
            $(document).on('click', '.remove-companion', function() {
                const index = $(this).data('index');
                companions.splice(index, 1);
                updateCompanionsTable();
            });

            // Enviar formulario
            $('#entry-form').on('submit', function() {
                $('#companions-data').val(JSON.stringify(companions));
            });
        });
    </script>
@stop --}}












































































































{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Registrar Entrada {{ $room ? '- Habitación ' . $room->room_number : '' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('entradas.store') }}" method="POST" id="entry-form">
                @csrf
                @if ($room)
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="room_type_id" value="{{ $room->roomType->id }}">
                @endif

                <!-- Selección de Habitación (si no se pasó una habitación) -->
                @if (!$room)
                    <div class="form-group">
                        <label for="room_id">Habitación</label>
                        <select class="form-control select2 @error('room_id') is-invalid @enderror" name="room_id"
                            id="room_id" required>
                            <option value="">Seleccionar Habitación</option>
                            @foreach (\App\Models\Room::with('roomType')->get() as $r)
                                <option value="{{ $r->id }}" data-room-type-id="{{ $r->roomType->id }}"
                                    {{ old('room_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->room_number }} ({{ $r->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- 1. Card Habitación -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Habitación {{ $room ? $room->room_number : 'N/A' }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($room && $room->roomType)
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Precios:</strong><br>
                                    @if ($room->roomType->roomTypeTariffs && $room->roomType->roomTypeTariffs->count() > 0)
                                        @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                            {{ $tariff->name }} ({{ $tariff->type }}): $
                                            {{ number_format($tariff->price, 2) }}<br>
                                        @endforeach
                                    @else
                                        No hay tarifas definidas.<br>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Estado:</strong><br>
                                    <span class="badge badge-success">Disponible</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Descripción:</strong><br>
                                    {{ $room->roomType->description ?? 'N/A' }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No se ha seleccionado una habitación.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Cliente -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cliente</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Cliente</label>
                                    <select class="form-control select2 @error('client_id') is-invalid @enderror"
                                        name="client_id" id="client_id" required>
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $selectedClientId) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} {{ $client->client->lastname ?? 'N/A' }} (Email:
                                                {{ $client->email ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="button" class="btn btn-primary btn-block"
                                        id="register-client-btn">Registrar Cliente</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_type">Tarifa / Precio</label>
                                    <select class="form-control select2 @error('entry_type') is-invalid @enderror"
                                        name="entry_type" id="entry_type" required>
                                        <option value="">Seleccionar Tarifa/Precio</option>
                                        @if ($room && $room->roomType && $room->roomType->roomTypeTariffs)
                                            @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                                <option value="{{ $tariff->id }}" data-type="{{ $tariff->type }}"
                                                    data-price="{{ $tariff->price }}"
                                                    data-duration="{{ $tariff->duration }}"
                                                    data-hour-checkout="{{ $tariff->hour_checkout }}"
                                                    {{ old('entry_type') == $tariff->id ? 'selected' : '' }}>
                                                    {{ $tariff->name }} ({{ $tariff->type }} - $
                                                    {{ number_format($tariff->price, 2) }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_date">Fecha de Entrada</label>
                                    <input type="date" class="form-control @error('check_in_date') is-invalid @enderror"
                                        name="check_in_date" id="check_in_date"
                                        value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_in_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">Cantidad</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                                        required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_time">Hora de Entrada</label>
                                    <input type="time"
                                        class="form-control @error('check_in_time') is-invalid @enderror"
                                        name="check_in_time" id="check_in_time"
                                        value="{{ old('check_in_time', now()->format('H:i')) }}" required>
                                    @error('check_in_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_date">Fecha de Salida</label>
                                    <input type="date"
                                        class="form-control @error('check_out_date') is-invalid @enderror"
                                        name="check_out_date" id="check_out_date"
                                        value="{{ old('check_out_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_out_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_time">Hora de Salida</label>
                                    <input type="time"
                                        class="form-control @error('check_out_time') is-invalid @enderror"
                                        name="check_out_time" id="check_out_time"
                                        value="{{ old('check_out_time', now()->addHours(4)->format('H:i')) }}" required>
                                    @error('check_out_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Card Registrar Pago -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Pago</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="efectivo">Efectivo</label>
                                    <input type="number" class="form-control @error('efectivo') is-invalid @enderror"
                                        name="efectivo" id="efectivo" value="{{ old('efectivo', 0) }}" step="0.01"
                                        min="0">
                                    @error('efectivo')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mercadopago">Mercadopago</label>
                                    <input type="number" class="form-control @error('mercadopago') is-invalid @enderror"
                                        name="mercadopago" id="mercadopago" value="{{ old('mercadopago', 0) }}"
                                        step="0.01" min="0">
                                    @error('mercadopago')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tarjeta">Tarjeta</label>
                                    <input type="number" class="form-control @error('tarjeta') is-invalid @enderror"
                                        name="tarjeta" id="tarjeta" value="{{ old('tarjeta', 0) }}" step="0.01"
                                        min="0">
                                    @error('tarjeta')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transferencia">Transferencia</label>
                                    <input type="number"
                                        class="form-control @error('transferencia') is-invalid @enderror"
                                        name="transferencia" id="transferencia" value="{{ old('transferencia', 0) }}"
                                        step="0.01" min="0">
                                    @error('transferencia')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount">Descuento</label>
                                    <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                        name="discount" id="discount" value="{{ old('discount', 0) }}" step="0.01"
                                        min="0">
                                    @error('discount')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total">Total</label>
                                    <input type="number" class="form-control @error('total') is-invalid @enderror"
                                        name="total" id="total" value="{{ old('total', 0) }}" step="0.01"
                                        readonly>
                                    @error('total')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="debt">A Deuda</label>
                                    <input type="number" class="form-control @error('debt') is-invalid @enderror"
                                        name="debt" id="debt" value="{{ old('debt', 0) }}" step="0.01"
                                        readonly>
                                    @error('debt')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tercera fila -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observations">Observaciones</label>
                                    <textarea class="form-control @error('observations') is-invalid @enderror" name="observations" id="observations"
                                        placeholder="Escribe aquí algún detalle que desees registrar.">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Registrar Acompañante -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Acompañante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-companion-btn">
                                <i class="fas fa-plus"></i> Registrar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm" id="companions-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones finales -->
                <div class="row mb-5">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger">Volver</a>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Registrar Acompañante -->
    <div class="modal fade" id="companion-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Acompañante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_companion_name">Nombre</label>
                        <input type="text" class="form-control" id="modal_companion_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_lastname">Apellido</label>
                        <input type="text" class="form-control" id="modal_companion_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_dni">DNI</label>
                        <input type="text" class="form-control" id="modal_companion_dni" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_phone">Teléfono</label>
                        <input type="text" class="form-control" id="modal_companion_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_email">Email</label>
                        <input type="email" class="form-control" id="modal_companion_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-companion-btn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Cliente -->
    <div class="modal fade" id="client-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_client_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_lastname">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_tipo_id">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="modal_client_tipo_id" style="width: 100%" required>
                            <option value="">Seleccionar</option>
                            @foreach ($tipoDocumentos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_documento">N° de Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_nro_documento" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_matricula">N° de Matrícula</label>
                        <input type="text" class="form-control" id="modal_client_nro_matricula">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_phone">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_email">Email</label>
                        <input type="email" class="form-control" id="modal_client_email">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_address">Dirección</label>
                        <input type="text" class="form-control" id="modal_client_address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-client-btn">Guardar</button>
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
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .card {
            border-radius: 0;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        #debt.danger {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        #debt.success {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.45/builds/moment-timezone-with-data.min.js"></script>
    <script>
        $(document).ready(function() {
            // Verificar carga de Moment y Moment-Timezone
            console.log('Moment cargado:', typeof moment !== 'undefined' ? 'Sí' : 'No');
            console.log('Moment-Timezone cargado:', typeof moment.tz !== 'undefined' ? 'Sí' : 'No');

            // Inicializar Select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).attr('id') === 'client_id' ? 'Seleccionar Cliente' :
                        $(this).attr('id') === 'entry_type' ? 'Seleccionar Tarifa/Precio' :
                        $(this).attr('id') === 'room_id' ? 'Seleccionar Habitación' : 'Seleccionar';
                },
                allowClear: true,
                width: '100%'
            });

            // Lista de acompañantes
            let companions = [];

            // Variable para rastrear si el usuario ha modificado manualmente la fecha/hora de salida
            let isCheckOutModified = false;

            // Detectar cambios manuales en check_out_date y check_out_time
            $('#check_out_date, #check_out_time').on('change', function() {
                isCheckOutModified = true;
                console.log('Fecha u hora de salida modificada manualmente');
            });

            // Actualizar precios y fechas de salida
            function updatePricesAndDates() {
                try {
                    console.log('Iniciando updatePricesAndDates...');

                    const entryTypeId = $('#entry_type').val();
                    console.log('Tarifa seleccionada (ID):', entryTypeId);

                    const quantity = parseInt($('#quantity').val()) || 1;
                    console.log('Cantidad:', quantity);

                    // Obtener datos de la tarifa seleccionada
                    const selectedTariff = $('#entry_type option:selected');
                    const tariffType = selectedTariff.data('type');
                    const price = parseFloat(selectedTariff.data('price')) || 0;
                    const duration = parseInt(selectedTariff.data('duration')) || 0;
                    const hourCheckout = selectedTariff.data('hour-checkout') || '11:00';

                    console.log('Datos de la tarifa:', {
                        tariffType,
                        price,
                        duration,
                        hourCheckout
                    });

                    // Obtener fechas y horas
                    const checkInDate = $('#check_in_date').val();
                    const checkInTime = $('#check_in_time').val();
                    console.log('Fecha y hora de entrada:', {
                        checkInDate,
                        checkInTime
                    });

                    // Validar entradas
                    if (!checkInDate || !checkInTime || !entryTypeId) {
                        console.log('Faltan datos:', {
                            checkInDate,
                            checkInTime,
                            entryTypeId
                        });
                        return;
                    }

                    const dateTimeString = `${checkInDate} ${checkInTime}`;
                    if (!moment(dateTimeString, 'YYYY-MM-DD HH:mm', true).isValid()) {
                        console.log('Formato de fecha/hora inválido:', dateTimeString);
                        return;
                    }

                    // Crear fecha de entrada
                    let checkIn;
                    if (typeof moment.tz !== 'undefined') {
                        checkIn = moment.tz(dateTimeString, 'YYYY-MM-DD HH:mm', 'America/Argentina/Buenos_Aires');
                    } else {
                        console.warn('Moment-Timezone no disponible, usando desplazamiento manual (UTC-3)...');
                        checkIn = moment(dateTimeString, 'YYYY-MM-DD HH:mm').utcOffset(-180);
                    }
                    console.log('Check-in (moment):', checkIn.format());

                    let checkOut;
                    let totalPrice = price * quantity;

                    if (tariffType === 'HORA') {
                        // Para tarifas por hora, sumar las horas según la duración y la cantidad
                        checkOut = checkIn.clone().add(duration * quantity, 'hours');
                    } else if (tariffType === 'DIA') {
                        // Para tarifas por día, sumar días y establecer la hora de salida
                        checkOut = checkIn.clone().add(quantity, 'days');
                        const [hour, minute] = hourCheckout.split(':');
                        checkOut.set({
                            hour: parseInt(hour),
                            minute: parseInt(minute)
                        });
                    } else {
                        console.log('Tipo de tarifa no reconocido:', tariffType);
                        return;
                    }

                    // Actualizar campos de salida solo si no han sido modificados manualmente
                    if (checkOut && !isCheckOutModified) {
                        $('#check_out_date').val(checkOut.format('YYYY-MM-DD'));
                        $('#check_out_time').val(checkOut.format('HH:mm'));
                        console.log('Check-out actualizado automáticamente:', checkOut.format(), 'Precio total:',
                            totalPrice);
                    } else if (!checkOut) {
                        console.log('checkOut no definido para tariffType:', tariffType);
                    } else {
                        console.log('Check-out no actualizado (modificado manualmente)');
                    }

                    // Guardar el precio para updatePayment
                    $('#total').data('base-price', totalPrice);
                    updatePayment();
                } catch (error) {
                    console.error('Error en updatePricesAndDates:', error);
                }
            }

            // Actualizar cálculo de pago
            function updatePayment() {
                try {
                    console.log('Iniciando updatePayment...');

                    const discount = parseFloat($('#discount').val()) || 0;
                    const efectivo = parseFloat($('#efectivo').val()) || 0;
                    const mercadopago = parseFloat($('#mercadopago').val()) || 0;
                    const tarjeta = parseFloat($('#tarjeta').val()) || 0;
                    const transferencia = parseFloat($('#transferencia').val()) || 0;

                    const price = parseFloat($('#total').data('base-price')) || 0;

                    console.log('Datos de pago:', {
                        price,
                        discount,
                        efectivo,
                        mercadopago,
                        tarjeta,
                        transferencia
                    });

                    const total = price - discount;
                    const totalPagado = efectivo + mercadopago + tarjeta + transferencia;
                    const debt = total - totalPagado;

                    $('#total').val(total.toFixed(2));
                    $('#debt').val(debt.toFixed(2));

                    if (debt > 0) {
                        $('#debt').removeClass('success').addClass('danger');
                    } else {
                        $('#debt').removeClass('danger').addClass('success');
                    }
                } catch (error) {
                    console.error('Error en updatePayment:', error);
                }
            }

            // Escuchar cambios para actualizar precios y fechas
            $('#entry_type, #quantity, #check_in_date, #check_in_time').on('change', function() {
                console.log('Evento change disparado en:', this.id);
                updatePricesAndDates();
            });

            // Escuchar cambios en los campos de pago
            $('#discount, #efectivo, #mercadopago, #tarjeta, #transferencia').on('input', function() {
                console.log('Evento input disparado en:', this.id);
                updatePayment();
            });

            // Inicializar precios y fechas al cargar la página
            updatePricesAndDates();

            // Forzar actualización si entry_type ya tiene un valor
            if ($('#entry_type').val()) {
                console.log('Forzando actualización inicial de entry_type...');
                $('#entry_type').trigger('change');
            }

            // Abrir modal para registrar cliente
            $('#register-client-btn').click(function() {
                $('#client-modal').modal('show');
            });

            // Guardar cliente desde el modal
            $('#save-client-btn').click(function() {
                const client = {
                    name: $('#modal_client_name').val(),
                    lastname: $('#modal_client_lastname').val(),
                    tipo_id: $('#modal_client_tipo_id').val(),
                    nro_documento: $('#modal_client_nro_documento').val(),
                    nro_matricula: $('#modal_client_nro_matricula').val(),
                    phone: $('#modal_client_phone').val(),
                    email: $('#modal_client_email').val(),
                    address: $('#modal_client_address').val()
                };

                if (!client.name || !client.lastname || !client.tipo_id || !client.nro_documento || !client
                    .phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('clientes.storeAjax') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: client.name,
                        lastname: client.lastname,
                        tipo_id: client.tipo_id,
                        nro_documento: client.nro_documento,
                        nro_matricula: client.nro_matricula,
                        phone: client.phone,
                        email: client.email,
                        address: client.address
                    },
                    success: function(response) {
                        if (response.success) {
                            const newClient = response.client;
                            const option = new Option(
                                `${newClient.name} ${newClient.lastname} (Email: ${newClient.email ?? 'N/A'})`,
                                newClient.id,
                                true,
                                true
                            );
                            $('#client_id').append(option).trigger('change');
                            $('#client-modal').modal('hide');
                            $('#modal_client_name, #modal_client_lastname, #modal_client_tipo_id, #modal_client_nro_documento, #modal_client_nro_matricula, #modal_client_phone, #modal_client_email, #modal_client_address')
                                .val('');
                            $('#modal_client_tipo_id').val('').trigger('change');
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Cliente registrado correctamente.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'No se pudo registrar el cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            timer: 2500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Abrir modal para registrar acompañante
            $('#add-companion-btn').click(function() {
                $('#companion-modal').modal('show');
            });

            // Guardar acompañante desde el modal
            $('#save-companion-btn').click(function() {
                const companion = {
                    name: $('#modal_companion_name').val(),
                    lastname: $('#modal_companion_lastname').val(),
                    dni: $('#modal_companion_dni').val(),
                    phone: $('#modal_companion_phone').val(),
                    email: $('#modal_companion_email').val()
                };

                if (!companion.name || !companion.lastname || !companion.dni || !companion.phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                companions.push(companion);
                updateCompanionsTable();
                $('#companion-modal').modal('hide');
                $('#modal_companion_name, #modal_companion_lastname, #modal_companion_dni, #modal_companion_phone, #modal_companion_email')
                    .val('');
            });

            // Actualizar la tabla de acompañantes
            function updateCompanionsTable() {
                const tbody = $('#companions-table tbody');
                tbody.empty();
                companions.forEach((companion, index) => {
                    tbody.append(`
                        <tr>
                            <td>${companion.name}</td>
                            <td>${companion.lastname}</td>
                            <td>${companion.dni}</td>
                            <td>${companion.phone}</td>
                            <td>${companion.email || ''}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-companion" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                $('#entry-form').append('<input type="hidden" name="companions" id="companions-data">');
                $('#companions-data').val(JSON.stringify(companions));
            }

            // Eliminar acompañante
            $(document).on('click', '.remove-companion', function() {
                const index = $(this).data('index');
                companions.splice(index, 1);
                updateCompanionsTable();
            });

            // Enviar formulario
            $('#entry-form').on('submit', function() {
                $('#companions-data').val(JSON.stringify(companions));
            });
        });
    </script>
@stop --}}














































































































{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Registrar Entrada {{ $room ? '- Habitación ' . $room->room_number : '' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('entradas.store') }}" method="POST" id="entry-form">
                @csrf
                @if ($room)
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="room_type_id" value="{{ $room->roomType->id }}">
                @endif

                <!-- Selección de Habitación (si no se pasó una habitación) -->
                @if (!$room)
                    <div class="form-group">
                        <label for="room_id">Habitación</label>
                        <select class="form-control select2 @error('room_id') is-invalid @enderror" name="room_id"
                            id="room_id" required>
                            <option value="">Seleccionar Habitación</option>
                            @foreach (\App\Models\Room::with('roomType')->get() as $r)
                                <option value="{{ $r->id }}" data-room-type-id="{{ $r->roomType->id }}"
                                    {{ old('room_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->room_number }} ({{ $r->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- 1. Card Habitación -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Habitación {{ $room ? $room->room_number : 'N/A' }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($room && $room->roomType)
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Precios:</strong><br>
                                    @if ($room->roomType->roomTypeTariffs && $room->roomType->roomTypeTariffs->count() > 0)
                                        @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                            {{ $tariff->name }} ({{ $tariff->type }}): $
                                            {{ number_format($tariff->price, 2) }}<br>
                                        @endforeach
                                    @else
                                        No hay tarifas definidas.<br>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Estado:</strong><br>
                                    <span class="badge badge-success">Disponible</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Descripción:</strong><br>
                                    {{ $room->roomType->description ?? 'N/A' }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No se ha seleccionado una habitación.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Cliente -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cliente</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Cliente</label>
                                    <select class="form-control select2 @error('client_id') is-invalid @enderror"
                                        name="client_id" id="client_id" required>
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $selectedClientId) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} {{ $client->lastname ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="button" class="btn btn-primary btn-block"
                                        id="register-client-btn">Registrar Cliente</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_type">Tarifa / Precio</label>
                                    <select class="form-control select2 @error('entry_type') is-invalid @enderror"
                                        name="entry_type" id="entry_type" required>
                                        <option value="">Seleccionar Tarifa/Precio</option>
                                        @if ($room && $room->roomType && $room->roomType->roomTypeTariffs)
                                            @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                                <option value="{{ $tariff->id }}" data-type="{{ $tariff->type }}"
                                                    data-price="{{ $tariff->price }}"
                                                    data-duration="{{ $tariff->duration }}"
                                                    data-hour-checkout="{{ $tariff->hour_checkout }}"
                                                    {{ old('entry_type') == $tariff->id ? 'selected' : '' }}>
                                                    {{ $tariff->name }} ({{ $tariff->type }} - $
                                                    {{ number_format($tariff->price, 2) }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_date">Fecha de Entrada</label>
                                    <input type="date" class="form-control @error('check_in_date') is-invalid @enderror"
                                        name="check_in_date" id="check_in_date"
                                        value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_in_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">Cantidad</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                                        required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_time">Hora de Entrada</label>
                                    <input type="time"
                                        class="form-control @error('check_in_time') is-invalid @enderror"
                                        name="check_in_time" id="check_in_time"
                                        value="{{ old('check_in_time', now()->format('H:i')) }}" required>
                                    @error('check_in_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_date">Fecha de Salida</label>
                                    <input type="date"
                                        class="form-control @error('check_out_date') is-invalid @enderror"
                                        name="check_out_date" id="check_out_date"
                                        value="{{ old('check_out_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_out_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_time">Hora de Salida</label>
                                    <input type="time"
                                        class="form-control @error('check_out_time') is-invalid @enderror"
                                        name="check_out_time" id="check_out_time"
                                        value="{{ old('check_out_time', now()->addHours(4)->format('H:i')) }}" required>
                                    @error('check_out_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Card Registrar Pago -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Pago</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="efectivo">Efectivo</label>
                                    <input type="number" class="form-control @error('efectivo') is-invalid @enderror"
                                        name="efectivo" id="efectivo" value="{{ old('efectivo', 0) }}" step="0.01"
                                        min="0">
                                    @error('efectivo')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mercadopago">Mercadopago</label>
                                    <input type="number" class="form-control @error('mercadopago') is-invalid @enderror"
                                        name="mercadopago" id="mercadopago" value="{{ old('mercadopago', 0) }}"
                                        step="0.01" min="0">
                                    @error('mercadopago')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tarjeta">Tarjeta</label>
                                    <input type="number" class="form-control @error('tarjeta') is-invalid @enderror"
                                        name="tarjeta" id="tarjeta" value="{{ old('tarjeta', 0) }}" step="0.01"
                                        min="0">
                                    @error('tarjeta')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transferencia">Transferencia</label>
                                    <input type="number"
                                        class="form-control @error('transferencia') is-invalid @enderror"
                                        name="transferencia" id="transferencia" value="{{ old('transferencia', 0) }}"
                                        step="0.01" min="0">
                                    @error('transferencia')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount">Descuento</label>
                                    <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                        name="discount" id="discount" value="{{ old('discount', 0) }}" step="0.01"
                                        min="0">
                                    @error('discount')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total">Total</label>
                                    <input type="number" class="form-control @error('total') is-invalid @enderror"
                                        name="total" id="total" value="{{ old('total', 0) }}" step="0.01"
                                        readonly>
                                    @error('total')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="debt">A Deuda</label>
                                    <input type="number" class="form-control @error('debt') is-invalid @enderror"
                                        name="debt" id="debt" value="{{ old('debt', 0) }}" step="0.01"
                                        readonly>
                                    @error('debt')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tercera fila -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observations">Observaciones</label>
                                    <textarea class="form-control @error('observations') is-invalid @enderror" name="observations" id="observations"
                                        placeholder="Escribe aquí algún detalle que desees registrar.">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Registrar Acompañante -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Acompañante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-companion-btn">
                                <i class="fas fa-plus"></i> Registrar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm" id="companions-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones finales -->
                <div class="row mb-5">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger">Volver</a>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Registrar Acompañante -->
    <div class="modal fade" id="companion-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Acompañante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_companion_name">Nombre</label>
                        <input type="text" class="form-control" id="modal_companion_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_lastname">Apellido</label>
                        <input type="text" class="form-control" id="modal_companion_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_dni">DNI</label>
                        <input type="text" class="form-control" id="modal_companion_dni" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_phone">Teléfono</label>
                        <input type="text" class="form-control" id="modal_companion_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_email">Email</label>
                        <input type="email" class="form-control" id="modal_companion_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-companion-btn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Cliente -->
    <div class="modal fade" id="client-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_client_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_lastname">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_tipo_id">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="modal_client_tipo_id" style="width: 100%" required>
                            <option value="">Seleccionar</option>
                            @foreach ($tipoDocumentos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_documento">N° de Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_nro_documento" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_matricula">N° de Matrícula</label>
                        <input type="text" class="form-control" id="modal_client_nro_matricula">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_phone">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_email">Email</label>
                        <input type="email" class="form-control" id="modal_client_email">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_address">Dirección</label>
                        <input type="text" class="form-control" id="modal_client_address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-client-btn">Guardar</button>
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
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .card {
            border-radius: 0;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        #debt.danger {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        #debt.success {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.45/builds/moment-timezone-with-data.min.js"></script>
    <script>
        $(document).ready(function() {
            // Verificar carga de Moment y Moment-Timezone
            console.log('Moment cargado:', typeof moment !== 'undefined' ? 'Sí' : 'No');
            console.log('Moment-Timezone cargado:', typeof moment.tz !== 'undefined' ? 'Sí' : 'No');

            // Inicializar Select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).attr('id') === 'client_id' ? 'Seleccionar Cliente' :
                        $(this).attr('id') === 'entry_type' ? 'Seleccionar Tarifa/Precio' :
                        $(this).attr('id') === 'room_id' ? 'Seleccionar Habitación' : 'Seleccionar';
                },
                allowClear: true,
                width: '100%'
            });

            // Lista de acompañantes
            let companions = [];

            // Variable para rastrear si el usuario ha modificado manualmente la fecha/hora de salida
            let isCheckOutModified = false;

            // Detectar cambios manuales en check_out_date y check_out_time
            $('#check_out_date, #check_out_time').on('change', function() {
                isCheckOutModified = true;
                console.log('Fecha u hora de salida modificada manualmente');
            });

            // Actualizar precios y fechas de salida
            function updatePricesAndDates() {
                try {
                    console.log('Iniciando updatePricesAndDates...');

                    const entryTypeId = $('#entry_type').val();
                    console.log('Tarifa seleccionada (ID):', entryTypeId);

                    const quantity = parseInt($('#quantity').val()) || 1;
                    console.log('Cantidad:', quantity);

                    // Obtener datos de la tarifa seleccionada
                    const selectedTariff = $('#entry_type option:selected');
                    const tariffType = selectedTariff.data('type');
                    const price = parseFloat(selectedTariff.data('price')) || 0;
                    const duration = parseInt(selectedTariff.data('duration')) || 0;
                    const hourCheckout = selectedTariff.data('hour-checkout') || '11:00';

                    console.log('Datos de la tarifa:', {
                        tariffType,
                        price,
                        duration,
                        hourCheckout
                    });

                    // Obtener fechas y horas
                    const checkInDate = $('#check_in_date').val();
                    const checkInTime = $('#check_in_time').val();
                    console.log('Fecha y hora de entrada:', {
                        checkInDate,
                        checkInTime
                    });

                    // Validar entradas
                    if (!checkInDate || !checkInTime || !entryTypeId) {
                        console.log('Faltan datos:', {
                            checkInDate,
                            checkInTime,
                            entryTypeId
                        });
                        return;
                    }

                    const dateTimeString = `${checkInDate} ${checkInTime}`;
                    if (!moment(dateTimeString, 'YYYY-MM-DD HH:mm', true).isValid()) {
                        console.log('Formato de fecha/hora inválido:', dateTimeString);
                        return;
                    }

                    // Crear fecha de entrada
                    let checkIn;
                    if (typeof moment.tz !== 'undefined') {
                        checkIn = moment.tz(dateTimeString, 'YYYY-MM-DD HH:mm', 'America/Argentina/Buenos_Aires');
                    } else {
                        console.warn('Moment-Timezone no disponible, usando desplazamiento manual (UTC-3)...');
                        checkIn = moment(dateTimeString, 'YYYY-MM-DD HH:mm').utcOffset(-180);
                    }
                    console.log('Check-in (moment):', checkIn.format());

                    let checkOut;
                    let totalPrice = price * quantity;

                    if (tariffType === 'HORA') {
                        // Para tarifas por hora, sumar las horas según la duración y la cantidad
                        checkOut = checkIn.clone().add(duration * quantity, 'hours');
                    } else if (tariffType === 'DIA') {
                        // Para tarifas por día, sumar días y establecer la hora de salida
                        checkOut = checkIn.clone().add(quantity, 'days');
                        const [hour, minute] = hourCheckout.split(':');
                        checkOut.set({
                            hour: parseInt(hour),
                            minute: parseInt(minute)
                        });
                    } else {
                        console.log('Tipo de tarifa no reconocido:', tariffType);
                        return;
                    }

                    // Actualizar campos de salida solo si no han sido modificados manualmente
                    if (checkOut && !isCheckOutModified) {
                        $('#check_out_date').val(checkOut.format('YYYY-MM-DD'));
                        $('#check_out_time').val(checkOut.format('HH:mm'));
                        console.log('Check-out actualizado automáticamente:', checkOut.format(), 'Precio total:',
                            totalPrice);
                    } else if (!checkOut) {
                        console.log('checkOut no definido para tariffType:', tariffType);
                    } else {
                        console.log('Check-out no actualizado (modificado manualmente)');
                    }

                    // Guardar el precio para updatePayment
                    $('#total').data('base-price', totalPrice);
                    updatePayment();
                } catch (error) {
                    console.error('Error en updatePricesAndDates:', error);
                }
            }

            // Actualizar cálculo de pago
            function updatePayment() {
                try {
                    console.log('Iniciando updatePayment...');

                    const discount = parseFloat($('#discount').val()) || 0;
                    const efectivo = parseFloat($('#efectivo').val()) || 0;
                    const mercadopago = parseFloat($('#mercadopago').val()) || 0;
                    const tarjeta = parseFloat($('#tarjeta').val()) || 0;
                    const transferencia = parseFloat($('#transferencia').val()) || 0;

                    const price = parseFloat($('#total').data('base-price')) || 0;

                    console.log('Datos de pago:', {
                        price,
                        discount,
                        efectivo,
                        mercadopago,
                        tarjeta,
                        transferencia
                    });

                    const total = price - discount;
                    const totalPagado = efectivo + mercadopago + tarjeta + transferencia;
                    const debt = total - totalPagado;

                    $('#total').val(total.toFixed(2));
                    $('#debt').val(debt.toFixed(2));

                    if (debt > 0) {
                        $('#debt').removeClass('success').addClass('danger');
                    } else {
                        $('#debt').removeClass('danger').addClass('success');
                    }
                } catch (error) {
                    console.error('Error en updatePayment:', error);
                }
            }

            // Escuchar cambios para actualizar precios y fechas
            $('#entry_type, #quantity, #check_in_date, #check_in_time').on('change', function() {
                console.log('Evento change disparado en:', this.id);
                updatePricesAndDates();
            });

            // Escuchar cambios en los campos de pago
            $('#discount, #efectivo, #mercadopago, #tarjeta, #transferencia').on('input', function() {
                console.log('Evento input disparado en:', this.id);
                updatePayment();
            });

            // Inicializar precios y fechas al cargar la página
            updatePricesAndDates();

            // Forzar actualización si entry_type ya tiene un valor
            if ($('#entry_type').val()) {
                console.log('Forzando actualización inicial de entry_type...');
                $('#entry_type').trigger('change');
            }

            // Abrir modal para registrar cliente
            $('#register-client-btn').click(function() {
                $('#client-modal').modal('show');
            });

            // Guardar cliente desde el modal
            $('#save-client-btn').click(function() {
                const client = {
                    name: $('#modal_client_name').val(),
                    lastname: $('#modal_client_lastname').val(),
                    tipo_id: $('#modal_client_tipo_id').val(),
                    nro_documento: $('#modal_client_nro_documento').val(),
                    nro_matricula: $('#modal_client_nro_matricula').val(),
                    phone: $('#modal_client_phone').val(),
                    email: $('#modal_client_email').val(),
                    address: $('#modal_client_address').val()
                };

                if (!client.name || !client.lastname || !client.tipo_id || !client.nro_documento || !client
                    .phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('clientes.storeAjax') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: client.name,
                        lastname: client.lastname,
                        tipo_id: client.tipo_id,
                        nro_documento: client.nro_documento,
                        nro_matricula: client.nro_matricula,
                        phone: client.phone,
                        email: client.email,
                        address: client.address
                    },
                    success: function(response) {
                        if (response.success) {
                            const newClient = response.client;
                            const option = new Option(
                                `${newClient.name} ${newClient.lastname}`,
                                newClient.id,
                                true,
                                true
                            );
                            $('#client_id').append(option).trigger('change');
                            $('#client-modal').modal('hide');
                            $('#modal_client_name, #modal_client_lastname, #modal_client_tipo_id, #modal_client_nro_documento, #modal_client_nro_matricula, #modal_client_phone, #modal_client_email, #modal_client_address')
                                .val('');
                            $('#modal_client_tipo_id').val('').trigger('change');
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Cliente registrado correctamente.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'No se pudo registrar el cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            timer: 2500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Abrir modal para registrar acompañante
            $('#add-companion-btn').click(function() {
                $('#companion-modal').modal('show');
            });

            // Guardar acompañante desde el modal
            $('#save-companion-btn').click(function() {
                const companion = {
                    name: $('#modal_companion_name').val(),
                    lastname: $('#modal_companion_lastname').val(),
                    dni: $('#modal_companion_dni').val(),
                    phone: $('#modal_companion_phone').val(),
                    email: $('#modal_companion_email').val()
                };

                if (!companion.name || !companion.lastname || !companion.dni || !companion.phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                companions.push(companion);
                updateCompanionsTable();
                $('#companion-modal').modal('hide');
                $('#modal_companion_name, #modal_companion_lastname, #modal_companion_dni, #modal_companion_phone, #modal_companion_email')
                    .val('');
            });

            // Actualizar la tabla de acompañantes
            function updateCompanionsTable() {
                const tbody = $('#companions-table tbody');
                tbody.empty();
                companions.forEach((companion, index) => {
                    tbody.append(`
                        <tr>
                            <td>${companion.name}</td>
                            <td>${companion.lastname}</td>
                            <td>${companion.dni}</td>
                            <td>${companion.phone}</td>
                            <td>${companion.email || ''}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-companion" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                $('#entry-form').append('<input type="hidden" name="companions" id="companions-data">');
                $('#companions-data').val(JSON.stringify(companions));
            }

            // Eliminar acompañante
            $(document).on('click', '.remove-companion', function() {
                const index = $(this).data('index');
                companions.splice(index, 1);
                updateCompanionsTable();
            });

            // Enviar formulario
            $('#entry-form').on('submit', function() {
                $('#companions-data').val(JSON.stringify(companions));
            });
        });
    </script>
@stop --}}















































































































@extends('adminlte::page')

@section('content_header')
    <h1><b>Registrar Entrada {{ $room ? '- Habitación ' . $room->room_number : '' }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Mostrar mensaje de éxito -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('entradas.store') }}" method="POST" id="entry-form">
                @csrf
                @if ($room)
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="room_type_id" value="{{ $room->roomType->id }}">
                @endif

                <!-- Selección de Habitación (si no se pasó una habitación) -->
                @if (!$room)
                    <div class="form-group">
                        <label for="room_id">Habitación</label>
                        <select class="form-control select2 @error('room_id') is-invalid @enderror" name="room_id"
                            id="room_id" required>
                            <option value="">Seleccionar Habitación</option>
                            @foreach (\App\Models\Room::with('roomType')->get() as $r)
                                <option value="{{ $r->id }}" data-room-type-id="{{ $r->roomType->id }}"
                                    {{ old('room_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->room_number }} ({{ $r->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- 1. Card Habitación -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Habitación {{ $room ? $room->room_number : 'N/A' }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($room && $room->roomType)
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Precios:</strong><br>
                                    @if ($room->roomType->roomTypeTariffs && $room->roomType->roomTypeTariffs->count() > 0)
                                        @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                            {{ $tariff->name }} ({{ $tariff->type }}): $
                                            {{ number_format($tariff->price, 2) }}<br>
                                        @endforeach
                                    @else
                                        No hay tarifas definidas.<br>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Estado:</strong><br>
                                    <span class="badge badge-success">Disponible</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Descripción:</strong><br>
                                    {{ $room->roomType->description ?? 'N/A' }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No se ha seleccionado una habitación.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Cliente -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cliente</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Cliente</label>
                                    <select class="form-control select2 @error('client_id') is-invalid @enderror"
                                        name="client_id" id="client_id" required>
                                        <option value="">Seleccionar Cliente</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $selectedClientId) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} {{ $client->lastname ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="button" class="btn btn-primary btn-block"
                                        id="register-client-btn">Registrar Cliente</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_type">Tarifa / Precio</label>
                                    <select class="form-control select2 @error('entry_type') is-invalid @enderror"
                                        name="entry_type" id="entry_type" required>
                                        <option value="">Seleccionar Tarifa/Precio</option>
                                        @if ($room && $room->roomType && $room->roomType->roomTypeTariffs)
                                            @foreach ($room->roomType->roomTypeTariffs as $tariff)
                                                <option value="{{ $tariff->id }}" data-type="{{ $tariff->type }}"
                                                    data-price="{{ $tariff->price }}"
                                                    data-duration="{{ $tariff->duration }}"
                                                    data-hour-checkout="{{ $tariff->hour_checkout }}"
                                                    {{ old('entry_type') == $tariff->id ? 'selected' : '' }}>
                                                    {{ $tariff->name }} ({{ $tariff->type }} - $
                                                    {{ number_format($tariff->price, 2) }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_date">Fecha de Entrada</label>
                                    <input type="date" class="form-control @error('check_in_date') is-invalid @enderror"
                                        name="check_in_date" id="check_in_date"
                                        value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_in_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">Cantidad</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1"
                                        required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_in_time">Hora de Entrada</label>
                                    <input type="time"
                                        class="form-control @error('check_in_time') is-invalid @enderror"
                                        name="check_in_time" id="check_in_time"
                                        value="{{ old('check_in_time', now()->format('H:i')) }}" required>
                                    @error('check_in_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_date">Fecha de Salida</label>
                                    <input type="date"
                                        class="form-control @error('check_out_date') is-invalid @enderror"
                                        name="check_out_date" id="check_out_date"
                                        value="{{ old('check_out_date', now()->format('Y-m-d')) }}" required>
                                    @error('check_out_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="check_out_time">Hora de Salida</label>
                                    <input type="time"
                                        class="form-control @error('check_out_time') is-invalid @enderror"
                                        name="check_out_time" id="check_out_time"
                                        value="{{ old('check_out_time', now()->addHours(4)->format('H:i')) }}" required>
                                    @error('check_out_time')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Card Registrar Pago -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Pago</h3>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="efectivo">Efectivo</label>
                                    <input type="number" class="form-control @error('efectivo') is-invalid @enderror"
                                        name="efectivo" id="efectivo" value="{{ old('efectivo', 0) }}" step="0.01"
                                        min="0">
                                    @error('efectivo')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mercadopago">Mercadopago</label>
                                    <input type="number" class="form-control @error('mercadopago') is-invalid @enderror"
                                        name="mercadopago" id="mercadopago" value="{{ old('mercadopago', 0) }}"
                                        step="0.01" min="0">
                                    @error('mercadopago')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tarjeta">Tarjeta</label>
                                    <input type="number" class="form-control @error('tarjeta') is-invalid @enderror"
                                        name="tarjeta" id="tarjeta" value="{{ old('tarjeta', 0) }}" step="0.01"
                                        min="0">
                                    @error('tarjeta')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transferencia">Transferencia</label>
                                    <input type="number"
                                        class="form-control @error('transferencia') is-invalid @enderror"
                                        name="transferencia" id="transferencia" value="{{ old('transferencia', 0) }}"
                                        step="0.01" min="0">
                                    @error('transferencia')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Segunda fila -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount">Descuento</label>
                                    <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                        name="discount" id="discount" value="{{ old('discount', 0) }}" step="0.01"
                                        min="0">
                                    @error('discount')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total">Total</label>
                                    <input type="number" class="form-control @error('total') is-invalid @enderror"
                                        name="total" id="total" value="{{ old('total', 0) }}" step="0.01"
                                        readonly>
                                    @error('total')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="debt">A Deuda</label>
                                    <input type="number" class="form-control @error('debt') is-invalid @enderror"
                                        name="debt" id="debt" value="{{ old('debt', 0) }}" step="0.01"
                                        readonly>
                                    @error('debt')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tercera fila -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observations">Observaciones</label>
                                    <textarea class="form-control @error('observations') is-invalid @enderror" name="observations" id="observations"
                                        placeholder="Escribe aquí algún detalle que desees registrar.">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Registrar Acompañante -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Acompañante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="add-companion-btn">
                                <i class="fas fa-plus"></i> Registrar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm" id="companions-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones finales -->
                <div class="row mb-5">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('entradas.panel-control') }}" class="btn btn-danger">Volver</a>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Registrar Acompañante -->
    <div class="modal fade" id="companion-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Acompañante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_companion_name">Nombre</label>
                        <input type="text" class="form-control" id="modal_companion_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_lastname">Apellido</label>
                        <input type="text" class="form-control" id="modal_companion_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_dni">DNI</label>
                        <input type="text" class="form-control" id="modal_companion_dni" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_phone">Teléfono</label>
                        <input type="text" class="form-control" id="modal_companion_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_companion_email">Email</label>
                        <input type="email" class="form-control" id="modal_companion_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-companion-btn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Cliente -->
    <div class="modal fade" id="client-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_client_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_lastname">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_tipo_id">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="modal_client_tipo_id" style="width: 100%" required>
                            <option value="">Seleccionar</option>
                            @foreach ($tipoDocumentos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_documento">N° de Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_nro_documento" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_nro_matricula">N° de Matrícula</label>
                        <input type="text" class="form-control" id="modal_client_nro_matricula">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_phone">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_client_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_client_email">Email</label>
                        <input type="email" class="form-control" id="modal_client_email">
                    </div>
                    <div class="form-group">
                        <label for="modal_client_address">Dirección</label>
                        <input type="text" class="form-control" id="modal_client_address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="save-client-btn">Guardar</button>
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
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .card {
            border-radius: 0;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        #debt.danger {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        #debt.success {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.45/builds/moment-timezone-with-data.min.js"></script>
    <script>
        $(document).ready(function() {
            // Verificar carga de Moment y Moment-Timezone
            console.log('Moment cargado:', typeof moment !== 'undefined' ? 'Sí' : 'No');
            console.log('Moment-Timezone cargado:', typeof moment.tz !== 'undefined' ? 'Sí' : 'No');

            // Inicializar Select2
            $('.select2').select2({
                placeholder: function() {
                    return $(this).attr('id') === 'client_id' ? 'Seleccionar Cliente' :
                        $(this).attr('id') === 'entry_type' ? 'Seleccionar Tarifa/Precio' :
                        $(this).attr('id') === 'room_id' ? 'Seleccionar Habitación' : 'Seleccionar';
                },
                allowClear: true,
                width: '100%'
            });

            // Lista de acompañantes
            let companions = [];

            // Variable para rastrear si el usuario ha modificado manualmente la fecha/hora de salida
            let isCheckOutModified = false;

            // Detectar cambios manuales en check_out_date y check_out_time
            $('#check_out_date, #check_out_time').on('change', function() {
                isCheckOutModified = true;
                console.log('Fecha u hora de salida modificada manualmente');
            });

            // Actualizar precios y fechas de salida
            function updatePricesAndDates() {
                try {
                    console.log('Iniciando updatePricesAndDates...');

                    const entryTypeId = $('#entry_type').val();
                    console.log('Tarifa seleccionada (ID):', entryTypeId);

                    const quantity = parseInt($('#quantity').val()) || 1;
                    console.log('Cantidad:', quantity);

                    // Obtener datos de la tarifa seleccionada
                    const selectedTariff = $('#entry_type option:selected');
                    const tariffType = selectedTariff.data('type');
                    const price = parseFloat(selectedTariff.data('price')) || 0;
                    const duration = parseInt(selectedTariff.data('duration')) || 0;
                    const hourCheckout = selectedTariff.data('hour-checkout') || '11:00';

                    console.log('Datos de la tarifa:', {
                        tariffType,
                        price,
                        duration,
                        hourCheckout
                    });

                    // Obtener fechas y horas
                    const checkInDate = $('#check_in_date').val();
                    const checkInTime = $('#check_in_time').val();
                    console.log('Fecha y hora de entrada:', {
                        checkInDate,
                        checkInTime
                    });

                    // Validar entradas
                    if (!checkInDate || !checkInTime || !entryTypeId) {
                        console.log('Faltan datos:', {
                            checkInDate,
                            checkInTime,
                            entryTypeId
                        });
                        $('#total').data('base-price', 0);
                        updatePayment();
                        return;
                    }

                    const dateTimeString = `${checkInDate} ${checkInTime}`;
                    if (!moment(dateTimeString, 'YYYY-MM-DD HH:mm', true).isValid()) {
                        console.log('Formato de fecha/hora inválido:', dateTimeString);
                        $('#total').data('base-price', 0);
                        updatePayment();
                        return;
                    }

                    // Crear fecha de entrada
                    let checkIn;
                    if (typeof moment.tz !== 'undefined') {
                        checkIn = moment.tz(dateTimeString, 'YYYY-MM-DD HH:mm', 'America/Argentina/Buenos_Aires');
                    } else {
                        console.warn('Moment-Timezone no disponible, usando desplazamiento manual (UTC-3)...');
                        checkIn = moment(dateTimeString, 'YYYY-MM-DD HH:mm').utcOffset(-180);
                    }
                    console.log('Check-in (moment):', checkIn.format());

                    let checkOut;
                    let totalPrice = price * quantity;

                    if (tariffType === 'HORA') {
                        // Para tarifas por hora, sumar las horas según la duración y la cantidad
                        checkOut = checkIn.clone().add(duration * quantity, 'hours');
                    } else if (tariffType === 'DIA') {
                        // Para tarifas por día, sumar días y establecer la hora de salida
                        checkOut = checkIn.clone().add(quantity, 'days');
                        const [hour, minute] = hourCheckout.split(':');
                        checkOut.set({
                            hour: parseInt(hour),
                            minute: parseInt(minute)
                        });
                    } else {
                        console.log('Tipo de tarifa no reconocido:', tariffType);
                        $('#total').data('base-price', 0);
                        updatePayment();
                        return;
                    }

                    // Actualizar campos de salida solo si no han sido modificados manualmente
                    if (checkOut && !isCheckOutModified) {
                        $('#check_out_date').val(checkOut.format('YYYY-MM-DD'));
                        $('#check_out_time').val(checkOut.format('HH:mm'));
                        console.log('Check-out actualizado automáticamente:', checkOut.format(), 'Precio total:',
                            totalPrice);
                    } else if (!checkOut) {
                        console.log('checkOut no definido para tariffType:', tariffType);
                    } else {
                        console.log('Check-out no actualizado (modificado manualmente)');
                    }

                    // Guardar el precio para updatePayment
                    $('#total').data('base-price', totalPrice);
                    updatePayment();
                } catch (error) {
                    console.error('Error en updatePricesAndDates:', error);
                    $('#total').data('base-price', 0);
                    updatePayment();
                }
            }

            // Actualizar cálculo de pago
            function updatePayment() {
                try {
                    console.log('Iniciando updatePayment...');

                    const discount = parseFloat($('#discount').val()) || 0;
                    const efectivo = parseFloat($('#efectivo').val()) || 0;
                    const mercadopago = parseFloat($('#mercadopago').val()) || 0;
                    const tarjeta = parseFloat($('#tarjeta').val()) || 0;
                    const transferencia = parseFloat($('#transferencia').val()) || 0;

                    const price = parseFloat($('#total').data('base-price')) || 0;

                    console.log('Datos de pago:', {
                        price,
                        discount,
                        efectivo,
                        mercadopago,
                        tarjeta,
                        transferencia
                    });

                    const total = price - discount;
                    const totalPagado = efectivo + mercadopago + tarjeta + transferencia;
                    const debt = total - totalPagado;

                    $('#total').val(total.toFixed(2));
                    $('#debt').val(debt.toFixed(2));

                    if (debt > 0) {
                        $('#debt').removeClass('success').addClass('danger');
                    } else {
                        $('#debt').removeClass('danger').addClass('success');
                    }
                } catch (error) {
                    console.error('Error en updatePayment:', error);
                    $('#total').val('0.00');
                    $('#debt').val('0.00');
                }
            }

            // Escuchar cambios para actualizar precios y fechas
            $('#entry_type, #quantity, #check_in_date, #check_in_time').on('change', function() {
                console.log('Evento change disparado en:', this.id);
                updatePricesAndDates();
            });

            // Escuchar cambios en los campos de pago
            $('#discount, #efectivo, #mercadopago, #tarjeta, #transferencia').on('input', function() {
                console.log('Evento input disparado en:', this.id);
                updatePayment();
            });

            // Inicializar precios y fechas al cargar la página
            updatePricesAndDates();

            // Forzar actualización si entry_type ya tiene un valor
            if ($('#entry_type').val()) {
                console.log('Forzando actualización inicial de entry_type...');
                $('#entry_type').trigger('change');
            }

            // Abrir modal para registrar cliente
            $('#register-client-btn').click(function() {
                $('#client-modal').modal('show');
            });

            // Guardar cliente desde el modal
            $('#save-client-btn').click(function() {
                const client = {
                    name: $('#modal_client_name').val(),
                    lastname: $('#modal_client_lastname').val(),
                    tipo_id: $('#modal_client_tipo_id').val(),
                    nro_documento: $('#modal_client_nro_documento').val(),
                    nro_matricula: $('#modal_client_nro_matricula').val(),
                    phone: $('#modal_client_phone').val(),
                    email: $('#modal_client_email').val(),
                    address: $('#modal_client_address').val()
                };

                if (!client.name || !client.lastname || !client.tipo_id || !client.nro_documento || !client
                    .phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('clientes.storeAjax') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: client.name,
                        lastname: client.lastname,
                        tipo_id: client.tipo_id,
                        nro_documento: client.nro_documento,
                        nro_matricula: client.nro_matricula,
                        phone: client.phone,
                        email: client.email,
                        address: client.address
                    },
                    success: function(response) {
                        if (response.success) {
                            const newClient = response.client;
                            const option = new Option(
                                `${newClient.name} ${newClient.lastname}`,
                                newClient.id,
                                true,
                                true
                            );
                            $('#client_id').append(option).trigger('change');
                            $('#client-modal').modal('hide');
                            $('#modal_client_name, #modal_client_lastname, #modal_client_tipo_id, #modal_client_nro_documento, #modal_client_nro_matricula, #modal_client_phone, #modal_client_email, #modal_client_address')
                                .val('');
                            $('#modal_client_tipo_id').val('').trigger('change');
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Cliente registrado correctamente.',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'No se pudo registrar el cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            timer: 2500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Abrir modal para registrar acompañante
            $('#add-companion-btn').click(function() {
                $('#companion-modal').modal('show');
            });

            // Guardar acompañante desde el modal
            $('#save-companion-btn').click(function() {
                const companion = {
                    name: $('#modal_companion_name').val(),
                    lastname: $('#modal_companion_lastname').val(),
                    dni: $('#modal_companion_dni').val(),
                    phone: $('#modal_companion_phone').val(),
                    email: $('#modal_companion_email').val()
                };

                if (!companion.name || !companion.lastname || !companion.dni || !companion.phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, completa todos los campos requeridos.',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    return;
                }

                companions.push(companion);
                updateCompanionsTable();
                $('#companion-modal').modal('hide');
                $('#modal_companion_name, #modal_companion_lastname, #modal_companion_dni, #modal_companion_phone, #modal_companion_email')
                    .val('');
            });

            // Actualizar la tabla de acompañantes
            function updateCompanionsTable() {
                const tbody = $('#companions-table tbody');
                tbody.empty();
                companions.forEach((companion, index) => {
                    tbody.append(`
                        <tr>
                            <td>${companion.name}</td>
                            <td>${companion.lastname}</td>
                            <td>${companion.dni}</td>
                            <td>${companion.phone}</td>
                            <td>${companion.email || ''}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-companion" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Asegurarse de que solo haya un input hidden para companions
                $('#companions-data').remove();
                $('#entry-form').append('<input type="hidden" name="companions" id="companions-data">');
                $('#companions-data').val(JSON.stringify(companions));
            }

            // Eliminar acompañante
            $(document).on('click', '.remove-companion', function() {
                const index = $(this).data('index');
                companions.splice(index, 1);
                updateCompanionsTable();
            });

            // Enviar formulario
            $('#entry-form').on('submit', function() {
                $('#companions-data').val(JSON.stringify(companions));
            });
        });
    </script>
@stop
