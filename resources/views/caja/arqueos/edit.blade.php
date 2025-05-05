@extends('adminlte::page')

@section('content_header')
    <h1><b>Modificar un arqueo</b> - Usuario: {{ $arqueo->usuario->name }}</h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.arqueos.update', $arqueo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_apertura">Fecha Apertura</label> <b class="text-danger">*</b>
                                    <input type="datetime-local"
                                        class="form-control {{ $errors->has('fecha_apertura') ? 'is-invalid' : '' }}"
                                        value="{{ old('fecha_apertura', $arqueo->fecha_apertura) }}" name="fecha_apertura"
                                        required>
                                    @error('fecha_apertura')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_inicial">Monto Inicial</label> <b class="text-danger">*</b>
                                    <input type="text"
                                        class="form-control {{ $errors->has('monto_inicial') ? 'is-invalid' : '' }} text-right"
                                        value="{{ old('monto_inicial', $arqueo->monto_inicial) }}" name="monto_inicial"
                                        required>
                                    @error('monto_inicial')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
                                        value="{{ old('descripcion', $arqueo->descripcion) }}" name="descripcion">
                                    @error('descripcion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('caja.arqueos.index') }}"
                                    class="btn btn-secondary text-white text-decoration-none"><i class="fas fa-reply"></i>
                                    Volver</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-regular fa-floppy-disk"></i> Actualizar
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
