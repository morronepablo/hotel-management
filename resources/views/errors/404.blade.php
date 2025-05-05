@extends('adminlte::page')

@section('title', '404 - Página no encontrada')

@section('content_header')
    <center>
        <h1 class="text-danger"><b>404 - Página no encontrada</b></h1>
    </center>
    <hr>
@stop

@section('content')
    <br><br><br><br><br>
    <div class="text-center">
        <img src="{{ url('/images/404.png') }}" alt="Imagen 403" width="400px">
        <h3>No se ha podido encontrar la página.</h3>
        <p>Por favor, contacte al administrador del sistema si cree que es un error.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Regresar</a>
    </div>
@stop
