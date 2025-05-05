@extends('adminlte::page')

@section('content_header')
    <h1><b>Entradas (Recepción)</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Habitaciones</h3>
                </div>
                <div class="card-body">
                    @if ($rooms->isEmpty())
                        <div class="alert alert-warning">
                            No hay habitaciones registradas.
                        </div>
                    @else
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Habitación</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $room)
                                    <tr>
                                        <td>{{ $room->room_number }}</td>
                                        <td>{{ $room->roomType->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($roomStatuses[$room->id] == 'Disponible')
                                                <span class="badge badge-success">Disponible</span>
                                            @else
                                                <span class="badge badge-danger">Ocupada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($roomStatuses[$room->id] == 'Disponible')
                                                <a href="{{ route('entradas.create', $room->id) }}"
                                                    class="btn btn-primary btn-sm" title="Registrar Entrada">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-primary btn-sm" disabled>
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
