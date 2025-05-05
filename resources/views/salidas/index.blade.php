@extends('adminlte::page')

@section('title', 'Verificación de Salidas')

@section('content_header')
    <h1>Verificación de Salidas</h1>
@endsection

@section('content')
    <div class="row" style="margin-top: 20px;">
        @forelse ($entradas as $entry)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Nro: {{ $entry->room->room_number }}</h3>

                        <!-- Contenedor de dos columnas -->
                        <div class="two-columns">
                            <!-- Columna izquierda -->
                            <div class="left-column">
                                <p>{{ $entry->roomType->name ?? 'PERSONALIZADO' }}</p>
                                <p class="client-name">
                                    {{ $entry->client->name }} {{ $entry->client->lastname }}
                                </p>
                                <p class="entry-dates">
                                    F. Entrada: {{ \Carbon\Carbon::parse($entry->check_in)->format('d/m/Y H:i') }}<br>
                                    F. Hora Salida:
                                    {{ $entry->latest_check_out ? $entry->latest_check_out->format('d/m/Y H:i') : 'No disponible' }}
                                </p>
                            </div>

                            <!-- Columna derecha -->
                            <div class="right-column">
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Total:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($entry->totalAPagar, 2, '.', ',') }}
                                    </span>
                                </div>
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Pagado:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($entry->totalPagado, 2, '.', ',') }}
                                    </span>
                                </div>
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Deuda:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($entry->saldoDeudor, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <a href="{{ route('salidas.show', $entry) }}" class="small-box-footer">
                        CULMINAR <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="alert alert-info">
                    No hay habitaciones ocupadas para mostrar.
                </div>
            </div>
        @endforelse
    </div>
@endsection

@section('css')
    <style>
        .two-columns {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .left-column,
        .right-column {
            flex: 1;
            padding: 5px;
        }

        .right-column {
            text-align: right;
        }

        .small-box {
            border-radius: 10px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .small-box .inner {
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
        }

        .small-box .inner h3 {
            margin-bottom: 10px;
        }

        .small-box .inner p {
            margin: 0<article class="fas fa-times"></article>
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

        .small-box .inner .entry-dates {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .small-box .icon {
            font-size: 2rem;
            opacity: 0.3;
        }

        .small-box-footer {
            flex-shrink: 0;
            margin-top: 5px;
        }

        .bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
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

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: '{{ session('info') }}',
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
        });
    </script>
@endsection
