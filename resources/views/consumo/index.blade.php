@extends('adminlte::page')

@section('title', 'Consumo | Atención al Huésped')

@section('content_header')
    <h1>Consumo | Atención al Huésped</h1>
@endsection

@section('content')
    <div class="row" style="margin-top: 20px;">
        @forelse ($entradas as $entry)
            @if ($entry->consumo && $entry->consumo->detalles->isNotEmpty())
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
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
                                    <span style="font-size: 12px;">
                                        <strong>Consumo:</strong> $
                                        {{ number_format($entry->totalConsumos, 2, '.', ',') }}
                                    </span><br>
                                    <span style="font-size: 12px;">
                                        <strong>Pagado:</strong> $ {{ number_format($entry->totalPagado, 2, '.', ',') }}
                                    </span><br>
                                    <span style="font-size: 12px;">
                                        <strong>Deuda:</strong> $
                                        {{ number_format($entry->saldoDeudor, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="{{ route('consumo.create', $entry->id) }}" class="small-box-footer">
                            VENDER <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-md-12">
                <div class="alert alert-info">
                    No hay habitaciones ocupadas en este momento.
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

        .bg-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }
    </style>
@endsection
