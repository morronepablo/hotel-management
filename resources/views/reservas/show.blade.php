@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalles de la Reserva</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de la Reserva</h3>
                    <div class="card-tools">
                        <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </a>
                        <a href="{{ route('reservas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $reserva->client->name }}</p>
                            <p><strong>Habitación:</strong> {{ $reserva->room->room_number }} ({{ $reserva->room->type }})
                            </p>
                            <p><strong>Check-In:</strong> {{ $reserva->check_in->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-Out:</strong> {{ $reserva->check_out->format('d/m/Y H:i') }}</p>
                            <p><strong>Monto Total:</strong> {{ number_format($reserva->total_amount, 2, ',', '.') }}</p>
                            <p><strong>Fecha de Creación:</strong> {{ $reserva->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
