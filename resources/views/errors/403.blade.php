@extends('adminlte::page')

@section('title', '403 - Acceso no autorizado')

@section('content_header')
    <center>
        <h1 class="text-danger"><b>403 - Acceso no autorizado</b></h1>
    </center>
    <hr>
@stop

@section('content')
    <br><br><br><br><br>
    <div class="text-center">
        <img src="{{ url('/images/403.png') }}" alt="Imagen 403" width="400px">
        <h3>No tiene permiso para acceder a esta página.</h3>
        <p>Por favor, contacte al administrador del sistema si cree que es un error.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Regresar</a>
    </div>
@stop
