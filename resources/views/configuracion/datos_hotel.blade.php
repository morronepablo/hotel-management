@extends('adminlte::page')

@section('title', 'Datos del Hotel')

@section('content_header')
    <h1>Datos de la Empresa</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('configuracion.actualizar_datos_hotel') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre del Hotel</label>
                            <input type="text" name="nombre" id="nombre" value="{{ $hotelSetting->nombre ?? '' }}"
                                class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                                placeholder="Nombre del Hotel">
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion"
                                value="{{ $hotelSetting->direccion ?? '' }}"
                                class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
                                placeholder="Dirección">
                            @error('direccion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" value="{{ $hotelSetting->telefono ?? '' }}"
                                class="form-control {{ $errors->has('telefono') ? 'is-invalid' : '' }}"
                                placeholder="Teléfono">
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ruc">CUIT</label>
                            <input type="text" name="cuit" id="cuit" value="{{ $hotelSetting->cuit ?? '' }}"
                                class="form-control {{ $errors->has('cuit') ? 'is-invalid' : '' }}" placeholder="CUIT">
                            @error('cuit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="simbolo_monetario">Símbolo Monetario</label>
                            <input type="text" name="simbolo_monetario" id="simbolo_monetario"
                                value="{{ $hotelSetting->simbolo_monetario ?? '' }}"
                                class="form-control {{ $errors->has('simbolo_monetario') ? 'is-invalid' : '' }}"
                                placeholder="Símbolo Monetario">
                            @error('simbolo_monetario')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control-file">
                            @error('logo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <br>
                            <center>
                                <output id="list">
                                    @if ($hotelSetting->logo !== null)
                                        <img src="{{ asset('uploads/' . $hotelSetting->logo) }}" width="200px"
                                            alt="logo">
                                    @endif
                                </output>
                            </center>
                            <script>
                                function archivo(evt) {
                                    var files = evt.target.files; // FileList object
                                    // Obtenemos la imagen del campo "file".
                                    for (var i = 0, f; f = files[i]; i++) {
                                        //Solo admitimos imágenes.
                                        if (!f.type.match('image.*')) {
                                            continue;
                                        }
                                        var reader = new FileReader();
                                        reader.onload = (function(theFile) {
                                            return function(e) {
                                                // Insertamos la imagen
                                                document.getElementById("list").innerHTML = ['<img class="thumb thumbnail" src="', e
                                                    .target.result, '" width="170px" title="', escape(theFile.name), '"/>'
                                                ].join('');
                                            };
                                        })(f);
                                        reader.readAsDataURL(f);
                                    }
                                }
                                document.getElementById('logo').addEventListener('change', archivo, false);
                            </script>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

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
        // Script para cerrar el modal (si usas un modal, aunque aquí no es necesario)
        document.querySelector('[data-dismiss="modal"]').addEventListener('click', function() {
            window.location.href = '{{ route('configuracion.datos_hotel') }}';
        });
        // Mostrar mensajes de éxito o error
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        @endif
    </script>
@endsection
