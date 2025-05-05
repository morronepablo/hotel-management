@extends('adminlte::page')

@section('content_header')
    <h1><b>Actualizar un usuario</b></h1>
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
                    <form action="{{ route('acceso.usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Rol</label>
                                    @if ($usuario->name === 'Admin')
                                        <select name="role" id="select_role"
                                            class="form-control select2 {{ $errors->has('role') ? 'is-invalid' : '' }}"
                                            style="height: 50px !important;" disabled>
                                            <option value="" disabled>Seleccione un rol</option>
                                            <!-- Opción vacía para el placeholder -->
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ $usuario->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    @else
                                        <select name="role" id="select_role"
                                            class="form-control select2 {{ $errors->has('role') ? 'is-invalid' : '' }}"
                                            style="height: 50px !important;" required>
                                            <option value="" disabled>Seleccione un rol</option>
                                            <!-- Opción vacía para el placeholder -->
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ $usuario->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nombre</label>
                                    @if ($usuario->name === 'Admin')
                                        <input type="text"
                                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            value="{{ old('name', $usuario->name) }}" name="name" disabled>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    @else
                                        <input type="text"
                                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            value="{{ old('name', $usuario->name) }}" name="name" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Email</label>
                                    <input type="email"
                                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                        value="{{ old('email', $usuario->email) }}" name="email" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <input type="password"
                                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        value="{{ old('password') }}" name="password">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <input type="password"
                                        class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                        value="{{ old('password_confirmation') }}" name="password_confirmation">
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('acceso.usuarios') }}"
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

@section('adminlte_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .main-footer {
            background-color: #343a40;
            /* Fondo oscuro */
            color: #ffffff;
            /* Texto blanco */
            border-top: 2px solid #007bff;
            /* Borde superior azul */
        }

        .main-footer a {
            color: #17a2b8;
            /* Color de los enlaces */
        }
    </style>
    <style>
        .select2-container .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + .75rem + 2px);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + .75rem + 2px);
        }
    </style>
    @stack('css')
    @yield('css')
@stop

@section('adminlte_js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select_role').select2({
                placeholder: "Seleccione un rol",
                allowClear: false
            });
        });
    </script>
    @stack('js')
    @yield('js')
@stop
