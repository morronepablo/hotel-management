@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalle de la Habitación</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-9">
            <div class="callout callout-info">
                <div class="card-header">
                    <h3 class="card-title text-info text-bold">Datos Registrados</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="room_number">Número de Habitación</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $room->room_number }}" name="room_number" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="level_id">Nivel/Piso</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $room->level->name ?? 'N/A' }}" name="level_id" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="room_type_id">Tipo de Habitación</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $room->roomType->name ?? 'N/A' }}" name="room_type_id" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Estado</label>
                                <input type="text" class="form-control border-info bg-white" value="{{ $room->status }}"
                                    name="status" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Fecha Registro</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $room->created_at->format('d/m/Y H:i:s') }}" name="date" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="{{ route('mantenimiento.habitacion.index') }}"
                                class="btn btn-secondary text-white text-decoration-none"><i class="fas fa-reply"></i>
                                Volver</a>
                        </div>
                    </div>
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
    </style>
@stop

@section('js')
@stop
