{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Panel de Control</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group d-flex align-items-center">
                <label for="level" class="mb-0 mr-3" style="white-space: nowrap;">Seleccione el Nivel/Piso</label>
                <select class="form-control" id="level" name="level"
                    style="width: 400px; background-color: #2b91ff; color: #00449c; font-weight: bold;">
                    <option value="">Todos los pisos</option>
                    @foreach (\App\Models\Level::all() as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        @foreach ($rooms as $room)
            <div class="col-lg-3 col-6" data-level="{{ $room->level_id }}">
                <div class="small-box {{ $room->is_reserved_today ? 'bg-orange-custom' : ($room->status == 'Disponible' ? 'bg-success' : ($room->status == 'Ocupada' ? 'bg-danger' : ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' ? 'bg-primary' : ($room->status == 'Para la Limpieza' ? 'bg-warning' : 'bg-warning')))) }}"
                    data-status="{{ $room->status }}">
                    <div class="inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <h3>Nº {{ $room->room_number }}</h3>
                        </div>
                        <p>{{ $room->roomType ? $room->roomType->name : 'N/A' }}</p>
                        @if ($room->is_reserved_today && $room->current_reservation)
                            <p class="client-name">
                                {{ $room->current_reservation->client->name . ' ' . $room->current_reservation->client->lastname }}
                            </p>
                            <p class="reservation-dates">
                                Entrada: {{ $room->current_reservation->check_in->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            <p class="client-name">
                                {{ $room->current_entry->client->name . ' ' . $room->current_entry->client->lastname }}</p>
                            <p class="entry-dates">
                                Entrada:
                                {{ \Carbon\Carbon::parse($room->current_entry->check_in)->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <p class="debug-dates" style="display: none;">
                                Debug - Check-in: {{ $room->current_entry->check_in }}<br>
                                Debug - Check-out: {{ $room->current_entry->check_out }}
                            </p>
                        @elseif ($room->status == 'Ocupada')
                            @php
                                $reservation = $room->reservations()->where('check_out', '>=', now())->first();
                            @endphp
                            @if ($reservation)
                                <p class="client-name">{{ $reservation->client->name }}</p>
                            @endif
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            <p class="client-name">Personalizado</p>
                            @if ($room->current_cleaning)
                                <p class="staff-name">{{ $room->current_cleaning->staff->nombre }}</p>
                            @endif
                        @endif
                        <!-- Contadores movidos a la parte inferior derecha -->
                        @if ($room->status == 'Ocupada' && $room->time_remaining)
                            <div class="countdown-wrapper">
                                <div class="countdown" data-checkout="{{ $room->time_remaining['check_out_timestamp'] }}"
                                    data-entry-type="{{ $room->current_entry->entry_type }}"
                                    data-server-time="{{ now()->timestamp }}">
                                    <div class="d-flex">
                                        <div class="time-card mr-1">
                                            <span class="time-remaining days">{{ $room->time_remaining['days'] }}</span>
                                            <small class="d-block">Día</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours']) }}</span>
                                            <small class="d-block">Hora</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes']) }}</span>
                                            <small class="d-block">Min</small>
                                        </div>
                                        <div class="time-card">
                                            <span
                                                class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds']) }}</span>
                                            <small class="d-block">Seg</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            @if ($room->time_remaining && $room->current_cleaning)
                                <div class="countdown-wrapper">
                                    <div class="countdown"
                                        data-checkout="{{ $room->time_remaining['check_out_timestamp'] ?? 0 }}"
                                        data-cleaning-type="{{ $room->current_cleaning->cleaning_type ?? '' }}"
                                        data-server-time="{{ now()->timestamp }}"
                                        data-is-expired="{{ $room->time_remaining['days'] == 0 && $room->time_remaining['hours'] == 0 && $room->time_remaining['minutes'] == 0 && $room->time_remaining['seconds'] == 0 ? 'true' : 'false' }}">
                                        <div class="d-flex">
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining days">{{ $room->time_remaining['days'] ?? 0 }}</span>
                                                <small class="d-block">Día</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours'] ?? 0) }}</span>
                                                <small class="d-block">Hora</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes'] ?? 0) }}</span>
                                                <small class="d-block">Min</small>
                                            </div>
                                            <div class="time-card">
                                                <span
                                                    class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds'] ?? 0) }}</span>
                                                <small class="d-block">Seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if ($room->is_reserved_today)
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    @elseif ($room->status == 'Disponible' || $room->status == 'Ocupada')
                        <div class="icon">
                            <i class="fas fa-bed"></i>
                        </div>
                    @elseif ($room->status == 'Para la Limpieza' || $room->status == 'En Limpieza')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @endif
                    <a class="small-box-footer {{ $room->status == 'Disponible' || $room->status == 'Ocupada' || $room->status == 'Para la Limpieza' || $room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' || $room->is_reserved_today ? '' : 'disabled' }}"
                        @if ($room->status == 'Disponible') href="{{ route('entradas.recepcion', ['room' => $room->id]) }}"
                        @elseif ($room->status == 'Ocupada')
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Ocupada-{{ $room->id }}"
                        @elseif ($room->status == 'Para la Limpieza')
                            href="#"
                            data-toggle="modal"
                            data-target="#cleaning-modal-{{ $room->id }}"
                        @elseif ($room->is_reserved_today)
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Reservada-{{ $room->id }}"
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            href="{{ route('cleaning.finish', $room->id) }}"
                            data-room-id="{{ $room->id }}"
                            data-room-number="{{ $room->room_number }}"
                            onclick="confirmFinishCleaning(event, this)"
                        @else
                            href="#" @endif>
                        @if ($room->is_reserved_today)
                            RESERVADA
                        @elseif ($room->status == 'Disponible')
                            DISPONIBLE
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            {{ $room->current_entry->tariff->name ?? 'N/A' }}
                            @if ($room->current_entry->quantity > 1)
                                ({{ $room->current_entry->quantity }})
                            @endif
                            | OCUPADO
                        @elseif ($room->status == 'Para la Limpieza')
                            PARA LA LIMPIEZA
                        @elseif ($room->status == 'Limpieza Profunda')
                            L.PROFUNDA
                        @elseif ($room->status == 'Limpieza Rápida')
                            L.RÁPIDA
                        @elseif ($room->status == 'En Limpieza')
                            EN LIMPIEZA
                        @else
                            OCUPADO
                        @endif
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Modal para Habitaciones Ocupadas -->
            @if ($room->status == 'Ocupada')
                <div class="modal fade" id="modal-Ocupada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Informes - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.ticket', $room->current_entry->id) }}" target="_blank"
                                            class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Comprob. Inicial</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.detalle-general', $room->current_entry->id) }}"
                                            target="_blank" class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Detalle General</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Consumo</span>
                                                <i class="fas fa-shopping-cart mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('servicio-consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Servicio</span>
                                                <i class="fas fa-wifi mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('renewals.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-info">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Renovar Alquiler</span>
                                                <i class="fas fa-sync-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('salidas.show', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-secondary">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Salida</span>
                                                <i class="fas fa-sign-out-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones en Para la Limpieza -->
            @if ($room->status == 'Para la Limpieza')
                <div class="modal fade" id="cleaning-modal-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Encargar Limpieza de la Hab Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('cleaning.assign', $room->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="staff-{{ $room->id }}" class="mb-3">Personal</label>
                                        <select class="form-control select2" style="width: 80%;"
                                            id="staff-{{ $room->id }}" name="staff" required>
                                            <option value="">Seleccionar personal</option>
                                            @foreach (\App\Models\Staff::all() as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="deep-{{ $room->id }}" value="deep" checked>
                                                <label class="form-check-label" for="deep-{{ $room->id }}">PROFUNDA /
                                                    60 (min)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="quick-{{ $room->id }}" value="quick">
                                                <label class="form-check-label" for="quick-{{ $room->id }}">RÁPIDA /
                                                    30 (min)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Limpiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones Reservadas -->
            @if ($room->is_reserved_today)
                <div class="modal fade" id="modal-Reservada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-orange-custom text-white">
                                <h5 class="modal-title">Opciones - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-primary enter-reservation-btn"
                                            data-room-id="{{ $room->id }}"
                                            data-client-id="{{ $room->current_reservation->client_id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Entrar</span>
                                                <i class="fas fa-sign-in-alt mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-danger cancel-reservation-btn"
                                            data-reservation-id="{{ $room->current_reservation->id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Cancelar</span>
                                                <i class="fas fa-times-circle mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
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

        .small-box {
            border-radius: 10px;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .small-box .inner {
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
            position: relative;
        }

        .small-box .inner h3 {
            margin-bottom: 10px;
        }

        .small-box .inner p {
            margin: 0;
        }

        .small-box .inner .client-name {
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .staff-name {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .reservation-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .entry-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .countdown-wrapper {
            position: absolute;
            bottom: 0px;
            /* Ajustado de 5px a 0px para bajar el contador */
            right: 10px;
            text-align: right;
        }

        .small-box .inner .countdown {
            line-height: 1;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .small-box .inner .countdown .time-card {
            border-radius: 5px;
            padding: 5px 6px;
            text-align: center;
            min-width: 36px;
            height: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .small-box .inner .countdown[data-entry-type] .time-card {
            background-color: #ff6666;
        }

        .small-box .inner .countdown[data-cleaning-type] .time-card {
            background-color: #66b0ff;
        }

        .small-box .inner .countdown .time-remaining {
            font-size: 1.44rem;
            font-weight: bold;
            line-height: 1;
        }

        .small-box .inner .countdown small {
            font-size: 0.72rem;
            line-height: 1;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .small-box .inner .countdown .mr-1 {
            margin-right: 6px;
        }

        .small-box .inner .countdown.negative .time-remaining {
            color: red;
        }

        .small-box .inner .countdown.expired .time-remaining {
            color: #fff;
        }

        .small-box .icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .small-box-footer {
            flex-shrink: 0;
        }

        .small-box-footer.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .bg-orange-custom {
            background-color: #ff8c00 !important;
            color: white !important;
        }

        .modal-header.bg-danger {
            background-color: #dc3545 !important;
        }

        .modal-header.bg-warning {
            background-color: #ffc107 !important;
        }

        .modal-header.bg-orange-custom {
            background-color: #ff8c00 !important;
        }

        .option-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ced4da !important;
            background-color: #f8f9fa !important;
            transition: background-color 0.2s;
            height: 80px;
            padding: 10px;
        }

        .option-btn:hover {
            background-color: #e9ecef !important;
        }

        .option-btn .d-flex {
            width: 100%;
            height: 100%;
        }

        .option-btn span {
            font-size: 0.9rem;
            text-align: center;
            line-height: 1.2;
        }

        .option-btn i {
            font-size: 1.5rem;
        }

        .option-btn.text-danger i,
        .option-btn.text-danger span {
            color: #dc3545 !important;
        }

        .option-btn.text-success i,
        .option-btn.text-success span {
            color: #28a745 !important;
        }

        .option-btn.text-info i,
        .option-btn.text-info span {
            color: #17a2b8 !important;
        }

        .option-btn.text-secondary i,
        .option-btn.text-secondary span {
            color: #6c757d !important;
        }

        .option-btn.text-primary i,
        .option-btn.text-primary span {
            color: #007bff !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#level').on('change', function() {
                var selectedLevel = $(this).val();
                $('.col-lg-3').each(function() {
                    var roomLevel = $(this).data('level');
                    if (selectedLevel === '' || roomLevel == selectedLevel) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            var startTime = Date.now() / 1000;

            function updateCountdown() {
                $('.countdown').each(function() {
                    var $countdown = $(this);
                    var checkOutTimestamp = parseInt($countdown.attr('data-checkout'));
                    var serverTime = parseInt($countdown.attr('data-server-time'));
                    var isExpired = $countdown.attr('data-is-expired') === 'true';

                    if (isNaN(checkOutTimestamp)) {
                        console.error('Invalid checkOutTimestamp:', checkOutTimestamp);
                        return;
                    }

                    var currentTime;
                    if (!isNaN(serverTime) && serverTime > 1000000000) {
                        var now = Date.now() / 1000;
                        var elapsedTime = now - startTime;
                        currentTime = serverTime + elapsedTime;
                    } else {
                        console.warn('ServerTime is invalid or not available, using client time');
                        currentTime = Date.now() / 1000;
                    }

                    var distance = (checkOutTimestamp - currentTime) * 1000;

                    var days, hours, minutes, seconds;
                    var totalSeconds = Math.floor(Math.abs(distance) / 1000);
                    var isNegative = distance < 0;

                    var entryType = $countdown.attr('data-entry-type');
                    var cleaningType = $countdown.attr('data-cleaning-type');

                    // Calcular días, horas, minutos y segundos
                    days = Math.floor(totalSeconds / (24 * 60 * 60));
                    totalSeconds %= (24 * 60 * 60);
                    hours = Math.floor(totalSeconds / (60 * 60));
                    totalSeconds %= (60 * 60);
                    minutes = Math.floor(totalSeconds / 60);
                    seconds = totalSeconds % 60;

                    var $daysElement = $countdown.find('.time-remaining.days');
                    var $hoursElement = $countdown.find('.time-remaining.hours');
                    var $minutesElement = $countdown.find('.time-remaining.minutes');
                    var $secondsElement = $countdown.find('.time-remaining.seconds');

                    if (cleaningType) {
                        // Para contadores de limpieza (como "Limpieza Rápida")
                        var maxSeconds = cleaningType === 'deep' ? 60 * 60 : 30 * 60;
                        if (totalSeconds > maxSeconds) {
                            totalSeconds = maxSeconds;
                            days = Math.floor(totalSeconds / (24 * 60 * 60));
                            totalSeconds %= (24 * 60 * 60);
                            hours = Math.floor(totalSeconds / (60 * 60));
                            totalSeconds %= (60 * 60);
                            minutes = Math.floor(totalSeconds / 60);
                            seconds = totalSeconds % 60;
                        }

                        if (isExpired || (days === 0 && hours === 0 && minutes === 0 && seconds === 0)) {
                            $countdown.addClass('expired');
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text('0');
                            if ($hoursElement.length) $hoursElement.text('00');
                            if ($minutesElement.length) $minutesElement.text('00');
                            if ($secondsElement.length) $secondsElement.text('00');
                            $countdown.attr('data-is-expired', 'true');
                            return; // Detener actualización si está expirado
                        }

                        $countdown.removeClass('expired');
                        $countdown.removeClass('negative');
                        if ($daysElement.length) $daysElement.text(days);
                        if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math.abs(
                            hours) : Math.abs(hours));
                        if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' + Math
                            .abs(minutes) : Math.abs(minutes));
                        if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' + Math
                            .abs(seconds) : Math.abs(seconds));

                        if (days === 0 && hours === 0 && minutes === 0 && seconds === 0) {
                            $countdown.attr('data-is-expired', 'true');
                            $countdown.addClass('expired');
                        }
                    } else if (entryType) {
                        // Para contadores de ocupación (como "4 HORAS | OCUPADO")
                        if (entryType === '4_hours' && distance > 0) {
                            var maxSecondsFor4Hours = 4 * 60 * 60;
                            if (totalSeconds > maxSecondsFor4Hours) {
                                totalSeconds = maxSecondsFor4Hours;
                                days = Math.floor(totalSeconds / (24 * 60 * 60));
                                totalSeconds %= (24 * 60 * 60);
                                hours = Math.floor(totalSeconds / (60 * 60));
                                totalSeconds %= (60 * 60);
                                minutes = Math.floor(totalSeconds / 60);
                                seconds = totalSeconds % 60;
                            }
                        }

                        if (isNegative) {
                            $countdown.addClass('negative');
                            if ($daysElement.length) $daysElement.text(days !== 0 ? '-' + days : days);
                            if ($hoursElement.length) $hoursElement.text(hours !== 0 ? '-' + (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)) : (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)));
                            if ($minutesElement.length) $minutesElement.text(minutes !== 0 ? '-' + (Math
                                    .abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)) :
                                (Math.abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)));
                            if ($secondsElement.length) $secondsElement.text(seconds !== 0 ? '-' + (Math
                                    .abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)) :
                                (Math.abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)));
                        } else {
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text(days);
                            if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math
                                .abs(hours) : Math.abs(hours));
                            if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' +
                                Math.abs(minutes) : Math.abs(minutes));
                            if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' +
                                Math.abs(seconds) : Math.abs(seconds));
                        }
                    }
                });
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);

            $('.small-box').each(function() {
                var roomNumber = $(this).find('h3').text();
                var status = $(this).data('status');
                console.log(`Room ${roomNumber} - Status: ${status}`);
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

            @if (session('showCajaCerradaAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Para poder realizar esta operación es necesario Aperturar Caja',
                    html: 'Para poder realizar esta operación es necesario Aperturar Caja<br><br>¿Está Usted de acuerdo?',
                    showCancelButton: false,
                    confirmButtonText: 'Sí, Adelante',
                    confirmButtonColor: '#28a745',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('showCajaOtroUsuarioAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Caja aperturada por otro usuario. Espere que el usuario responsable cierre la caja.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#007bff',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('openModal'))
                $('#{{ session('openModal') }}').modal('show');
            @endif

            $('.enter-reservation-btn').click(function() {
                const roomId = $(this).data('room-id');
                const clientId = $(this).data('client-id');
                window.location.href = '{{ route('entradas.recepcion', ['room' => ':room']) }}'.replace(
                    ':room', roomId) + '?client_id=' + clientId;
            });

            $('.cancel-reservation-btn').click(function() {
                const reservationId = $(this).data('reservation-id');
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se cancelará la reserva. Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Cancelar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('reservas.cancel') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reservation_id: reservationId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Reserva cancelada correctamente.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo cancelar la reserva.',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });

        function confirmFinishCleaning(event, element) {
            event.preventDefault();
            var roomId = $(element).data('room-id');
            var roomNumber = $(element).data('room-number');
            var finishUrl = $(element).attr('href');

            Swal.fire({
                title: '¿La habitación ' + roomNumber + ' ya está disponible?',
                showCancelButton: true,
                confirmButtonText: 'Ya está Limpio',
                cancelButtonText: 'Aún no',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#dc3545',
                icon: 'question'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = finishUrl;
                }
            });
        }
    </script>
@stop --}}





































































































{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Panel de Control</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group d-flex align-items-center">
                <label for="level" class="mb-0 mr-3" style="white-space: nowrap;">Seleccione el Nivel/Piso</label>
                <select class="form-control" id="level" name="level"
                    style="width: 400px; background-color: #2b91ff; color: #00449c; font-weight: bold;">
                    <option value="">Todos los pisos</option>
                    @foreach (\App\Models\Level::all() as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        @foreach ($rooms as $room)
            <div class="col-lg-3 col-6" data-level="{{ $room->level_id }}">
                <div class="small-box {{ $room->is_reserved_today ? 'bg-orange-custom' : ($room->status == 'Disponible' ? 'bg-success' : ($room->status == 'Ocupada' ? 'bg-danger' : ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' ? 'bg-primary' : ($room->status == 'Para la Limpieza' ? 'bg-warning' : 'bg-warning')))) }}"
                    data-status="{{ $room->status }}">
                    <div class="inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <h3>Nº {{ $room->room_number }}</h3>
                        </div>
                        <p>{{ $room->roomType ? $room->roomType->name : 'N/A' }}</p>
                        @if ($room->is_reserved_today && $room->current_reservation)
                            <p class="client-name">
                                {{ $room->current_reservation->client->name . ' ' . $room->current_reservation->client->lastname }}
                            </p>
                            <p class="reservation-dates">
                                Entrada: {{ $room->current_reservation->check_in->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            <p class="client-name">
                                {{ $room->current_entry->client->name . ' ' . $room->current_entry->client->lastname }}</p>
                            <p class="entry-dates">
                                Entrada:
                                {{ \Carbon\Carbon::parse($room->current_entry->check_in)->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <p class="debug-dates" style="display: none;">
                                Debug - Check-in: {{ $room->current_entry->check_in }}<br>
                                Debug - Check-out: {{ $room->current_entry->check_out }}
                            </p>
                        @elseif ($room->status == 'Ocupada')
                            @php
                                $reservation = $room->reservations()->where('check_out', '>=', now())->first();
                            @endphp
                            @if ($reservation)
                                <p class="client-name">{{ $reservation->client->name }}</p>
                            @endif
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            <p class="client-name">Personalizado</p>
                            @if ($room->current_cleaning)
                                <p class="staff-name">{{ $room->current_cleaning->staff->nombre }}</p>
                            @endif
                        @endif
                        <!-- Contadores movidos a la parte inferior derecha -->
                        @if ($room->status == 'Ocupada' && $room->time_remaining)
                            <div class="countdown-wrapper">
                                <div class="countdown" data-checkout="{{ $room->time_remaining['check_out_timestamp'] }}"
                                    data-entry-type="{{ $room->current_entry->entry_type }}"
                                    data-server-time="{{ now()->timestamp }}">
                                    <div class="d-flex">
                                        <div class="time-card mr-1">
                                            <span class="time-remaining days">{{ $room->time_remaining['days'] }}</span>
                                            <small class="d-block">Día</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours']) }}</span>
                                            <small class="d-block">Hora</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes']) }}</span>
                                            <small class="d-block">Min</small>
                                        </div>
                                        <div class="time-card">
                                            <span
                                                class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds']) }}</span>
                                            <small class="d-block">Seg</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            @if ($room->time_remaining && $room->current_cleaning)
                                <div class="countdown-wrapper">
                                    <div class="countdown"
                                        data-checkout="{{ $room->time_remaining['check_out_timestamp'] ?? 0 }}"
                                        data-cleaning-type="{{ $room->current_cleaning->cleaning_type ?? '' }}"
                                        data-server-time="{{ now()->timestamp }}"
                                        data-is-expired="{{ $room->time_remaining['days'] == 0 && $room->time_remaining['hours'] == 0 && $room->time_remaining['minutes'] == 0 && $room->time_remaining['seconds'] == 0 ? 'true' : 'false' }}">
                                        <div class="d-flex">
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining days">{{ $room->time_remaining['days'] ?? 0 }}</span>
                                                <small class="d-block">Día</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours'] ?? 0) }}</span>
                                                <small class="d-block">Hora</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes'] ?? 0) }}</span>
                                                <small class="d-block">Min</small>
                                            </div>
                                            <div class="time-card">
                                                <span
                                                    class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds'] ?? 0) }}</span>
                                                <small class="d-block">Seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if ($room->is_reserved_today)
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    @elseif ($room->status == 'Disponible' || $room->status == 'Ocupada')
                        <div class="icon">
                            <i class="fas fa-bed"></i>
                        </div>
                    @elseif ($room->status == 'Para la Limpieza' || $room->status == 'En Limpieza')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @endif
                    <a class="small-box-footer {{ $room->status == 'Disponible' || $room->status == 'Ocupada' || $room->status == 'Para la Limpieza' || $room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' || $room->is_reserved_today ? '' : 'disabled' }}"
                        @if ($room->status == 'Disponible') href="{{ route('entradas.recepcion', ['room' => $room->id]) }}"
                        @elseif ($room->status == 'Ocupada')
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Ocupada-{{ $room->id }}"
                        @elseif ($room->status == 'Para la Limpieza')
                            href="#"
                            data-toggle="modal"
                            data-target="#cleaning-modal-{{ $room->id }}"
                        @elseif ($room->is_reserved_today)
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Reservada-{{ $room->id }}"
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            href="{{ route('cleaning.finish', $room->id) }}"
                            data-room-id="{{ $room->id }}"
                            data-room-number="{{ $room->room_number }}"
                            onclick="confirmFinishCleaning(event, this)"
                        @else
                            href="#" @endif>
                        @if ($room->is_reserved_today)
                            RESERVADA
                        @elseif ($room->status == 'Disponible')
                            DISPONIBLE
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            {{ $room->current_entry->tariff->name ?? 'N/A' }}
                            @if ($room->current_entry->quantity > 1 && !preg_match('/\(.+\)/', $room->current_entry->tariff->name ?? ''))
                                ({{ $room->current_entry->quantity }})
                            @endif
                            | OCUPADO
                        @elseif ($room->status == 'Para la Limpieza')
                            PARA LA LIMPIEZA
                        @elseif ($room->status == 'Limpieza Profunda')
                            L.PROFUNDA
                        @elseif ($room->status == 'Limpieza Rápida')
                            L.RÁPIDA
                        @elseif ($room->status == 'En Limpieza')
                            EN LIMPIEZA
                        @else
                            OCUPADO
                        @endif
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Modal para Habitaciones Ocupadas -->
            @if ($room->status == 'Ocupada')
                <div class="modal fade" id="modal-Ocupada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Informes - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.ticket', $room->current_entry->id) }}" target="_blank"
                                            class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Comprob. Inicial</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.detalle-general', $room->current_entry->id) }}"
                                            target="_blank" class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Detalle General</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Consumo</span>
                                                <i class="fas fa-shopping-cart mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('servicio-consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Servicio</span>
                                                <i class="fas fa-wifi mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('renewals.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-info">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Renovar Alquiler</span>
                                                <i class="fas fa-sync-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('salidas.show', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-secondary">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Salida</span>
                                                <i class="fas fa-sign-out-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones en Para la Limpieza -->
            @if ($room->status == 'Para la Limpieza')
                <div class="modal fade" id="cleaning-modal-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Encargar Limpieza de la Hab Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('cleaning.assign', $room->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="staff-{{ $room->id }}" class="mb-3">Personal</label>
                                        <select class="form-control select2" style="width: 80%;"
                                            id="staff-{{ $room->id }}" name="staff" required>
                                            <option value="">Seleccionar personal</option>
                                            @foreach (\App\Models\Staff::all() as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="deep-{{ $room->id }}" value="deep" checked>
                                                <label class="form-check-label" for="deep-{{ $room->id }}">PROFUNDA /
                                                    60 (min)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="quick-{{ $room->id }}" value="quick">
                                                <label class="form-check-label" for="quick-{{ $room->id }}">RÁPIDA /
                                                    30 (min)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Limpiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones Reservadas -->
            @if ($room->is_reserved_today)
                <div class="modal fade" id="modal-Reservada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-orange-custom text-white">
                                <h5 class="modal-title">Opciones - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-primary enter-reservation-btn"
                                            data-room-id="{{ $room->id }}"
                                            data-client-id="{{ $room->current_reservation->client_id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Entrar</span>
                                                <i class="fas fa-sign-in-alt mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-danger cancel-reservation-btn"
                                            data-reservation-id="{{ $room->current_reservation->id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Cancelar</span>
                                                <i class="fas fa-times-circle mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
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

        .small-box {
            border-radius: 10px;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .small-box .inner {
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
            position: relative;
        }

        .small-box .inner h3 {
            margin-bottom: 10px;
        }

        .small-box .inner p {
            margin: 0;
        }

        .small-box .inner .client-name {
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .staff-name {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .reservation-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .entry-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .countdown-wrapper {
            position: absolute;
            bottom: 0px;
            right: 10px;
            text-align: right;
        }

        .small-box .inner .countdown {
            line-height: 1;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .small-box .inner .countdown .time-card {
            border-radius: 5px;
            padding: 5px 6px;
            text-align: center;
            min-width: 36px;
            height: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .small-box .inner .countdown[data-entry-type] .time-card {
            background-color: #ff6666;
        }

        .small-box .inner .countdown[data-cleaning-type] .time-card {
            background-color: #66b0ff;
        }

        .small-box .inner .countdown .time-remaining {
            font-size: 1.44rem;
            font-weight: bold;
            line-height: 1;
        }

        .small-box .inner .countdown small {
            font-size: 0.72rem;
            line-height: 1;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .small-box .inner .countdown .mr-1 {
            margin-right: 6px;
        }

        .small-box .inner .countdown.negative .time-remaining {
            color: red;
        }

        .small-box .inner .countdown.expired .time-remaining {
            color: #fff;
        }

        .small-box .icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .small-box-footer {
            flex-shrink: 0;
        }

        .small-box-footer.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .bg-orange-custom {
            background-color: #ff8c00 !important;
            color: white !important;
        }

        .modal-header.bg-danger {
            background-color: #dc3545 !important;
        }

        .modal-header.bg-warning {
            background-color: #ffc107 !important;
        }

        .modal-header.bg-orange-custom {
            background-color: #ff8c00 !important;
        }

        .option-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ced4da !important;
            background-color: #f8f9fa !important;
            transition: background-color 0.2s;
            height: 80px;
            padding: 10px;
        }

        .option-btn:hover {
            background-color: #e9ecef !important;
        }

        .option-btn .d-flex {
            width: 100%;
            height: 100%;
        }

        .option-btn span {
            font-size: 0.9rem;
            text-align: center;
            line-height: 1.2;
        }

        .option-btn i {
            font-size: 1.5rem;
        }

        .option-btn.text-danger i,
        .option-btn.text-danger span {
            color: #dc3545 !important;
        }

        .option-btn.text-success i,
        .option-btn.text-success span {
            color: #28a745 !important;
        }

        .option-btn.text-info i,
        .option-btn.text-info span {
            color: #17a2b8 !important;
        }

        .option-btn.text-secondary i,
        .option-btn.text-secondary span {
            color: #6c757d !important;
        }

        .option-btn.text-primary i,
        .option-btn.text-primary span {
            color: #007bff !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#level').on('change', function() {
                var selectedLevel = $(this).val();
                $('.col-lg-3').each(function() {
                    var roomLevel = $(this).data('level');
                    if (selectedLevel === '' || roomLevel == selectedLevel) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            var startTime = Date.now() / 1000;

            function updateCountdown() {
                $('.countdown').each(function() {
                    var $countdown = $(this);
                    var checkOutTimestamp = parseInt($countdown.attr('data-checkout'));
                    var serverTime = parseInt($countdown.attr('data-server-time'));
                    var isExpired = $countdown.attr('data-is-expired') === 'true';

                    if (isNaN(checkOutTimestamp)) {
                        console.error('Invalid checkOutTimestamp:', checkOutTimestamp);
                        return;
                    }

                    var currentTime;
                    if (!isNaN(serverTime) && serverTime > 1000000000) {
                        var now = Date.now() / 1000;
                        var elapsedTime = now - startTime;
                        currentTime = serverTime + elapsedTime;
                    } else {
                        console.warn('ServerTime is invalid or not available, using client time');
                        currentTime = Date.now() / 1000;
                    }

                    var distance = (checkOutTimestamp - currentTime) * 1000;

                    var days, hours, minutes, seconds;
                    var totalSeconds = Math.floor(Math.abs(distance) / 1000);
                    var isNegative = distance < 0;

                    var entryType = $countdown.attr('data-entry-type');
                    var cleaningType = $countdown.attr('data-cleaning-type');

                    // Calcular días, horas, minutos y segundos
                    days = Math.floor(totalSeconds / (24 * 60 * 60));
                    totalSeconds %= (24 * 60 * 60);
                    hours = Math.floor(totalSeconds / (60 * 60));
                    totalSeconds %= (60 * 60);
                    minutes = Math.floor(totalSeconds / 60);
                    seconds = totalSeconds % 60;

                    var $daysElement = $countdown.find('.time-remaining.days');
                    var $hoursElement = $countdown.find('.time-remaining.hours');
                    var $minutesElement = $countdown.find('.time-remaining.minutes');
                    var $secondsElement = $countdown.find('.time-remaining.seconds');

                    if (cleaningType) {
                        // Para contadores de limpieza (como "Limpieza Rápida")
                        var maxSeconds = cleaningType === 'deep' ? 60 * 60 : 30 * 60;
                        if (totalSeconds > maxSeconds) {
                            totalSeconds = maxSeconds;
                            days = Math.floor(totalSeconds / (24 * 60 * 60));
                            totalSeconds %= (24 * 60 * 60);
                            hours = Math.floor(totalSeconds / (60 * 60));
                            totalSeconds %= (60 * 60);
                            minutes = Math.floor(totalSeconds / 60);
                            seconds = totalSeconds % 60;
                        }

                        if (isExpired || (days === 0 && hours === 0 && minutes === 0 && seconds === 0)) {
                            $countdown.addClass('expired');
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text('0');
                            if ($hoursElement.length) $hoursElement.text('00');
                            if ($minutesElement.length) $hoursElement.text('00');
                            if ($secondsElement.length) $secondsElement.text('00');
                            $countdown.attr('data-is-expired', 'true');
                            return; // Detener actualización si está expirado
                        }

                        $countdown.removeClass('expired');
                        $countdown.removeClass('negative');
                        if ($daysElement.length) $daysElement.text(days);
                        if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math.abs(
                            hours) : Math.abs(hours));
                        if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' + Math
                            .abs(minutes) : Math.abs(minutes));
                        if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' + Math
                            .abs(seconds) : Math.abs(seconds));

                        if (days === 0 && hours === 0 && minutes === 0 && seconds === 0) {
                            $countdown.attr('data-is-expired', 'true');
                            $countdown.addClass('expired');
                        }
                    } else if (entryType) {
                        // Para contadores de ocupación (como "4 HORAS | OCUPADO")
                        if (entryType === '4_hours' && distance > 0) {
                            var maxSecondsFor4Hours = 4 * 60 * 60;
                            if (totalSeconds > maxSecondsFor4Hours) {
                                totalSeconds = maxSecondsFor4Hours;
                                days = Math.floor(totalSeconds / (24 * 60 * 60));
                                totalSeconds %= (24 * 60 * 60);
                                hours = Math.floor(totalSeconds / (60 * 60));
                                totalSeconds %= (60 * 60);
                                minutes = Math.floor(totalSeconds / 60);
                                seconds = totalSeconds % 60;
                            }
                        }

                        if (isNegative) {
                            $countdown.addClass('negative');
                            if ($daysElement.length) $daysElement.text(days !== 0 ? '-' + days : days);
                            if ($hoursElement.length) $hoursElement.text(hours !== 0 ? '-' + (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)) : (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)));
                            if ($minutesElement.length) $minutesElement.text(minutes !== 0 ? '-' + (Math
                                    .abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)) :
                                (Math.abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)));
                            if ($secondsElement.length) $secondsElement.text(seconds !== 0 ? '-' + (Math
                                    .abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)) :
                                (Math.abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)));
                        } else {
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text(days);
                            if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math
                                .abs(hours) : Math.abs(hours));
                            if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' +
                                Math.abs(minutes) : Math.abs(minutes));
                            if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' +
                                Math.abs(seconds) : Math.abs(seconds));
                        }
                    }
                });
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);

            $('.small-box').each(function() {
                var roomNumber = $(this).find('h3').text();
                var status = $(this).data('status');
                console.log(`Room ${roomNumber} - Status: ${status}`);
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

            @if (session('showCajaCerradaAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Para poder realizar esta operación es necesario Aperturar Caja',
                    html: 'Para poder realizar esta operación es necesario Aperturar Caja<br><br>¿Está Usted de acuerdo?',
                    showCancelButton: false,
                    confirmButtonText: 'Sí, Adelante',
                    confirmButtonColor: '#28a745',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('showCajaOtroUsuarioAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Caja aperturada por otro usuario. Espere que el usuario responsable cierre la caja.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#007bff',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('openModal'))
                $('#{{ session('openModal') }}').modal('show');
            @endif

            $('.enter-reservation-btn').click(function() {
                const roomId = $(this).data('room-id');
                const clientId = $(this).data('client-id');
                window.location.href = '{{ route('entradas.recepcion', ['room' => ':room']) }}'.replace(
                    ':room', roomId) + '?client_id=' + clientId;
            });

            $('.cancel-reservation-btn').click(function() {
                const reservationId = $(this).data('reservation-id');
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se cancelará la reserva. Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Cancelar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('reservas.cancel') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reservation_id: reservationId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Reserva cancelada correctamente.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo cancelar la reserva.',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });

        function confirmFinishCleaning(event, element) {
            event.preventDefault();
            var roomId = $(element).data('room-id');
            var roomNumber = $(element).data('room-number');
            var finishUrl = $(element).attr('href');

            Swal.fire({
                title: '¿La habitación ' + roomNumber + ' ya está disponible?',
                showCancelButton: true,
                confirmButtonText: 'Ya está Limpio',
                cancelButtonText: 'Aún no',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#dc3545',
                icon: 'question'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = finishUrl;
                }
            });
        }
    </script>
@stop --}}







































































































@extends('adminlte::page')

@section('content_header')
    <h1><b>Panel de Control</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group d-flex align-items-center">
                <label for="level" class="mb-0 mr-3" style="white-space: nowrap;">Seleccione el Nivel/Piso</label>
                <select class="form-control" id="level" name="level"
                    style="width: 400px; background-color: #2b91ff; color: #00449c; font-weight: bold;">
                    <option value="">Todos los pisos</option>
                    @foreach (\App\Models\Level::all() as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        @foreach ($rooms as $room)
            <div class="col-lg-3 col-6" data-level="{{ $room->level_id }}">
                <div class="small-box {{ $room->is_reserved_today ? 'bg-orange-custom' : ($room->status == 'Disponible' ? 'bg-success' : ($room->status == 'Ocupada' ? 'bg-danger' : ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' ? 'bg-primary' : ($room->status == 'Para la Limpieza' ? 'bg-warning' : 'bg-warning')))) }}"
                    data-status="{{ $room->status }}">
                    <div class="inner">
                        <div class="d-flex justify-content-between align-items-start">
                            <h3>Nº {{ $room->room_number }}</h3>
                        </div>
                        <p>{{ $room->roomType ? $room->roomType->name : 'N/A' }}</p>
                        @if ($room->is_reserved_today && $room->current_reservation)
                            <p class="client-name">
                                {{ $room->current_reservation->client->name . ' ' . $room->current_reservation->client->lastname }}
                            </p>
                            <p class="reservation-dates">
                                Entrada: {{ $room->current_reservation->check_in->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            <p class="client-name">
                                {{ $room->current_entry->client->name . ' ' . $room->current_entry->client->lastname }}</p>
                            <p class="entry-dates">
                                Entrada:
                                {{ \Carbon\Carbon::parse($room->current_entry->check_in)->format('d/m/Y H:i') }}<br>
                                Salida:
                                {{ $room->display_check_out ? \Carbon\Carbon::parse($room->display_check_out)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <p class="debug-dates" style="display: none;">
                                Debug - Check-in: {{ $room->current_entry->check_in }}<br>
                                Debug - Check-out: {{ $room->current_entry->check_out }}
                            </p>
                        @elseif ($room->status == 'Ocupada')
                            @php
                                $reservation = $room->reservations()->where('check_out', '>=', now())->first();
                            @endphp
                            @if ($reservation)
                                <p class="client-name">{{ $reservation->client->name }}</p>
                            @endif
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            <p class="client-name">Personalizado</p>
                            @if ($room->current_cleaning)
                                <p class="staff-name">{{ $room->current_cleaning->staff->nombre }}</p>
                            @endif
                        @endif
                        <!-- Contadores movidos a la parte inferior derecha -->
                        @if ($room->status == 'Ocupada' && $room->time_remaining)
                            <div class="countdown-wrapper">
                                <div class="countdown" data-checkout="{{ $room->time_remaining['check_out_timestamp'] }}"
                                    data-entry-type="{{ $room->current_entry->roomTypeTariff->name ?? 'unknown' }}"
                                    data-server-time="{{ now()->timestamp }}">
                                    <div class="d-flex">
                                        <div class="time-card mr-1">
                                            <span class="time-remaining days">{{ $room->time_remaining['days'] }}</span>
                                            <small class="d-block">Día</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours']) }}</span>
                                            <small class="d-block">Hora</small>
                                        </div>
                                        <div class="time-card mr-1">
                                            <span
                                                class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes']) }}</span>
                                            <small class="d-block">Min</small>
                                        </div>
                                        <div class="time-card">
                                            <span
                                                class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds']) }}</span>
                                            <small class="d-block">Seg</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            @if ($room->time_remaining && $room->current_cleaning)
                                <div class="countdown-wrapper">
                                    <div class="countdown"
                                        data-checkout="{{ $room->time_remaining['check_out_timestamp'] ?? 0 }}"
                                        data-cleaning-type="{{ $room->current_cleaning->cleaning_type ?? '' }}"
                                        data-server-time="{{ now()->timestamp }}"
                                        data-is-expired="{{ $room->time_remaining['days'] == 0 && $room->time_remaining['hours'] == 0 && $room->time_remaining['minutes'] == 0 && $room->time_remaining['seconds'] == 0 ? 'true' : 'false' }}">
                                        <div class="d-flex">
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining days">{{ $room->time_remaining['days'] ?? 0 }}</span>
                                                <small class="d-block">Día</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining hours">{{ sprintf('%02d', $room->time_remaining['hours'] ?? 0) }}</span>
                                                <small class="d-block">Hora</small>
                                            </div>
                                            <div class="time-card mr-1">
                                                <span
                                                    class="time-remaining minutes">{{ sprintf('%02d', $room->time_remaining['minutes'] ?? 0) }}</span>
                                                <small class="d-block">Min</small>
                                            </div>
                                            <div class="time-card">
                                                <span
                                                    class="time-remaining seconds">{{ sprintf('%02d', $room->time_remaining['seconds'] ?? 0) }}</span>
                                                <small class="d-block">Seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if ($room->is_reserved_today)
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    @elseif ($room->status == 'Disponible' || $room->status == 'Ocupada')
                        <div class="icon">
                            <i class="fas fa-bed"></i>
                        </div>
                    @elseif ($room->status == 'Para la Limpieza' || $room->status == 'En Limpieza')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida')
                        <div class="icon">
                            <i class="fas fa-broom"></i>
                        </div>
                    @endif
                    <a class="small-box-footer {{ $room->status == 'Disponible' || $room->status == 'Ocupada' || $room->status == 'Para la Limpieza' || $room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza' || $room->is_reserved_today ? '' : 'disabled' }}"
                        @if ($room->status == 'Disponible') href="{{ route('entradas.recepcion', ['room' => $room->id]) }}"
                        @elseif ($room->status == 'Ocupada')
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Ocupada-{{ $room->id }}"
                        @elseif ($room->status == 'Para la Limpieza')
                            href="#"
                            data-toggle="modal"
                            data-target="#cleaning-modal-{{ $room->id }}"
                        @elseif ($room->is_reserved_today)
                            href="#"
                            data-toggle="modal"
                            data-target="#modal-Reservada-{{ $room->id }}"
                        @elseif ($room->status == 'Limpieza Profunda' || $room->status == 'Limpieza Rápida' || $room->status == 'En Limpieza')
                            href="{{ route('cleaning.finish', $room->id) }}"
                            data-room-id="{{ $room->id }}"
                            data-room-number="{{ $room->room_number }}"
                            onclick="confirmFinishCleaning(event, this)"
                        @else
                            href="#" @endif>
                        @if ($room->is_reserved_today)
                            RESERVADA
                        @elseif ($room->status == 'Disponible')
                            DISPONIBLE
                        @elseif ($room->status == 'Ocupada' && $room->current_entry)
                            {{ $room->current_entry->roomTypeTariff->name ?? 'N/A' }}
                            @if ($room->current_entry->quantity > 1)
                                ({{ $room->current_entry->quantity }})
                            @endif
                            | OCUPADO
                        @elseif ($room->status == 'Para la Limpieza')
                            PARA LA LIMPIEZA
                        @elseif ($room->status == 'Limpieza Profunda')
                            L.PROFUNDA
                        @elseif ($room->status == 'Limpieza Rápida')
                            L.RÁPIDA
                        @elseif ($room->status == 'En Limpieza')
                            EN LIMPIEZA
                        @else
                            OCUPADO
                        @endif
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Modal para Habitaciones Ocupadas -->
            @if ($room->status == 'Ocupada')
                <div class="modal fade" id="modal-Ocupada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Informes - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.ticket', $room->current_entry->id) }}" target="_blank"
                                            class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Comprob. Inicial</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('entradas.detalle-general', $room->current_entry->id) }}"
                                            target="_blank" class="btn btn-light btn-block option-btn text-danger">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Detalle General</span>
                                                <i class="fas fa-file-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Consumo</span>
                                                <i class="fas fa-shopping-cart mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('servicio-consumo.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-success">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Servicio</span>
                                                <i class="fas fa-wifi mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('renewals.create', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-info">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Renovar Alquiler</span>
                                                <i class="fas fa-sync-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('salidas.show', $room->current_entry->id) }}"
                                            class="btn btn-light btn-block option-btn text-secondary">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Salida</span>
                                                <i class="fas fa-sign-out-alt mt-2"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones en Para la Limpieza -->
            @if ($room->status == 'Para la Limpieza')
                <div class="modal fade" id="cleaning-modal-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Encargar Limpieza de la Hab Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('cleaning.assign', $room->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="staff-{{ $room->id }}" class="mb-3">Personal</label>
                                        <select class="form-control select2" style="width: 80%;"
                                            id="staff-{{ $room->id }}" name="staff" required>
                                            <option value="">Seleccionar personal</option>
                                            @foreach (\App\Models\Staff::all() as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="deep-{{ $room->id }}" value="deep" checked>
                                                <label class="form-check-label" for="deep-{{ $room->id }}">PROFUNDA /
                                                    60 (min)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cleaning_type"
                                                    id="quick-{{ $room->id }}" value="quick">
                                                <label class="form-check-label" for="quick-{{ $room->id }}">RÁPIDA /
                                                    30 (min)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Limpiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para Habitaciones Reservadas -->
            @if ($room->is_reserved_today)
                <div class="modal fade" id="modal-Reservada-{{ $room->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-orange-custom text-white">
                                <h5 class="modal-title">Opciones - Habitación Nº {{ $room->room_number }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-primary enter-reservation-btn"
                                            data-room-id="{{ $room->id }}"
                                            data-client-id="{{ $room->current_reservation->client_id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Entrar</span>
                                                <i class="fas fa-sign-in-alt mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <button
                                            class="btn btn-light btn-block option-btn text-danger cancel-reservation-btn"
                                            data-reservation-id="{{ $room->current_reservation->id }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <span>Cancelar</span>
                                                <i class="fas fa-times-circle mt-2"></i>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
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

        .small-box {
            border-radius: 10px;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .small-box .inner {
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
            position: relative;
        }

        .small-box .inner h3 {
            margin-bottom: 10px;
        }

        .small-box .inner p {
            margin: 0;
        }

        .small-box .inner .client-name {
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .staff-name {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .reservation-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .entry-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .inner .countdown-wrapper {
            position: absolute;
            bottom: 0px;
            /* Ajustado de 5px a 0px para bajar el contador */
            right: 10px;
            text-align: right;
        }

        .small-box .inner .countdown {
            line-height: 1;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .small-box .inner .countdown .time-card {
            border-radius: 5px;
            padding: 5px 6px;
            text-align: center;
            min-width: 36px;
            height: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .small-box .inner .countdown[data-entry-type] .time-card {
            background-color: #ff6666;
        }

        .small-box .inner .countdown[data-cleaning-type] .time-card {
            background-color: #66b0ff;
        }

        .small-box .inner .countdown .time-remaining {
            font-size: 1.44rem;
            font-weight: bold;
            line-height: 1;
        }

        .small-box .inner .countdown small {
            font-size: 0.72rem;
            line-height: 1;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .small-box .inner .countdown .mr-1 {
            margin-right: 6px;
        }

        .small-box .inner .countdown.negative .time-remaining {
            color: red;
        }

        .small-box .inner .countdown.expired .time-remaining {
            color: #fff;
        }

        .small-box .icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .small-box-footer {
            flex-shrink: 0;
        }

        .small-box-footer.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .bg-orange-custom {
            background-color: #ff8c00 !important;
            color: white !important;
        }

        .modal-header.bg-danger {
            background-color: #dc3545 !important;
        }

        .modal-header.bg-warning {
            background-color: #ffc107 !important;
        }

        .modal-header.bg-orange-custom {
            background-color: #ff8c00 !important;
        }

        .option-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ced4da !important;
            background-color: #f8f9fa !important;
            transition: background-color 0.2s;
            height: 80px;
            padding: 10px;
        }

        .option-btn:hover {
            background-color: #e9ecef !important;
        }

        .option-btn .d-flex {
            width: 100%;
            height: 100%;
        }

        .option-btn span {
            font-size: 0.9rem;
            text-align: center;
            line-height: 1.2;
        }

        .option-btn i {
            font-size: 1.5rem;
        }

        .option-btn.text-danger i,
        .option-btn.text-danger span {
            color: #dc3545 !important;
        }

        .option-btn.text-success i,
        .option-btn.text-success span {
            color: #28a745 !important;
        }

        .option-btn.text-info i,
        .option-btn.text-info span {
            color: #17a2b8 !important;
        }

        .option-btn.text-secondary i,
        .option-btn.text-secondary span {
            color: #6c757d !important;
        }

        .option-btn.text-primary i,
        .option-btn.text-primary span {
            color: #007bff !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#level').on('change', function() {
                var selectedLevel = $(this).val();
                $('.col-lg-3').each(function() {
                    var roomLevel = $(this).data('level');
                    if (selectedLevel === '' || roomLevel == selectedLevel) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            var startTime = Date.now() / 1000;

            function updateCountdown() {
                $('.countdown').each(function() {
                    var $countdown = $(this);
                    var checkOutTimestamp = parseInt($countdown.attr('data-checkout'));
                    var serverTime = parseInt($countdown.attr('data-server-time'));
                    var isExpired = $countdown.attr('data-is-expired') === 'true';

                    if (isNaN(checkOutTimestamp)) {
                        console.error('Invalid checkOutTimestamp:', checkOutTimestamp);
                        return;
                    }

                    var currentTime;
                    if (!isNaN(serverTime) && serverTime > 1000000000) {
                        var now = Date.now() / 1000;
                        var elapsedTime = now - startTime;
                        currentTime = serverTime + elapsedTime;
                    } else {
                        console.warn('ServerTime is invalid or not available, using client time');
                        currentTime = Date.now() / 1000;
                    }

                    var distance = (checkOutTimestamp - currentTime) * 1000;

                    var days, hours, minutes, seconds;
                    var totalSeconds = Math.floor(Math.abs(distance) / 1000);
                    var isNegative = distance < 0;

                    var entryType = $countdown.attr('data-entry-type');
                    var cleaningType = $countdown.attr('data-cleaning-type');

                    // Mapear el nombre de la tarifa a un tipo de entrada
                    if (entryType) {
                        // Asumiendo que el nombre de la tarifa contiene "HORA" para "4_hours", "NOCHE" para "full_night", etc.
                        if (entryType.includes('HORA')) {
                            entryType = '4_hours';
                        } else if (entryType.includes('NOCHE')) {
                            entryType = 'full_night';
                        } else if (entryType.includes('MES')) {
                            entryType = 'month';
                        } else {
                            entryType = 'unknown';
                        }
                    }

                    // Calcular días, horas, minutos y segundos
                    days = Math.floor(totalSeconds / (24 * 60 * 60));
                    totalSeconds %= (24 * 60 * 60);
                    hours = Math.floor(totalSeconds / (60 * 60));
                    totalSeconds %= (60 * 60);
                    minutes = Math.floor(totalSeconds / 60);
                    seconds = totalSeconds % 60;

                    var $daysElement = $countdown.find('.time-remaining.days');
                    var $hoursElement = $countdown.find('.time-remaining.hours');
                    var $minutesElement = $countdown.find('.time-remaining.minutes');
                    var $secondsElement = $countdown.find('.time-remaining.seconds');

                    if (cleaningType) {
                        // Para contadores de limpieza (como "Limpieza Rápida")
                        var maxSeconds = cleaningType === 'deep' ? 60 * 60 : 30 * 60;
                        if (totalSeconds > maxSeconds) {
                            totalSeconds = maxSeconds;
                            days = Math.floor(totalSeconds / (24 * 60 * 60));
                            totalSeconds %= (24 * 60 * 60);
                            hours = Math.floor(totalSeconds / (60 * 60));
                            totalSeconds %= (60 * 60);
                            minutes = Math.floor(totalSeconds / 60);
                            seconds = totalSeconds % 60;
                        }

                        if (isExpired || (days === 0 && hours === 0 && minutes === 0 && seconds === 0)) {
                            $countdown.addClass('expired');
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text('0');
                            if ($hoursElement.length) $hoursElement.text('00');
                            if ($minutesElement.length) $minutesElement.text('00');
                            if ($secondsElement.length) $secondsElement.text('00');
                            $countdown.attr('data-is-expired', 'true');
                            return; // Detener actualización si está expirado
                        }

                        $countdown.removeClass('expired');
                        $countdown.removeClass('negative');
                        if ($daysElement.length) $daysElement.text(days);
                        if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math.abs(
                            hours) : Math.abs(hours));
                        if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' + Math
                            .abs(minutes) : Math.abs(minutes));
                        if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' + Math
                            .abs(seconds) : Math.abs(seconds));

                        if (days === 0 && hours === 0 && minutes === 0 && seconds === 0) {
                            $countdown.attr('data-is-expired', 'true');
                            $countdown.addClass('expired');
                        }
                    } else if (entryType) {
                        // Para contadores de ocupación (como "4 HORAS | OCUPADO")
                        if (entryType === '4_hours' && distance > 0) {
                            var maxSecondsFor4Hours = 4 * 60 * 60;
                            if (totalSeconds > maxSecondsFor4Hours) {
                                totalSeconds = maxSecondsFor4Hours;
                                days = Math.floor(totalSeconds / (24 * 60 * 60));
                                totalSeconds %= (24 * 60 * 60);
                                hours = Math.floor(totalSeconds / (60 * 60));
                                totalSeconds %= (60 * 60);
                                minutes = Math.floor(totalSeconds / 60);
                                seconds = totalSeconds % 60;
                            }
                        }

                        if (isNegative) {
                            $countdown.addClass('negative');
                            if ($daysElement.length) $daysElement.text(days !== 0 ? '-' + days : days);
                            if ($hoursElement.length) $hoursElement.text(hours !== 0 ? '-' + (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)) : (Math.abs(
                                hours) < 10 ? '0' + Math.abs(hours) : Math.abs(hours)));
                            if ($minutesElement.length) $minutesElement.text(minutes !== 0 ? '-' + (Math
                                    .abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)) :
                                (Math.abs(minutes) < 10 ? '0' + Math.abs(minutes) : Math.abs(minutes)));
                            if ($secondsElement.length) $secondsElement.text(seconds !== 0 ? '-' + (Math
                                    .abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)) :
                                (Math.abs(seconds) < 10 ? '0' + Math.abs(seconds) : Math.abs(seconds)));
                        } else {
                            $countdown.removeClass('negative');
                            if ($daysElement.length) $daysElement.text(days);
                            if ($hoursElement.length) $hoursElement.text(Math.abs(hours) < 10 ? '0' + Math
                                .abs(hours) : Math.abs(hours));
                            if ($minutesElement.length) $minutesElement.text(Math.abs(minutes) < 10 ? '0' +
                                Math.abs(minutes) : Math.abs(minutes));
                            if ($secondsElement.length) $secondsElement.text(Math.abs(seconds) < 10 ? '0' +
                                Math.abs(seconds) : Math.abs(seconds));
                        }
                    }
                });
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);

            $('.small-box').each(function() {
                var roomNumber = $(this).find('h3').text();
                var status = $(this).data('status');
                console.log(`Room ${roomNumber} - Status: ${status}`);
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

            @if (session('showCajaCerradaAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Para poder realizar esta operación es necesario Aperturar Caja',
                    html: 'Para poder realizar esta operación es necesario Aperturar Caja<br><br>¿Está Usted de acuerdo?',
                    showCancelButton: false,
                    confirmButtonText: 'Sí, Adelante',
                    confirmButtonColor: '#28a745',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('showCajaOtroUsuarioAlert'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia!',
                    text: 'Caja aperturada por otro usuario. Espere que el usuario responsable cierre la caja.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#007bff',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('caja.arqueos.create') }}";
                    }
                });
            @endif

            @if (session('openModal'))
                $('#{{ session('openModal') }}').modal('show');
            @endif

            $('.enter-reservation-btn').click(function() {
                const roomId = $(this).data('room-id');
                const clientId = $(this).data('client-id');
                window.location.href = '{{ route('entradas.recepcion', ['room' => ':room']) }}'.replace(
                    ':room', roomId) + '?client_id=' + clientId;
            });

            $('.cancel-reservation-btn').click(function() {
                const reservationId = $(this).data('reservation-id');
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se cancelará la reserva. Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Cancelar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('reservas.cancel') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reservation_id: reservationId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Reserva cancelada correctamente.',
                                        timer: 2500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo cancelar la reserva.',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });

        function confirmFinishCleaning(event, element) {
            event.preventDefault();
            var roomId = $(element).data('room-id');
            var roomNumber = $(element).data('room-number');
            var finishUrl = $(element).attr('href');

            Swal.fire({
                title: '¿La habitación ' + roomNumber + ' ya está disponible?',
                showCancelButton: true,
                confirmButtonText: 'Ya está Limpio',
                cancelButtonText: 'Aún no',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#dc3545',
                icon: 'question'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = finishUrl;
                }
            });
        }
    </script>
@stop
