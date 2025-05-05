@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalle del Tipo de Habitación</b></h1>
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
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $roomType->name }}" name="name" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_4_hours">Precio (4 horas)</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $roomType->price_4_hours ? number_format($roomType->price_4_hours, 2) : 'N/A' }}"
                                    name="price_4_hours" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_full_night">Precio (Noche Completa)</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $roomType->price_full_night ? number_format($roomType->price_full_night, 2) : 'N/A' }}"
                                    name="price_full_night" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_month">Precio (Mes)</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $roomType->price_month ? number_format($roomType->price_month, 2) : 'N/A' }}"
                                    name="price_month" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Fecha Registro</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $roomType->created_at->format('d/m/Y H:i:s') }}" name="date" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="{{ route('mantenimiento.tipo_habitacion.index') }}"
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
