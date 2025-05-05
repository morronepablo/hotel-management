@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalles del Tipo de Habitación</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Tipo de Habitación: {{ $roomType->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('mantenimiento.tipo_habitacion.tarifas', $roomType->id) }}"
                            class="btn btn-primary btn-sm" title="Gestionar Tarifas">
                            <i class="fas fa-money-bill"></i> Gestionar Tarifas
                        </a>
                        <a href="{{ route('mantenimiento.tipo_habitacion.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $roomType->name }}</p>
                            <p><strong>Descripción:</strong> {{ $roomType->description ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <h5 class="mt-4">Tarifas Asociadas</h5>
                    <table class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="text-center">Nro.</th>
                                <th scope="col" class="text-center">Nombre</th>
                                <th scope="col" class="text-center">Tipo</th>
                                <th scope="col" class="text-center">Duración (Horas)</th>
                                <th scope="col" class="text-center">Hora de Salida</th>
                                <th scope="col" class="text-center">Precio</th>
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
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No hay tarifas asociadas a este tipo de
                                        habitación.</td>
                                </tr>
                            @endif
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
    <script>
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
