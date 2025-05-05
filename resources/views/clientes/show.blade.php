@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalles del Cliente #{{ $client->id }}</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información del Cliente</h3>
                    <div class="card-tools">
                        <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> {{ $client->id }}</p>
                            <p><strong>Nombre:</strong> {{ $client->name }}</p>
                            <p><strong>Apellido:</strong> {{ $client->lastname }}</p>
                            <p><strong>Tipo de Documento:</strong>
                                {{ $client->tipoDocumento ? $client->tipoDocumento->nombre : 'N/A' }}</p>
                            <p><strong>N° Documento:</strong> {{ $client->nro_documento }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>N° Matrícula:</strong> {{ $client->nro_matricula ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
                            <p><strong>Teléfono:</strong> {{ $client->phone }}</p>
                            <p><strong>Dirección:</strong> {{ $client->address ?? 'N/A' }}</p>
                            <p><strong>Fecha de Creación:</strong> {{ $client->created_at->format('d/m/Y H:i') }}</p>
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
