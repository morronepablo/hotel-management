{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Cierre de un arqueo</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.arqueos.store_cierre') }}" method="POST">
                        @csrf
                        <input type="text" value="{{ $arqueo->id }}" name="arqueo_id" hidden>
                        <input type="hidden" name="monto_final" id="monto_final_raw">
                        <input type="hidden" name="ventas_efectivo" id="ventas_efectivo_raw">
                        <input type="hidden" name="ventas_tarjeta" id="ventas_tarjeta_raw">
                        <input type="hidden" name="ventas_mercadopago" id="ventas_mercadopago_raw">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_apertura">Fecha Apertura</label>
                                    <input type="datetime-local" class="form-control border-info bg-white"
                                        value="{{ $arqueo->fecha_apertura }}" name="fecha_apertura" id="fecha_apertura"
                                        disabled>
                                    @error('fecha_apertura')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_inicial">Monto inicial</label>
                                    <input type="text" class="form-control border-info bg-white text-right"
                                        value="{{ old('monto_inicial', '$ ' . number_format($arqueo->monto_inicial, 2, ',', '.')) }}"
                                        name="monto_inicial" disabled>
                                    @error('monto_inicial')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_cierre">Fecha Cierre</label> <b class="text-danger">*</b>
                                    <input type="datetime-local"
                                        class="form-control {{ $errors->has('fecha_cierre') ? 'is-invalid' : '' }}"
                                        value="{{ old('fecha_cierre') }}" name="fecha_cierre" id="fecha_cierre" required>
                                    @error('fecha_cierre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_final">Monto Final</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('monto_final') ? 'is-invalid' : '' }} text-right"
                                        id="monto_final" readonly>
                                    @error('monto_final')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('caja.arqueos.index') }}"
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

        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Contador de Dinero</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-success">Billetes</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20000.jpg') }}"
                                                alt="20000" width="80"></td>
                                        <td id="bill_20000_value">$20,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10000.jpg') }}"
                                                alt="10000" width="80"></td>
                                        <td id="bill_10000_value">$10,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_2000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_2000.jpg') }}"
                                                alt="2000" width="80"></td>
                                        <td id="bill_2000_value">$2,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_1000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_1000.jpg') }}"
                                                alt="1000" width="80"></td>
                                        <td id="bill_1000_value">$1,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_500"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_500.jpg') }}"
                                                alt="500" width="80"></td>
                                        <td id="bill_500_value">$500.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_200"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_200.jpg') }}"
                                                alt="200" width="80"></td>
                                        <td id="bill_200_value">$200.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_100"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_100.jpg') }}"
                                                alt="100" width="80"></td>
                                        <td id="bill_100_value">$100.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_50"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_50.jpg') }}"
                                                alt="50" width="80"></td>
                                        <td id="bill_50_value">$50.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20.jpg') }}"
                                                alt="20" width="80"></td>
                                        <td id="bill_20_value">$20.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10.jpg') }}"
                                                alt="10" width="80"></td>
                                        <td id="bill_10_value">$10.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_5"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_5.jpg') }}"
                                                alt="5" width="80"></td>
                                        <td id="bill_5_value">$5.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-success">Monedas</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_2"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_2.jpg') }}"
                                                alt="2" width="30"></td>
                                        <td id="coin_2_value">$2.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_1"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_1.jpg') }}"
                                                alt="1" width="30"></td>
                                        <td id="coin_1_value">$1.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_050"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_050.jpg') }}"
                                                alt="0.50" width="30"></td>
                                        <td id="coin_050_value">$0.50</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_025"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_025.jpg') }}"
                                                alt="0.25" width="30"></td>
                                        <td id="coin_025_value">$0.25</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_cards_input">Total Tarjetas</label></td>
                                        <td><input type="number" class="form-control text-right" id="total_cards_input"
                                                min="0" step="0.01" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_mercadopago_input">Total Mercado Pago</label>
                                        </td>
                                        <td><input type="number" class="form-control text-right"
                                                id="total_mercadopago_input" min="0" step="0.01"
                                                value="0"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Efectivo</label>
                                <input type="text" class="form-control text-right" id="total_cash" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Tarjetas</label>
                                <input type="text" class="form-control text-right" id="total_cards" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Mercado Pago</label>
                                <input type="text" class="form-control text-right" id="total_mercadopago" readonly>
                            </div>
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

        .count-input {
            width: 100px;
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let isProcessing = false;

            function formatCurrency(amount) {
                return '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function calculateTotal() {
                var bill_20000 = parseInt($('#bill_20000').val()) || 0;
                var bill_10000 = parseInt($('#bill_10000').val()) || 0;
                var bill_2000 = parseInt($('#bill_2000').val()) || 0;
                var bill_1000 = parseInt($('#bill_1000').val()) || 0;
                var bill_500 = parseInt($('#bill_500').val()) || 0;
                var bill_200 = parseInt($('#bill_200').val()) || 0;
                var bill_100 = parseInt($('#bill_100').val()) || 0;
                var bill_50 = parseInt($('#bill_50').val()) || 0;
                var bill_20 = parseInt($('#bill_20').val()) || 0;
                var bill_10 = parseInt($('#bill_10').val()) || 0;
                var bill_5 = parseInt($('#bill_5').val()) || 0;
                var coin_2 = parseInt($('#coin_2').val()) || 0;
                var coin_1 = parseInt($('#coin_1').val()) || 0;
                var coin_050 = parseInt($('#coin_050').val()) || 0;
                var coin_025 = parseInt($('#coin_025').val()) || 0;
                var total_cards = parseFloat($('#total_cards_input').val()) || 0;
                var total_mercadopago = parseFloat($('#total_mercadopago_input').val()) || 0;

                var totalCash = (bill_20000 * 20000) + (bill_10000 * 10000) + (bill_2000 * 2000) +
                    (bill_1000 * 1000) + (bill_500 * 500) + (bill_200 * 200) + (bill_100 * 100) +
                    (bill_50 * 50) + (bill_20 * 20) + (bill_10 * 10) + (bill_5 * 5) +
                    (coin_2 * 2) + (coin_1 * 1) + (coin_050 * 0.50) + (coin_025 * 0.25);

                var totalAmount = totalCash + total_cards + total_mercadopago;

                $('#total_cash').val(formatCurrency(totalCash));
                $('#total_cards').val(formatCurrency(total_cards));
                $('#total_mercadopago').val(formatCurrency(total_mercadopago));
                $('#monto_final').val(formatCurrency(totalAmount));

                $('#monto_final_raw').val(totalAmount.toFixed(2));
                $('#ventas_efectivo_raw').val(totalCash.toFixed(2));
                $('#ventas_tarjeta_raw').val(total_cards.toFixed(2));
                $('#ventas_mercadopago_raw').val(total_mercadopago.toFixed(2));
            }

            var totalTarjetas = {{ $totalTarjetas ?? 0 }};
            var totalMercadoPago = {{ $totalMercadoPago ?? 0 }};
            $('#total_cards_input').val(totalTarjetas.toFixed(2));
            $('#total_mercadopago_input').val(totalMercadoPago.toFixed(2));
            calculateTotal();

            $('.count-input, #total_cards_input, #total_mercadopago_input').on('input', function() {
                calculateTotal();
            });
        });
    </script>
@stop --}}




































































































{{-- @extends('adminlte::page')

@section('content_header')
    <h1><b>Cierre de un arqueo</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.arqueos.store_cierre') }}" method="POST">
                        @csrf
                        <input type="text" value="{{ $arqueo->id }}" name="arqueo_id" hidden>
                        <input type="hidden" name="monto_final" id="monto_final_raw">
                        <input type="hidden" name="ventas_efectivo" id="ventas_efectivo_raw">
                        <input type="hidden" name="ventas_tarjeta" id="ventas_tarjeta_raw">
                        <input type="hidden" name="ventas_mercadopago" id="ventas_mercadopago_raw">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_apertura">Fecha Apertura</label>
                                    <input type="datetime-local" class="form-control border-info bg-white"
                                        value="{{ $arqueo->fecha_apertura }}" name="fecha_apertura" id="fecha_apertura"
                                        disabled>
                                    @error('fecha_apertura')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_inicial">Monto inicial</label>
                                    <input type="text" class="form-control border-info bg-white text-right"
                                        value="{{ old('monto_inicial', '$ ' . number_format($arqueo->monto_inicial, 2, ',', '.')) }}"
                                        name="monto_inicial" disabled>
                                    @error('monto_inicial')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_cierre">Fecha Cierre</label> <b class="text-danger">*</b>
                                    <input type="datetime-local"
                                        class="form-control {{ $errors->has('fecha_cierre') ? 'is-invalid' : '' }}"
                                        value="{{ old('fecha_cierre') }}" name="fecha_cierre" id="fecha_cierre" required>
                                    @error('fecha_cierre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_final">Monto Final</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('monto_final') ? 'is-invalid' : '' }} text-right"
                                        id="monto_final" readonly>
                                    @error('monto_final')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('caja.arqueos.index') }}"
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

        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Contador de Dinero</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-success">Billetes</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20000.jpg') }}"
                                                alt="20000" width="80"></td>
                                        <td id="bill_20000_value">$20,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10000.jpg') }}"
                                                alt="10000" width="80"></td>
                                        <td id="bill_10000_value">$10,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_2000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_2000.jpg') }}"
                                                alt="2000" width="80"></td>
                                        <td id="bill_2000_value">$2,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_1000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_1000.jpg') }}"
                                                alt="1000" width="80"></td>
                                        <td id="bill_1000_value">$1,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_500"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_500.jpg') }}"
                                                alt="500" width="80"></td>
                                        <td id="bill_500_value">$500.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_200"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_200.jpg') }}"
                                                alt="200" width="80"></td>
                                        <td id="bill_200_value">$200.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_100"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_100.jpg') }}"
                                                alt="100" width="80"></td>
                                        <td id="bill_100_value">$100.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_50"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_50.jpg') }}"
                                                alt="50" width="80"></td>
                                        <td id="bill_50_value">$50.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20.jpg') }}"
                                                alt="20" width="80"></td>
                                        <td id="bill_20_value">$20.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10.jpg') }}"
                                                alt="10" width="80"></td>
                                        <td id="bill_10_value">$10.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_5"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_5.jpg') }}"
                                                alt="5" width="80"></td>
                                        <td id="bill_5_value">$5.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-success">Monedas</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_2"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_2.jpg') }}"
                                                alt="2" width="30"></td>
                                        <td id="coin_2_value">$2.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_1"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_1.jpg') }}"
                                                alt="1" width="30"></td>
                                        <td id="coin_1_value">$1.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_050"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_050.jpg') }}"
                                                alt="0.50" width="30"></td>
                                        <td id="coin_050_value">$0.50</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_025"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_025.jpg') }}"
                                                alt="0.25" width="30"></td>
                                        <td id="coin_025_value">$0.25</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_cards_input">Total Tarjetas</label></td>
                                        <td><input type="number" class="form-control text-right" id="total_cards_input"
                                                min="0" step="0.01" value="{{ $totalTarjetas }}"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_mercadopago_input">Total Mercado Pago</label>
                                        </td>
                                        <td><input type="number" class="form-control text-right"
                                                id="total_mercadopago_input" min="0" step="0.01"
                                                value="{{ $totalMercadoPago }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Efectivo</label>
                                <input type="text" class="form-control text-right" id="total_cash" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Tarjetas</label>
                                <input type="text" class="form-control text-right" id="total_cards" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Mercado Pago</label>
                                <input type="text" class="form-control text-right" id="total_mercadopago" readonly>
                            </div>
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

        .count-input {
            width: 100px;
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let isProcessing = false;

            function formatCurrency(amount) {
                return '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function calculateTotal() {
                // Calcular el total de efectivo basado en billetes y monedas ingresados manualmente
                var bill_20000 = parseInt($('#bill_20000').val()) || 0;
                var bill_10000 = parseInt($('#bill_10000').val()) || 0;
                var bill_2000 = parseInt($('#bill_2000').val()) || 0;
                var bill_1000 = parseInt($('#bill_1000').val()) || 0;
                var bill_500 = parseInt($('#bill_500').val()) || 0;
                var bill_200 = parseInt($('#bill_200').val()) || 0;
                var bill_100 = parseInt($('#bill_100').val()) || 0;
                var bill_50 = parseInt($('#bill_50').val()) || 0;
                var bill_20 = parseInt($('#bill_20').val()) || 0;
                var bill_10 = parseInt($('#bill_10').val()) || 0;
                var bill_5 = parseInt($('#bill_5').val()) || 0;
                var coin_2 = parseInt($('#coin_2').val()) || 0;
                var coin_1 = parseInt($('#coin_1').val()) || 0;
                var coin_050 = parseInt($('#coin_050').val()) || 0;
                var coin_025 = parseInt($('#coin_025').val()) || 0;

                var totalCashManual = (bill_20000 * 20000) + (bill_10000 * 10000) + (bill_2000 * 2000) +
                    (bill_1000 * 1000) + (bill_500 * 500) + (bill_200 * 200) + (bill_100 * 100) +
                    (bill_50 * 50) + (bill_20 * 20) + (bill_10 * 10) + (bill_5 * 5) +
                    (coin_2 * 2) + (coin_1 * 1) + (coin_050 * 0.50) + (coin_025 * 0.25);

                // Usar el total de efectivo manual si el usuario ha ingresado valores, de lo contrario usar el calculado
                var totalCash = totalCashManual > 0 ? totalCashManual : parseFloat('{{ $totalEfectivo }}') || 0;
                var totalCards = parseFloat($('#total_cards_input').val()) || 0;
                var totalMercadoPago = parseFloat($('#total_mercadopago_input').val()) || 0;

                var totalAmount = totalCash + totalCards + totalMercadoPago;

                $('#total_cash').val(formatCurrency(totalCash));
                $('#total_cards').val(formatCurrency(totalCards));
                $('#total_mercadopago').val(formatCurrency(totalMercadoPago));
                $('#monto_final').val(formatCurrency(totalAmount));

                $('#monto_final_raw').val(totalAmount.toFixed(2));
                $('#ventas_efectivo_raw').val(totalCash.toFixed(2));
                $('#ventas_tarjeta_raw').val(totalCards.toFixed(2));
                $('#ventas_mercadopago_raw').val(totalMercadoPago.toFixed(2));
            }

            // Inicializar los campos con los valores calculados desde el backend
            var totalEfectivo = parseFloat('{{ $totalEfectivo }}') || 0;
            var totalTarjetas = parseFloat('{{ $totalTarjetas }}') || 0;
            var totalMercadoPago = parseFloat('{{ $totalMercadoPago }}') || 0;

            $('#total_cards_input').val(totalTarjetas.toFixed(2));
            $('#total_mercadopago_input').val(totalMercadoPago.toFixed(2));

            // Calcular el total inicial
            calculateTotal();

            // Actualizar los totales cuando el usuario modifique los valores
            $('.count-input, #total_cards_input, #total_mercadopago_input').on('input', function() {
                calculateTotal();
            });

            // Actualizar los totales cuando cambie la fecha de cierre
            $('#fecha_cierre').on('change', function() {
                var fechaCierre = $(this).val();
                if (fechaCierre) {
                    $.ajax({
                        url: '{{ route('caja.arqueos.cierre', $arqueo->id) }}',
                        method: 'GET',
                        data: {
                            fecha_cierre: fechaCierre
                        },
                        success: function(response) {
                            $('#total_cards_input').val(parseFloat(response.totalTarjetas)
                                .toFixed(2));
                            $('#total_mercadopago_input').val(parseFloat(response
                                .totalMercadoPago).toFixed(2));
                            totalEfectivo = parseFloat(response.totalEfectivo) || 0;
                            calculateTotal();
                        },
                        error: function(xhr) {
                            console.error('Error al actualizar los totales:', xhr);
                        }
                    });
                }
            });
        });
    </script>
@stop --}}













































































































































@extends('adminlte::page')

@section('content_header')
    <h1><b>Cierre de un arqueo</b></h1>
    <hr>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ingrese los datos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('caja.arqueos.store_cierre') }}" method="POST">
                        @csrf
                        <input type="text" value="{{ $arqueo->id }}" name="arqueo_id" hidden>
                        <input type="hidden" name="monto_final" id="monto_final_raw">
                        <input type="hidden" name="ventas_efectivo" id="ventas_efectivo_raw">
                        <input type="hidden" name="ventas_tarjeta" id="ventas_tarjeta_raw">
                        <input type="hidden" name="ventas_mercadopago" id="ventas_mercadopago_raw">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_apertura">Fecha Apertura</label>
                                    <input type="datetime-local" class="form-control border-info bg-white"
                                        value="{{ $arqueo->fecha_apertura }}" name="fecha_apertura" id="fecha_apertura"
                                        disabled>
                                    @error('fecha_apertura')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_inicial">Monto inicial</label>
                                    <input type=" rtext" class="form-control border-info bg-white text-right"
                                        value="{{ old('monto_inicial', '$ ' . number_format($arqueo->monto_inicial, 2, ',', '.')) }}"
                                        name="monto_inicial" disabled>
                                    @error('monto_inicial')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fecha_cierre">Fecha Cierre</label> <b class="text-danger">*</b>
                                    <input type="datetime-local"
                                        class="form-control {{ $errors->has('fecha_cierre') ? 'is-invalid' : '' }}"
                                        value="{{ old('fecha_cierre') }}" name="fecha_cierre" id="fecha_cierre" required>
                                    @error('fecha_cierre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto_final">Monto Final</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('monto_final') ? 'is-invalid' : '' }} text-right"
                                        id="monto_final" readonly>
                                    @error('monto_final')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('caja.arqueos.index') }}"
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

        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Contador de Dinero</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-success">Billetes</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20000.jpg') }}"
                                                alt="20000" width="80"></td>
                                        <td id="bill_20000_value">$20,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10000.jpg') }}"
                                                alt="10000" width="80"></td>
                                        <td id="bill_10000_value">$10,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_2000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_2000.jpg') }}"
                                                alt="2000" width="80"></td>
                                        <td id="bill_2000_value">$2,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_1000"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_1000.jpg') }}"
                                                alt="1000" width="80"></td>
                                        <td id="bill_1000_value">$1,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_500"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_500.jpg') }}"
                                                alt="500" width="80"></td>
                                        <td id="bill_500_value">$500.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_200"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_200.jpg') }}"
                                                alt="200" width="80"></td>
                                        <td id="bill_200_value">$200.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_100"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_100.jpg') }}"
                                                alt="100" width="80"></td>
                                        <td id="bill_100_value">$100.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_50"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_50.jpg') }}"
                                                alt="50" width="80"></td>
                                        <td id="bill_50_value">$50.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_20"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_20.jpg') }}"
                                                alt="20" width="80"></td>
                                        <td id="bill_20_value">$20.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_10"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_10.jpg') }}"
                                                alt="10" width="80"></td>
                                        <td id="bill_10_value">$10.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="bill_5"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/bill_5.jpg') }}"
                                                alt="5" width="80"></td>
                                        <td id="bill_5_value">$5.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-success">Monedas</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Cantidad</th>
                                        <th>Denominación</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_2"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_2.jpg') }}"
                                                alt="2" width="30"></td>
                                        <td id="coin_2_value">$2.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_1"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_1.jpg') }}"
                                                alt="1" width="30"></td>
                                        <td id="coin_1_value">$1.00</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_050"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_050.jpg') }}"
                                                alt="0.50" width="30"></td>
                                        <td id="coin_050_value">$0.50</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" class="form-control count-input" id="coin_025"
                                                min="0" value="0"></td>
                                        <td class="text-center"><img src="{{ asset('images/coin_025.jpg') }}"
                                                alt="0.25" width="30"></td>
                                        <td id="coin_025_value">$0.25</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_cards_input">Total Tarjetas</label></td>
                                        <td><input type="number" class="form-control text-right" id="total_cards_input"
                                                min="0" step="0.01" value="{{ $totalTarjetas }}"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="total_mercadopago_input">Total Mercado Pago</label>
                                        </td>
                                        <td><input type="number" class="form-control text-right"
                                                id="total_mercadopago_input" min="0" step="0.01"
                                                value="{{ $totalMercadoPago }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Efectivo</label>
                                <input type="text" class="form-control text-right" id="total_cash" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Tarjetas</label>
                                <input type="text" class="form-control text-right" id="total_cards" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Mercado Pago</label>
                                <input type="text" class="form-control text-right" id="total_mercadopago" readonly>
                            </div>
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

        .count-input {
            width: 100px;
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let isProcessing = false;

            function formatCurrency(amount) {
                return '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function calculateTotal() {
                // Calcular el total de efectivo basado únicamente en billetes y monedas ingresados manualmente
                var bill_20000 = parseInt($('#bill_20000').val()) || 0;
                var bill_10000 = parseInt($('#bill_10000').val()) || 0;
                var bill_2000 = parseInt($('#bill_2000').val()) || 0;
                var bill_1000 = parseInt($('#bill_1000').val()) || 0;
                var bill_500 = parseInt($('#bill_500').val()) || 0;
                var bill_200 = parseInt($('#bill_200').val()) || 0;
                var bill_100 = parseInt($('#bill_100').val()) || 0;
                var bill_50 = parseInt($('#bill_50').val()) || 0;
                var bill_20 = parseInt($('#bill_20').val()) || 0;
                var bill_10 = parseInt($('#bill_10').val()) || 0;
                var bill_5 = parseInt($('#bill_5').val()) || 0;
                var coin_2 = parseInt($('#coin_2').val()) || 0;
                var coin_1 = parseInt($('#coin_1').val()) || 0;
                var coin_050 = parseInt($('#coin_050').val()) || 0;
                var coin_025 = parseInt($('#coin_025').val()) || 0;

                var totalCash = (bill_20000 * 20000) + (bill_10000 * 10000) + (bill_2000 * 2000) +
                    (bill_1000 * 1000) + (bill_500 * 500) + (bill_200 * 200) + (bill_100 * 100) +
                    (bill_50 * 50) + (bill_20 * 20) + (bill_10 * 10) + (bill_5 * 5) +
                    (coin_2 * 2) + (coin_1 * 1) + (coin_050 * 0.50) + (coin_025 * 0.25);

                var totalCards = parseFloat($('#total_cards_input').val()) || 0;
                var totalMercadoPago = parseFloat($('#total_mercadopago_input').val()) || 0;

                var totalAmount = totalCash + totalCards + totalMercadoPago;

                $('#total_cash').val(formatCurrency(totalCash));
                $('#total_cards').val(formatCurrency(totalCards));
                $('#total_mercadopago').val(formatCurrency(totalMercadoPago));
                $('#monto_final').val(formatCurrency(totalAmount));

                $('#monto_final_raw').val(totalAmount.toFixed(2));
                $('#ventas_efectivo_raw').val(totalCash.toFixed(2));
                $('#ventas_tarjeta_raw').val(totalCards.toFixed(2));
                $('#ventas_mercadopago_raw').val(totalMercadoPago.toFixed(2));
            }

            // Inicializar los campos con los valores calculados desde el backend para tarjetas y Mercado Pago
            var totalTarjetas = parseFloat('{{ $totalTarjetas }}') || 0;
            var totalMercadoPago = parseFloat('{{ $totalMercadoPago }}') || 0;

            $('#total_cards_input').val(totalTarjetas.toFixed(2));
            $('#total_mercadopago_input').val(totalMercadoPago.toFixed(2));

            // Calcular el total inicial
            calculateTotal();

            // Actualizar los totales cuando el usuario modifique los valores
            $('.count-input, #total_cards_input, #total_mercadopago_input').on('input', function() {
                calculateTotal();
            });

            // Actualizar los totales de tarjetas y Mercado Pago cuando cambie la fecha de cierre
            $('#fecha_cierre').on('change', function() {
                var fechaCierre = $(this).val();
                if (fechaCierre) {
                    $.ajax({
                        url: '{{ route('caja.arqueos.cierre', $arqueo->id) }}',
                        method: 'GET',
                        data: {
                            fecha_cierre: fechaCierre
                        },
                        success: function(response) {
                            $('#total_cards_input').val(parseFloat(response.totalTarjetas)
                                .toFixed(2));
                            $('#total_mercadopago_input').val(parseFloat(response
                                .totalMercadoPago).toFixed(2));
                            calculateTotal();
                        },
                        error: function(xhr) {
                            console.error('Error al actualizar los totales:', xhr);
                        }
                    });
                }
            });
        });
    </script>
@stop
