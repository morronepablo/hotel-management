@extends('adminlte::page')

@section('content_header')
    <h1><b>Calendario de Reservas</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Vista de Reservas por Fecha</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('reservas.calendario', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <span class="btn btn-outline-secondary btn-sm disabled">
                                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('es')->translatedFormat('F Y') }}
                                -
                                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth()->locale('es')->translatedFormat('F Y') }}
                            </span>
                            <a href="{{ route('reservas.calendario', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <!-- Botón para registrar cliente -->
                        <button type="button" class="btn btn-primary btn-sm ml-2" id="register-client-btn">
                            <i class="fas fa-user-plus"></i> Registrar Cliente
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($rooms->isEmpty())
                        <div class="alert alert-warning m-3">
                            No hay habitaciones registradas. Por favor, agrega habitaciones para usar el calendario.
                        </div>
                    @else
                        <div class="table-responsive" style="overflow-x: hidden;">
                            <table class="table table-bordered table-sm" id="reservation-calendar">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="position: sticky; left: 0; z-index: 1; width: 80px;"></th>
                                        <th style="position: sticky; left: 80px; z-index: 1; width: 200px;"></th>
                                        @php
                                            $currentMonth = null;
                                            $colspan = 0;
                                        @endphp
                                        @foreach ($days as $dayData)
                                            @if ($currentMonth !== $dayData['month'])
                                                @if ($currentMonth !== null)
                                                    <th colspan="{{ $colspan }}" class="text-center">
                                                        {{ \Carbon\Carbon::createFromDate($dayData['year'], $currentMonth, 1)->locale('es')->translatedFormat('F Y') }}
                                                    </th>
                                                @endif
                                                @php
                                                    $currentMonth = $dayData['month'];
                                                    $colspan = 0;
                                                @endphp
                                            @endif
                                            @php
                                                $colspan++;
                                            @endphp
                                        @endforeach
                                        <th colspan="{{ $colspan }}" class="text-center">
                                            {{ \Carbon\Carbon::createFromDate($dayData['year'], $currentMonth, 1)->locale('es')->translatedFormat('F Y') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="position: sticky; left: 0; z-index: 1; width: 80px;">Habitación</th>
                                        <th style="position: sticky; left: 80px; z-index: 1; width: 200px;">Tipo</th>
                                        @foreach ($days as $dayData)
                                            <th class="text-center">{{ str_pad($dayData['day'], 2, '0', STR_PAD_LEFT) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rooms as $room)
                                        <tr data-room-id="{{ $room->id }}">
                                            <td
                                                style="position: sticky; left: 0; background: #fff; z-index: 1; width: 80px;">
                                                {{ $room->room_number ?? 'N/A' }}</td>
                                            <td
                                                style="position: sticky; left: 80px; background: #fff; z-index: 1; width: 200px;">
                                                {{ $room->roomType ? $room->roomType->name : 'N/A' }}</td>
                                            @php
                                                $startOfFirstMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
                                                $endOfLastMonth = $startOfFirstMonth->copy()->addMonth()->endOfMonth();
                                                $currentDate = $startOfFirstMonth->copy();

                                                // Preparar los eventos (reservas y ocupaciones)
                                                $reservations = $room->reservations
                                                    ->map(function ($reservation) {
                                                        return [
                                                            'type' => 'reservation',
                                                            'start' => \Carbon\Carbon::parse($reservation->check_in),
                                                            'end' => \Carbon\Carbon::parse($reservation->check_out),
                                                            'details' => $reservation,
                                                        ];
                                                    })
                                                    ->toArray();

                                                $occupations = isset($occupiedEntries[$room->id])
                                                    ? collect($occupiedEntries[$room->id])
                                                        ->map(function ($entry) {
                                                            return [
                                                                'type' => 'occupation',
                                                                'start' => \Carbon\Carbon::parse($entry['check_in']),
                                                                'end' => \Carbon\Carbon::parse(
                                                                    $entry['check_out'],
                                                                )->endOfDay(),
                                                                'details' => null,
                                                            ];
                                                        })
                                                        ->toArray()
                                                    : [];

                                                // Combinar y ordenar eventos por fecha de inicio
                                                $events = array_merge($reservations, $occupations);
                                                usort($events, function ($a, $b) {
                                                    return $a['start'] <=> $b['start'];
                                                });

                                                $eventIndex = 0;
                                            @endphp
                                            @while ($currentDate <= $endOfLastMonth)
                                                @php
                                                    $isOccupied = false;
                                                    $isReserved = false;
                                                    $eventDetails = null;
                                                    $eventSpan = 1;

                                                    // Buscar el próximo evento (priorizando ocupaciones)
                                                    while ($eventIndex < count($events)) {
                                                        $event = $events[$eventIndex];
                                                        $eventStart = $event['start'];
                                                        $eventEnd = $event['end'];

                                                        // Ajustar las fechas al rango del período
                                                        $eventStartInPeriod = $eventStart->lt($startOfFirstMonth)
                                                            ? $startOfFirstMonth
                                                            : $eventStart;
                                                        $eventEndInPeriod = $eventEnd->gt($endOfLastMonth)
                                                            ? $endOfLastMonth
                                                            : $eventEnd;

                                                        if (
                                                            $currentDate->between(
                                                                $eventStartInPeriod,
                                                                $eventEndInPeriod,
                                                                true,
                                                            ) ||
                                                            ($eventStart->isSameDay($eventEnd) &&
                                                                $currentDate->isSameDay($eventStart))
                                                        ) {
                                                            if ($event['type'] === 'occupation') {
                                                                $isOccupied = true;
                                                                $eventDetails = $event['details'];
                                                            } elseif ($event['type'] === 'reservation') {
                                                                $isReserved = true;
                                                                $eventDetails = $event['details'];
                                                            }

                                                            // Calcular el número de celdas que abarca el evento dentro del período
                                                            if ($eventStartInPeriod->isSameDay($eventEndInPeriod)) {
                                                                $eventSpan = 1; // Evento de un solo día
                                                            } else {
                                                                $eventSpan =
                                                                    $eventStartInPeriod->diffInDays($eventEndInPeriod) +
                                                                    1;
                                                            }

                                                            break;
                                                        } elseif ($eventStart->gt($currentDate)) {
                                                            break;
                                                        }
                                                        $eventIndex++;
                                                    }

                                                    // Generar un color aleatorio para la reserva basado en su ID
                                                    if ($isReserved) {
                                                        srand($eventDetails->id); // Semilla basada en el ID
                                                        $minValue = 100; // Para evitar colores muy oscuros
                                                        $maxValue = 255;
                                                        $r = rand($minValue, $maxValue);
                                                        $g = rand($minValue, $maxValue);
                                                        $b = rand($minValue, $maxValue);
                                                        $color = sprintf('#%02x%02x%02x', $r, $g, $b);
                                                    }
                                                @endphp

                                                @if ($isOccupied)
                                                    <td colspan="{{ $eventSpan }}" class="calendar-cell occupied"
                                                        style="background-color: black; color: white; cursor: not-allowed;"
                                                        data-date="{{ $currentDate->format('Y-m-d') }}"
                                                        data-room-id="{{ $room->id }}"></td>
                                                    @php
                                                        $currentDate->addDays($eventSpan);
                                                    @endphp
                                                @elseif ($isReserved)
                                                    @php
                                                        $clientName = $eventDetails->client
                                                            ? $eventDetails->client->name .
                                                                ' ' .
                                                                $eventDetails->client->lastname
                                                            : 'N/A';
                                                        $checkInTime =
                                                            $eventDetails->check_in_time ??
                                                            $eventDetails->check_in->format('H:i');
                                                        $checkOutTime =
                                                            $eventDetails->check_out_time ??
                                                            $eventDetails->check_out->format('H:i');
                                                        $tooltip = "{$clientName} ({$eventDetails->check_in->format(
    'd/m/Y',
)} {$checkInTime} - {$eventDetails->check_out->format(
    'd/m/Y',
)} {$checkOutTime})";
                                                    @endphp
                                                    <td colspan="{{ $eventSpan }}" class="calendar-cell reserved"
                                                        style="background-color: {{ $color }};"
                                                        title="{{ $tooltip }}"
                                                        data-reservation-id="{{ $eventDetails->id }}"
                                                        data-room-id="{{ $room->id }}"
                                                        data-check-in-date="{{ $eventDetails->check_in->format('Y-m-d') }}"
                                                        data-check-in-time="{{ $eventDetails->check_in_time ?? $eventDetails->check_in->format('H:i') }}"
                                                        data-check-out-date="{{ $eventDetails->check_out->format('Y-m-d') }}"
                                                        data-check-out-time="{{ $eventDetails->check_out_time ?? $eventDetails->check_out->format('H:i') }}"
                                                        data-client-id="{{ $eventDetails->client_id }}">
                                                        <div class="reservation-client"
                                                            style="--colspan: {{ $eventSpan }};">
                                                            {{ $clientName }}
                                                        </div>
                                                    </td>
                                                    @php
                                                        $currentDate->addDays($eventSpan);
                                                    @endphp
                                                @else
                                                    <td class="calendar-cell"
                                                        data-date="{{ $currentDate->format('Y-m-d') }}"
                                                        data-room-id="{{ $room->id }}"></td>
                                                    @php
                                                        $currentDate->addDay();
                                                    @endphp
                                                @endif
                                            @endwhile
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar reserva -->
    <div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="reservationModalLabel">Reservar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="reservationForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="reservation_id" id="modal-reservation-id">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="hidden" name="room_id" id="modal-room-id">
                        <div class="form-group">
                            <label for="check_in_date">Fecha (IN)</label>
                            <input type="date" class="form-control" name="check_in_date" id="check_in_date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="check_in_time">Hora (IN)</label>
                            <input type="time" class="form-control" name="check_in_time" id="check_in_time"
                                value="09:00" step="60">
                        </div>
                        <div class="form-group">
                            <label for="check_out_date">Fecha (OUT)</label>
                            <input type="date" class="form-control" name="check_out_date" id="check_out_date">
                        </div>
                        <div class="form-group">
                            <label for="check_out_time">Hora (OUT)</label>
                            <input type="time" class="form-control" name="check_out_time" id="check_out_time"
                                value="10:00" step="60">
                        </div>
                        <div class="form-group">
                            <label for="client_id">Cliente</label>
                            <select class="form-control select2" name="client_id" id="client_id" style="width: 86%"
                                required>
                                <option value="">Seleccionar</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name . ' ' . $client->lastname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para acciones sobre una reserva existente -->
    <div class="modal fade" id="reservationActionModal" tabindex="-1" role="dialog"
        aria-labelledby="reservationActionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="reservationActionModalLabel">Acciones de la Reserva</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Selecciona una acción para la reserva:</p>
                    <input type="hidden" id="action-reservation-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="edit-reservation-btn">Modificar</button>
                    <button type="button" class="btn btn-danger" id="cancel-reservation-btn">Cancelar Reserva</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Cliente -->
    <div class="modal fade" id="client-modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="clientModalLabel">Registrar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
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

        .calendar-cell {
            width: 40px;
            height: 40px;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
        }

        .calendar-cell:hover {
            background-color: #e9ecef;
        }

        .calendar-cell.reserved {
            cursor: pointer;
        }

        .calendar-cell.selected {
            background-color: #b3d7ff !important;
        }

        .calendar-cell.occupied:hover {
            background-color: black !important;
            /* Evitar el hover en celdas ocupadas */
        }

        .reservation-client {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
            max-width: calc(40px * var(--colspan) - 10px);
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.75rem);
        }

        /* Asegurar que el texto sea legible en fondos de colores */
        .calendar-cell.reserved .reservation-client {
            color: #000;
        }

        /* Forzar el ancho de la columna "Tipo" */
        #reservation-calendar th:nth-child(2),
        #reservation-calendar td:nth-child(2) {
            width: 200px !important;
            min-width: 200px !important;
        }

        /* Evitar que la tabla se desborde horizontalmente */
        #reservation-calendar {
            table-layout: fixed;
            width: auto;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/es.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Configurar moment.js para usar español
            moment.locale('es');

            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Seleccionar",
                allowClear: true
            });

            // Generar color para las reservas
            function generateColor(id) {
                const minValue = 100;
                const maxValue = 255;
                const seedrandom = function(seed) {
                    let x = Math.sin(seed) * 10000;
                    return () => {
                        x = Math.sin(x) * 10000;
                        return x - Math.floor(x);
                    };
                };
                const rand = seedrandom(id);
                const r = Math.floor(rand() * (maxValue - minValue + 1)) + minValue;
                const g = Math.floor(rand() * (maxValue - minValue + 1)) + minValue;
                const b = Math.floor(rand() * (maxValue - minValue + 1)) + minValue;
                return `rgb(${r}, ${g}, ${b})`;
            }

            // Variables para el arrastre
            let startDate = null;
            let endDate = null;
            let roomId = null;
            let isDragging = false;
            let currentRow = null;

            // Función para vincular eventos de arrastre
            function bindDragEvents() {
                $('.calendar-cell:not(.reserved):not(.occupied)').off('mousedown mouseover mouseup').on('mousedown',
                    function(e) {
                        e.preventDefault();
                        if (isDragging) return;
                        isDragging = true;
                        startDate = moment($(this).data('date'), 'YYYY-MM-DD');
                        endDate = startDate.clone();
                        roomId = $(this).data('room-id');
                        currentRow = $(this).closest('tr');
                        $(this).addClass('selected');

                        $(document).on('mousemove', handleMouseMove);
                    });

                $(document).on('mouseup', function(e) {
                    if (!isDragging) return;
                    isDragging = false;
                    $(document).off('mousemove', handleMouseMove);
                    currentRow = null;

                    if (startDate && endDate && roomId) {
                        $('#reservationModalLabel').text('Crear Reserva');
                        $('#reservationForm').attr('action', '{{ route('reservas.store') }}');
                        $('#form-method').val('POST');
                        $('#modal-room-id').val(roomId);
                        $('#modal-reservation-id').val('');
                        $('#check_in_date').val(startDate.format('YYYY-MM-DD'));
                        $('#check_in_time').val('09:00');
                        $('#check_out_date').val(endDate.format('YYYY-MM-DD'));
                        $('#check_out_time').val('10:00');
                        $('#client_id').val('').trigger('change');
                        $('#check_in_date, #check_in_time, #check_out_date, #check_out_time').prop(
                            'disabled', false);
                        $('#reservationModal').modal('show');
                    }
                });
            }

            // Manejar selección de días
            function handleMouseMove(e) {
                if (!isDragging || !currentRow) return;

                const $hoveredCell = $(document.elementFromPoint(e.clientX, e.clientY)).closest(
                    '.calendar-cell:not(.reserved):not(.occupied)');
                if ($hoveredCell.length && $hoveredCell.closest('tr').is(currentRow)) {
                    const currentDate = moment($hoveredCell.data('date'), 'YYYY-MM-DD');
                    const rowCells = currentRow.find('.calendar-cell:not(.reserved):not(.occupied)');
                    rowCells.removeClass('selected');

                    if (currentDate.isBefore(startDate)) {
                        endDate = startDate.clone();
                        startDate = currentDate.clone();
                    } else {
                        endDate = currentDate.clone();
                    }

                    rowCells.each(function() {
                        const cellDate = moment($(this).data('date'), 'YYYY-MM-DD');
                        if (cellDate.isBetween(startDate, endDate, null, '[]')) {
                            $(this).addClass('selected');
                        }
                    });
                }
            }

            // Inicializar eventos de arrastre
            bindDragEvents();

            // Acción al hacer clic en una reserva existente
            $('.calendar-cell.reserved').click(function() {
                const reservationId = $(this).data('reservation-id');
                $('#action-reservation-id').val(reservationId);
                $('#reservationActionModal').modal('show');
            });

            // Botón para modificar la reserva
            $('#edit-reservation-btn').click(function() {
                const reservationId = $('#action-reservation-id').val();
                const reservationCell = $(
                    `.calendar-cell.reserved[data-reservation-id="${reservationId}"]`);

                $('#reservationModalLabel').text('Modificar Reserva');
                $('#reservationForm').attr('action', '{{ url('reservas') }}/' + reservationId);
                $('#form-method').val('PUT');
                $('#modal-reservation-id').val(reservationId);
                $('#modal-room-id').val(reservationCell.data('room-id'));
                $('#check_in_date').val(reservationCell.data('check-in-date'));
                $('#check_in_time').val(reservationCell.data('check-in-time'));
                $('#check_out_date').val(reservationCell.data('check-out-date'));
                $('#check_out_time').val(reservationCell.data('check-out-time'));
                $('#client_id').val(reservationCell.data('client-id')).trigger('change');
                $('#check_in_date, #check_in_time, #check_out_date, #check_out_time').prop('disabled',
                    false);

                $('#reservationActionModal').modal('hide');
                $('#reservationModal').modal('show');
            });

            // Botón para cancelar la reserva
            $('#cancel-reservation-btn').click(function() {
                const reservationId = $('#action-reservation-id').val();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esto cambiará el estado de la reserva a Cancelada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cancelar reserva',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('reservas/cancel') }}',
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
                                        text: 'Reserva cancelada exitosamente.',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo cancelar la reserva.',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

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
                    url: '{{ route('clientes.store') }}',
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
                            const $clientSelect = $('#client_id');
                            const newOption = new Option(
                                `${response.client.name} ${response.client.lastname}`,
                                response.client.id,
                                true,
                                true
                            );
                            $clientSelect.append(newOption);
                            $clientSelect.val(response.client.id).trigger('change');
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
                        let errorMessage = 'Hubo un problema al registrar el cliente.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                '<br>');
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

            // Normalizar el formato de check_in_time y check_out_time
            $('#reservationForm').on('submit', function(e) {
                const checkInTime = $('#check_in_time').val();
                const checkOutTime = $('#check_out_time').val();

                if (checkInTime.match(/^\d{2}:\d{2}:\d{2}$/)) {
                    $('#check_in_time').val(checkInTime.substring(0, 5));
                }
                if (checkOutTime.match(/^\d{2}:\d{2}:\d{2}$/)) {
                    $('#check_out_time').val(checkOutTime.substring(0, 5));
                }
            });

            // Limpiar selección al cerrar el modal de creación/edición
            $('#reservationModal').on('hidden.bs.modal', function() {
                $('.calendar-cell').removeClass('selected');
                startDate = null;
                endDate = null;
                roomId = null;
                currentRow = null;
                $('#reservationForm')[0].reset();
                $('#form-method').val('POST');
                $('#reservationForm').attr('action', '{{ route('reservas.store') }}');
                $('#client_id').val('').trigger('change');
            });

            // Limpiar selección al cerrar el modal de acciones
            $('#reservationActionModal').on('hidden.bs.modal', function() {
                $('#action-reservation-id').val('');
            });

            // Mostrar el modal automáticamente si hay errores de validación
            @if ($errors->any())
                $('#reservationModal').modal('show');
                $('#reservationModalLabel').text(
                    '{{ old('_method') == 'PUT' ? 'Modificar Reserva' : 'Crear Reserva' }}');
                $('#reservationForm').attr('action',
                    '{{ old('_method') == 'PUT' ? url('reservas') . '/' . old('reservation_id') : route('reservas.store') }}'
                );
                $('#form-method').val('{{ old('_method', 'POST') }}');
                $('#modal-room-id').val('{{ old('room_id') }}');
                $('#modal-reservation-id').val('{{ old('reservation_id') }}');
                $('#check_in_date').val('{{ old('check_in_date') }}');
                $('#check_in_time').val('{{ old('check_in_time') }}');
                $('#check_out_date').val('{{ old('check_out_date') }}');
                $('#check_out_time').val('{{ old('check_out_time') }}');
                $('#client_id').val('{{ old('client_id') }}').trigger('change');
                $('#check_in_date, #check_in_time, #check_out_date, #check_out_time').prop('disabled', false);
            @endif

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

            @if (session('error') && !$errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@stop
