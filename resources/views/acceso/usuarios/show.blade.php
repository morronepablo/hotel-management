@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalle del Usuario</b></h1>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Rol</label>
                                <select name="role" id="select_role" class="form-control border-info bg-white" disabled>
                                    <option value="{{ $usuario->roles->pluck('name')->implode(', ') }}">
                                        {{ $usuario->roles->pluck('name')->implode(', ') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control border-info bg-white" value="{{ $usuario->name }}"
                                    name="name" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Email</label>
                                <input type="email" class="form-control border-info bg-white"
                                    value="{{ $usuario->email }}" name="email" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Fecha Registro</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $usuario->created_at->format('d/m/Y H:i:s') }}" name="date" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="{{ route('acceso.usuarios') }}"
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
            /* Fondo oscuro */
            color: #ffffff;
            /* Texto blanco */
            border-top: 2px solid #007bff;
            /* Borde superior azul */
        }

        .main-footer a {
            color: #17a2b8;
            /* Color de los enlaces */
        }
    </style>
@stop

@section('js')

@stop
