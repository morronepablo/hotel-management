@extends('adminlte::page')

@section('content_header')
    <h1><b>Detalle del Arqueo</b> - Usuario: {{ $arqueo->usuario->name }}</h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="callout callout-info">
                <div class="card-header">
                    <h3 class="card-title text-info text-bold">Datos Registrados</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha_apertura">Fecha Apertura</label>
                                <input type="datetime-local" class="form-control border-info bg-white"
                                    value="{{ \Carbon\Carbon::parse($arqueo->fecha_apertura)->format('Y-m-d\TH:i') }}"
                                    name="fecha_apertura" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="monto_inicial">Monto Inicial</label>
                                <input type="text" class="form-control border-info bg-white text-right"
                                    value="$ {{ number_format($arqueo->monto_inicial, 2, ',', '.') }}" name="monto_inicial"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha_cierre">Fecha Cierre</label>
                                <input type="datetime-local" class="form-control border-info bg-white"
                                    value="{{ $arqueo->fecha_cierre ? \Carbon\Carbon::parse($arqueo->fecha_cierre)->format('Y-m-d\TH:i') : '' }}"
                                    name="fecha_cierre" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="monto_final">Monto Final</label>
                                <input type="text" class="form-control border-info bg-white text-right"
                                    value="{{ $arqueo->monto_final ? '$ ' . number_format($arqueo->monto_final, 2, ',', '.') : '' }}"
                                    name="monto_final" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <input type="text" class="form-control border-info bg-white"
                                    value="{{ $arqueo->descripcion }}" name="descripcion" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="{{ route('caja.arqueos.index') }}"
                                class="btn btn-secondary text-white text-decoration-none"><i class="fas fa-reply"></i>
                                Volver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="callout callout-success">
                <div class="card-header">
                    <h3 class="card-title text-success text-bold">Ingresos</h3>
                </div>
                <div class="card-body">
                    <div
                        class="table-responsive-custom {{ count($movimientos->where('tipo', 'Ingreso')) > 10 ? 'scrollable' : '' }}">
                        <table class="table table-bordered table-sm table-striped table-hover">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th scope="col">Nro</th>
                                    <th scope="col">Detalle</th>
                                    <th scope="col">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $contador = 1;
                                $totalIngreso = 0;
                                ?>
                                @foreach ($movimientos as $movimiento)
                                    @if ($movimiento->tipo === 'Ingreso')
                                        <?php $totalIngreso += $movimiento->monto; ?>
                                        <tr>
                                            <td class="text-center">{{ $contador++ }}</td>
                                            <td>{{ $movimiento->descripcion }}</td>
                                            <td class="text-right">
                                                {{ '$ ' . number_format($movimiento->monto, 2, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right text-success"><b>Total Ingresos</b></td>
                                    <td class="text-right text-success">
                                        <b>{{ '$ ' . number_format($totalIngreso, 2, ',', '.') }}</b>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="callout callout-danger">
                <div class="card-header">
                    <h3 class="card-title text-danger text-bold">Egresos</h3>
                </div>
                <div class="card-body">
                    <div
                        class="table-responsive-custom {{ count($movimientos->where('tipo', 'Egreso')) > 10 ? 'scrollable' : '' }}">
                        <table class="table table-bordered table-sm table-striped table-hover">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th scope="col">Nro</th>
                                    <th scope="col">Detalle</th>
                                    <th scope="col">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $contadorB = 1;
                                $totalEgreso = 0;
                                ?>
                                @foreach ($movimientos as $movimiento)
                                    @if ($movimiento->tipo === 'Egreso')
                                        <?php $totalEgreso += $movimiento->monto; ?>
                                        <tr>
                                            <td class="text-center">{{ $contadorB++ }}</td>
                                            <td>{{ $movimiento->descripcion }}</td>
                                            <td class="text-right">
                                                {{ '$ ' . number_format($movimiento->monto, 2, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right text-danger"><b>Total Egresos</b></td>
                                    <td class="text-right text-danger">
                                        <b>{{ '$ ' . number_format($totalEgreso, 2, ',', '.') }}</b>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="callout callout-warning">
                <div class="card-header">
                    <h3 class="card-title text-warning text-bold">Diferencia</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <label for="diferencia" class="mb-0">Dif. (Ingresos - Egresos)</label>
                        </div>
                        <div class="col">
                            <?php $diferencia = $arqueo->monto_inicial + $totalIngreso - $totalEgreso - ($arqueo->monto_final ?? 0); ?>
                            <input type="text" class="form-control border-info bg-white text-right"
                                value="$ {{ number_format($diferencia, 2, ',', '.') }}" name="diferencia" disabled>
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

        .table-responsive-custom {
            position: relative;
        }

        .table-responsive-custom.scrollable {
            max-height: 500px;
            overflow-y: auto;
        }

        .table-responsive-custom table {
            width: 100%;
            margin-bottom: 0;
        }

        .table-responsive-custom thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40;
            color: #ffffff;
        }

        .table-responsive-custom tfoot {
            background-color: #f8f9fa;
        }

        .table-responsive-custom tbody tr {
            white-space: nowrap;
        }
    </style>
@stop

@section('js')
@stop
