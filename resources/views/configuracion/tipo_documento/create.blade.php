@extends('adminlte::page')

@section('content_header')
    <h1><b>Registro de nuevo tipo de documento</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('configuracion.tipo_documento.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                                        value="{{ old('nombre') }}" name="nombre" required>
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitud">Longitud</label>
                                    <input type="number"
                                        class="form-control {{ $errors->has('longitud') ? 'is-invalid' : '' }}"
                                        value="{{ old('longitud') }}" name="longitud" required>
                                    @error('longitud')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('configuracion.tipo_documento') }}"
                                    class="btn btn-secondary text-white text-decoration-none"><i class="fas fa-reply"></i>
                                    Volver</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-regular fa-floppy-disk"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </form>
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

@section('adminlte_css')
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
    @stack('css')
    @yield('css')
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
