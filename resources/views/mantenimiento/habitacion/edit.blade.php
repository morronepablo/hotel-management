@extends('adminlte::page')

@section('content_header')
    <h1><b>Actualizar habitación</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('mantenimiento.habitacion.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_number">Número de Habitación</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('room_number') ? 'is-invalid' : '' }}"
                                        value="{{ old('room_number', $room->room_number) }}" name="room_number"
                                        id="room_number" required>
                                    @error('room_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level_id">Nivel/Piso</label>
                                    <select class="form-control select2 {{ $errors->has('level_id') ? 'is-invalid' : '' }}"
                                        name="level_id" id="level_id" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->id }}"
                                                {{ old('level_id', $room->level_id) == $level->id ? 'selected' : '' }}>
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_type_id">Tipo de Habitación</label>
                                    <select
                                        class="form-control select2 {{ $errors->has('room_type_id') ? 'is-invalid' : '' }}"
                                        name="room_type_id" id="room_type_id" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($roomTypes as $roomType)
                                            <option value="{{ $roomType->id }}"
                                                {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                                {{ $roomType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_type_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}"
                                        name="status" id="status" required>
                                        <option value="Disponible"
                                            {{ old('status', $room->status) == 'Disponible' ? 'selected' : '' }}>
                                            Disponible
                                        </option>
                                        <option value="Ocupada"
                                            {{ old('status', $room->status) == 'Ocupada' ? 'selected' : '' }}>
                                            Ocupada
                                        </option>
                                        <option value="Para la Limpieza"
                                            {{ old('status', $room->status) == 'Para la Limpieza' ? 'selected' : '' }}>
                                            Para la Limpieza
                                        </option>
                                        <option value="Limpieza Profunda"
                                            {{ old('status', $room->status) == 'Limpieza Profunda' ? 'selected' : '' }}>
                                            Limpieza Profunda
                                        </option>
                                        <option value="Limpieza Rápida"
                                            {{ old('status', $room->status) == 'Limpieza Rápida' ? 'selected' : '' }}>
                                            Limpieza Rápida
                                        </option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('mantenimiento.habitacion.index') }}"
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .main-footer {
            background-color: #343a40;
            color: #ffffff;
            border-top: 2px solid #007bff;
        }

        .main-footer a {
            color: #17a2b8;
        }

        .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.75rem);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Seleccionar",
                allowClear: true
            });
        });
    </script>
@stop
