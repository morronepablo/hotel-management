@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalle del Producto</b></h1>
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
                                <label for="codigo">Código</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $producto->codigo }}" name="codigo" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="producto">Producto</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $producto->producto }}" name="producto" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_id">Categoría</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $producto->categoria->denominacion }}" name="categoria_id" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="imagen">Imagen</label>
                                <div>
                                    <img src="{{ asset('uploads/productos/' . $producto->imagen) }}"
                                        alt="{{ $producto->producto }}"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Stock</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $producto->stock }}" name="stock" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio">Precio</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ number_format($producto->precio, 2) }}" name="precio" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control border-info bg-white" name="descripcion" rows="3" disabled>{{ $producto->descripcion }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Fecha Registro</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $producto->created_at->format('d/m/Y H:i:s') }}" name="date" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="{{ route('almacen.producto') }}"
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
