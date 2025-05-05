@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de Arqueos</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title my-1">Arqueos registrados</h3>
                    <div class="card-tools">
                        <a href="{{ route('caja.arqueos.reporte') }}" target="_blank" class="btn btn-secondary btn-sm">
                            <i class="fa fa-file-pdf"></i> Reporte
                        </a>
                        @if ($arqueoAbierto)
                        @else
                            @can('crear-caja')
                                <a href="{{ route('caja.arqueos.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Crear nuevo
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <table id="arqueos-table" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th></th> <!-- Columna para la flecha -->
                                <th scope="col">Nro.</th>
                                <th scope="col">Fecha Apertura</th>
                                <th scope="col">Monto Inicial</th>
                                <th scope="col">Fecha Cierre</th>
                                <th scope="col">Monto Final</th>
                                <th scope="col">Efectivo</th>
                                <th scope="col">Tarjetas</th>
                                <th scope="col">Mercado Pago</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Movimientos</th>
                                <th scope="col">Usuario</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($arqueos as $arqueo)
                                <tr data-movimientos='@json($arqueo->movimientos)'>
                                    <td></td> <!-- Celda vacía para la flecha -->
                                    <th scope="row" class="text-center align-middle">{{ $loop->iteration }}</th>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($arqueo->fecha_apertura)->format('d/m/Y H:i') }}</td>
                                    <td class="text-right align-middle">$
                                        {{ number_format($arqueo->monto_inicial, 2, ',', '.') }}</td>
                                    <td class="text-center align-middle">
                                        {{ $arqueo->fecha_cierre ? \Carbon\Carbon::parse($arqueo->fecha_cierre)->format('d/m/Y H:i') : '' }}
                                    </td>
                                    <td class="text-right align-middle">
                                        {{ $arqueo->monto_final ? '$ ' . number_format($arqueo->monto_final, 2, ',', '.') : '' }}
                                    </td>
                                    <td class="text-right align-middle">
                                        {{ $arqueo->ventas_efectivo ? '$ ' . number_format($arqueo->ventas_efectivo, 2, ',', '.') : '' }}
                                    </td>
                                    <td class="text-right align-middle">
                                        {{ $arqueo->ventas_tarjeta ? '$ ' . number_format($arqueo->ventas_tarjeta, 2, ',', '.') : '' }}
                                    </td>
                                    <td class="text-right align-middle">
                                        {{ $arqueo->ventas_mercadopago ? '$ ' . number_format($arqueo->ventas_mercadopago, 2, ',', '.') : '' }}
                                    </td>
                                    <td class="align-middle">{{ $arqueo->descripcion }}</td>
                                    <td class="align-middle" style="min-width: 300px;">
                                        <div class="row">
                                            <div class="col-md-4 text-success">
                                                <b>Ingresos</b> <br>
                                                $ {{ number_format($arqueo->total_ingresos, 2, ',', '.') }}
                                            </div>
                                            <div class="col-md-4 text-danger">
                                                <b>Egresos</b> <br>
                                                $ {{ number_format($arqueo->total_egresos, 2, ',', '.') }}
                                            </div>
                                            <?php $diferencia = $arqueo->monto_inicial + $arqueo->total_ingresos - $arqueo->total_egresos - ($arqueo->monto_final ?? 0); ?>
                                            <div class="col-md-4 text-warning">
                                                <b>Dif.</b> <br>
                                                $ {{ number_format($diferencia, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $arqueo->usuario->name }}</td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="{{ route('caja.arqueos.show', $arqueo->id) }}"
                                                class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Ver Arqueo">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('editar-caja')
                                                @if ($arqueo->fecha_cierre === null)
                                                    <a href="{{ route('caja.arqueos.edit', $arqueo->id) }}"
                                                        class="btn btn-success btn-sm" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Editar Arqueo">
                                                        <i class="fas fa-pencil"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-success btn-sm" disabled>
                                                        <i class="fas fa-pencil" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Editar Arqueo"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                            @can('crear-caja')
                                                @if ($arqueo->fecha_cierre === null)
                                                    <a href="{{ route('caja.arqueos.ingresoegreso', $arqueo->id) }}"
                                                        class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Ingresar Ingreso/Egreso">
                                                        <i class="fas fa-cash-register"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-warning btn-sm" disabled>
                                                        <i class="fas fa-cash-register" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Ingresar Ingreso/Egreso"></i>
                                                    </button>
                                                @endif
                                                @if ($arqueo->fecha_cierre === null)
                                                    <a href="{{ route('caja.arqueos.cierre', $arqueo->id) }}"
                                                        class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Cierre Arqueo">
                                                        <i class="fas fa-lock"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-lock" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Cierre Arqueo"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                            @can('eliminar-caja')
                                                <form action="{{ route('caja.arqueos.destroy', $arqueo->id) }}" method="POST"
                                                    onclick="preguntar{{ $arqueo->id }}(event)"
                                                    id="miFormulario{{ $arqueo->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Eliminar Arqueo"
                                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    function preguntar{{ $arqueo->id }}(event) {
                                                        event.preventDefault();
                                                        Swal.fire({
                                                            title: "Estas seguro de querer eliminar arqueo?",
                                                            text: "¡Si eliminas este arqueo, se borrará todos los movimientos generados!",
                                                            icon: "warning",
                                                            showCancelButton: true,
                                                            confirmButtonColor: "#3085d6",
                                                            cancelButtonColor: "#d33",
                                                            confirmButtonText: "¡Sí, bórralo!"
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                var form = $('#miFormulario{{ $arqueo->id }}');
                                                                form.submit();
                                                            }
                                                        });
                                                    }
                                                </script>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    <style>
        td.details-control {
            cursor: pointer;
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            width: 20px;
        }

        tr.shown td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }

        .child-table {
            margin: 10px 0;
        }

        .btn.rounded-sm {
            border-radius: 5px !important;
        }

        .btn.rounded-sm {
            margin-right: 10px;
        }

        .btn.rounded-sm:last-child {
            margin-right: 0;
        }

        #arqueos-table_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .btn-secondary:hover {
            filter: brightness(90%);
        }

        .btn-danger:hover {
            filter: brightness(90%);
        }

        .btn-info:hover {
            filter: brightness(90%);
        }

        .btn-success:hover {
            filter: brightness(90%);
        }

        .btn-warning:hover {
            filter: brightness(90%);
        }

        #arqueos-table {
            font-size: 0.85rem;
        }

        #arqueos-table thead th {
            font-size: 0.9rem;
        }

        #arqueos-table tbody td {
            font-size: 0.8rem;
        }

        #arqueos-table tbody td,
        #arqueos-table thead th {
            padding: 0.4rem 0.6rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script>
        function formatDetails(movimientos) {
            let html = '<table class="table table-sm table-bordered" style="width:100%; font-size: 0.75rem;">';
            html +=
                '<thead><tr><th class="text-center">Tipo</th><th class="text-center">Monto</th><th>Descripción</th></tr></thead><tbody>';

            movimientos.forEach(function(movimiento) {
                let tipo = movimiento.tipo || '(Sin tipo)';
                let monto = movimiento.monto ? '$ ' + parseFloat(movimiento.monto).toLocaleString('es-AR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) : '(Sin monto)';
                let descripcion = movimiento.descripcion || '(Sin descripción)';

                html += '<tr>';
                html += '<td class="text-center ' + (tipo === 'Ingreso' ? 'text-success' : 'text-danger') + '">' +
                    tipo + '</td>';
                html += '<td class="text-right">' + monto + '</td>';
                html += '<td>' + descripcion + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            return html;
        }

        $(document).ready(function() {
            var table = $('#arqueos-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 8,
                "lengthMenu": [
                    [5, 8, 10, 25, 50, 100, -1],
                    [5, 8, 10, 25, 50, 100, "Todos"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "dom": '<"row"<"col-md-6"l><"col-md-6 text-right"f>><"row"<"col-md-12"B>>rt<"row"<"col-md-6"i><"col-md-6 text-right"p>>',
                buttons: [{
                        text: '<i class="fas fa-copy"></i> Copiar',
                        extend: 'copy',
                        className: 'btn btn-secondary rounded-sm'
                    },
                    {
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        extend: 'pdf',
                        className: 'btn btn-danger rounded-sm'
                    },
                    {
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        extend: 'csv',
                        className: 'btn btn-info rounded-sm'
                    },
                    {
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        extend: 'excel',
                        className: 'btn btn-success rounded-sm'
                    },
                    {
                        text: '<i class="fas fa-print"></i> Imprimir',
                        extend: 'print',
                        className: 'btn btn-warning rounded-sm'
                    }
                ],
                "columnDefs": [{
                    "targets": 0,
                    "orderable": false,
                    "className": 'details-control',
                    "defaultContent": ''
                }]
            });

            table.buttons().container().appendTo('#arqueos-table_wrapper .col-md-12:eq(1)');

            $('#arqueos-table tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    var movimientos = JSON.parse(tr.attr('data-movimientos') || '[]');
                    if (movimientos.length > 0) {
                        row.child(formatDetails(movimientos)).show();
                        tr.addClass('shown');
                    } else {
                        row.child('<p>No hay movimientos para mostrar.</p>').show();
                        tr.addClass('shown');
                    }
                }
            });

            @if (session('mensaje'))
                Swal.fire({
                    position: "center",
                    icon: "{{ session('icono') }}",
                    title: "{{ session('mensaje') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        });

        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop
