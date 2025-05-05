@extends('adminlte::page')

@section('content_header')
    <h1><b>Ingreso/Egreso</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.arqueos.store_ingresos_egresos') }}" method="POST">
                        @csrf
                        <input type="text" value="{{ $arqueo->id }}" name="arqueo_id" hidden>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label> <b class="text-danger">*</b>
                                    <select name="tipo"
                                        class="form-control {{ $errors->has('tipo') ? 'is-invalid' : '' }}" required>
                                        <option value="Ingreso" {{ old('tipo') == 'Ingreso' ? 'selected' : '' }}>
                                            Ingreso</option>
                                        <option value="Egreso" {{ old('tipo') == 'Egreso' ? 'selected' : '' }}>
                                            Egreso</option>
                                    </select>
                                    @error('tipo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto">Monto</label> <b class="text-danger">*</b>
                                    <input type="text"
                                        class="form-control {{ $errors->has('monto') ? 'is-invalid' : '' }} text-right"
                                        value="{{ old('monto') }}" name="monto" required>
                                    @error('monto')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
                                        value="{{ old('descripcion') }}" name="descripcion">
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
    <script>
        $(document).ready(function() {
            // Si necesitas algún script adicional para esta vista, puedes añadirlo aquí.
        });
    </script>
@stop
