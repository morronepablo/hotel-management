@extends('adminlte::page')

@section('content_header')
    <h1><b>Registro de nuevo producto</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('almacen.producto.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigo">Código</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
                                        value="{{ old('codigo') }}" name="codigo" required>
                                    @error('codigo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="producto">Producto</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('producto') ? 'is-invalid' : '' }}"
                                        value="{{ old('producto') }}" name="producto" required>
                                    @error('producto')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="categoria_id">Categoría</label>
                                    <select class="form-control {{ $errors->has('categoria_id') ? 'is-invalid' : '' }}"
                                        name="categoria_id" required>
                                        <option value="">Seleccione una categoría</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->denominacion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="imagen">Imagen</label>
                                    <input type="file"
                                        class="form-control {{ $errors->has('imagen') ? 'is-invalid' : '' }}"
                                        name="imagen" id="imagen">
                                    @error('imagen')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <br>
                                    <center>
                                        <output id="list"></output>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock">Stock</label>
                                    <input type="number"
                                        class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                                        value="{{ old('stock', 0) }}" name="stock" required>
                                    @error('stock')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="precio">Precio</label>
                                    <input type="number" step="0.01"
                                        class="form-control {{ $errors->has('precio') ? 'is-invalid' : '' }}"
                                        value="{{ old('precio') }}" name="precio" required>
                                    @error('precio')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('almacen.producto') }}"
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

@section('adminlte_css')
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
    @stack('css')
    @yield('css')
@stop

@section('adminlte_js')
    <script>
        function archivo(evt) {
            var files = evt.target.files; // FileList object
            for (var i = 0, f; f = files[i]; i++) {
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();
                reader.onload = (function(theFile) {
                    return function(e) {
                        document.getElementById("list").innerHTML = ['<img class="thumb thumbnail" src="', e
                            .target.result,
                            '" width="50px" height="50px" style="object-fit: cover;" title="', escape(
                                theFile.name), '"/>'
                        ].join('');
                    };
                })(f);
                reader.readAsDataURL(f);
            }
        }
        document.getElementById('imagen').addEventListener('change', archivo, false);
    </script>
    @stack('js')
    @yield('js')
@stop
