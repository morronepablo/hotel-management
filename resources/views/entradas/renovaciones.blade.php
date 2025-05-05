{{-- @extends('adminlte::page')

@section('title', 'Renovaciones | Atención al Huésped')

@section('content_header')
    <h1>Renovaciones | Atención al Huésped</h1>
@endsection

@section('content')
    <div class="row" style="margin-top: 20px;">
        @forelse ($entries as $entry)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
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
                                    <strong>Tipo:</strong>
                                    @if ($entry->entry_type == '4_hours')
                                        4 Horas
                                    @elseif ($entry->entry_type == 'full_night')
                                        Toda la Noche
                                    @elseif ($entry->entry_type == 'month')
                                        Por Mes
                                    @endif
                                </span><br>
                                <span style="font-size: 12px;">
                                    <strong>Total:</strong> $ {{ number_format($entry->total, 2, '.', ',') }}
                                </span><br>
                                <span style="font-size: 12px;">
                                    <strong>Deuda:</strong> $ {{ number_format($entry->debt, 2, '.', ',') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <a href="{{ route('renewals.create', $entry->id) }}" class="small-box-footer">
                        RENOVAR <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
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

        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
    </style>
@endsection









@extends('adminlte::page')

@section('content_header')
    <h1>Renovaciones | Atención al Huésped</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        @forelse ($renewals as $renewal)
            <div class="col-md-4">
                <div class="card card-teal">
                    <div class="card-header">
                        <h3 class="card-title">Nro: {{ $renewal->entry->room->room_number }}</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Apart:</strong> {{ $renewal->entry->roomType->name ?? 'N/A' }}</p>
                        <p><strong>Cliente:</strong> {{ $renewal->entry->client->name }}
                            {{ $renewal->entry->client->lastname }}</p>
                        <p><strong>F. Entrada:</strong>
                            {{ \Carbon\Carbon::parse($renewal->entry->check_in)->format('d/m/Y H:i') }}</p>
                        <p><strong>F. Salida:</strong>
                            {{ $renewal->latest_check_out ? $renewal->latest_check_out->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p><strong>Tipo:</strong> {{ $renewal->roomTypeTariff->name ?? 'N/A' }}</p>
                        <p><strong>Total:</strong> $ {{ number_format($renewal->total, 2) }}</p>
                        <p><strong>Deuda:</strong> $ {{ number_format($renewal->debt, 2) }}</p>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('renewals.create', $renewal->entry->id) }}" class="btn btn-primary">
                            RENOVAR <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="alert alert-info">
                    No hay renovaciones registradas.
                </div>
            </div>
        @endforelse
    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 0;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .card-teal {
            border-top: 3px solid #20c997;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@stop --}}






























{{-- @extends('adminlte::page')

@section('title', 'Renovaciones | Atención al Huésped')

@section('content_header')
    <h1>Renovaciones | Atención al Huésped</h1>
@endsection

@section('content')
    <div class="row" style="margin-top: 20px;">
        @forelse ($entries as $entry)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
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
                                    <strong>Tipo:</strong>
                                    @if ($entry->entry_type == '4_hours')
                                        4 Horas
                                    @elseif ($entry->entry_type == 'full_night')
                                        Toda la Noche
                                    @elseif ($entry->entry_type == 'month')
                                        Por Mes
                                    @endif
                                </span><br>
                                <span style="font-size: 12px;">
                                    <strong>Total:</strong> $ {{ number_format($entry->total, 2, '.', ',') }}
                                </span><br>
                                <span style="font-size: 12px;">
                                    <strong>Pagado:</strong> $
                                    {{ number_format($entry->total - $entry->debt, 2, '.', ',') }}
                                </span><br>
                                <span style="font-size: 12px;">
                                    <strong>Deuda:</strong> $ {{ number_format($entry->debt, 2, '.', ',') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <a href="{{ route('renewals.create', $entry->id) }}" class="small-box-footer">
                        RENOVAR <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
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

        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
    </style>
@endsection --}}








































@extends('adminlte::page')

@section('title', 'Renovaciones | Atención al Huésped')

@section('content_header')
    <h1>Renovaciones | Atención al Huésped</h1>
@endsection

@section('content')
    <div class="row" style="margin-top: 20px;">
        @forelse ($renewals as $renewal)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>Nro: {{ $renewal->entry->room->room_number }}</h3>

                        <!-- Contenedor de dos columnas -->
                        <div class="two-columns">
                            <!-- Columna izquierda -->
                            <div class="left-column">
                                <p>{{ $renewal->entry->roomType->name ?? 'PERSONALIZADO' }}</p>
                                <p class="client-name">
                                    {{ $renewal->entry->client->name }} {{ $renewal->entry->client->lastname }}
                                </p>
                                <p class="entry-dates">
                                    F. Entrada:
                                    {{ \Carbon\Carbon::parse($renewal->entry->check_in)->format('d/m/Y H:i') }}<br>
                                    F. Hora Salida:
                                    {{ $renewal->latest_check_out ? $renewal->latest_check_out->format('d/m/Y H:i') : 'No disponible' }}
                                </p>
                            </div>

                            <!-- Columna derecha -->
                            <div class="right-column">
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Total:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($renewal->total, 2, '.', ',') }}
                                    </span>
                                </div>
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Pagado:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($renewal->total - $renewal->debt, 2, '.', ',') }}
                                    </span>
                                </div>
                                <div class="two-columns">
                                    <span style="font-size: 12px;">
                                        <strong>Deuda:</strong>
                                    </span>
                                    <span style="font-size: 12px;">
                                        $ {{ number_format($renewal->debt, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <a href="{{ route('renewals.create', $renewal->entry->id) }}" class="small-box-footer">
                        RENOVAR <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="alert alert-info">
                    No hay renovaciones registradas.
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

        .bg-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
    </style>
@endsection
