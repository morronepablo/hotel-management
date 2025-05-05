@extends('adminlte::page')

@section('title', 'Dashboard')

@php
    use Carbon\Carbon;
@endphp

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <div class="row">
        <!-- Habitaciones que requieren atención -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $roomsRequiringAttention }}</h3>
                    <p>Hab. que requieren atención</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="{{ route('entradas.panel-control') }}" class="small-box-footer">Más info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Habitaciones Disponibles -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $availableRooms }}</h3>
                    <p>Habitaciones Disponibles</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('entradas.panel-control') }}" class="small-box-footer">Más info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Habitaciones Reservadas -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $confirmedReservations }}</h3>
                    <p>Habitaciones Reservadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('entradas.panel-control') }}" class="small-box-footer">Más info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Habitaciones Ocupadas -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $occupiedRooms }}</h3>
                    <p>Habitaciones Ocupadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bed"></i>
                </div>
                <a href="{{ route('entradas.panel-control') }}" class="small-box-footer">Más info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Selector de Mes -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="form-group">
                <label for="monthSelector">Seleccionar Mes para Productos Más Vendidos:</label>
                <select id="monthSelector" class="form-control" style="width: 200px; display: inline-block;">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == $mes_actual_numero ? 'selected' : '' }}>
                            {{ ucfirst(Carbon::create()->month($i)->locale('es')->monthName) }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mt-5">
        <!-- Gráfico de Alquileres, Renovaciones, Consumos y Servicios -->
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Gráfico Anual ({{ Carbon::now()->year }})</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Productos Más Vendidos -->
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Productos Más Vendidos ({{ $mes_actual }})</h3>
                </div>
                <div class="card-body">
                    <canvas id="topProductosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <?php
    $monthsArray = range(1, $monthsInYear);
    $monthNames = array_map(function ($month) {
        return ucfirst(Carbon::create()->month($month)->locale('es')->monthName);
    }, $monthsArray);
    $alquileresByMonthJson = json_encode(array_values($alquileresByMonth));
    $renovacionesByMonthJson = json_encode(array_values($renovacionesByMonth));
    $consumosByMonthJson = json_encode(array_values($consumosByMonth));
    $serviciosByMonthJson = json_encode(array_values($serviciosByMonth));
    $topProductosLabels = $topProductos->pluck('nombre')->toArray() ?: ['Sin datos'];
    $topProductosData = $topProductos->pluck('total_vendido')->toArray() ?: [0];
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var months = <?php echo json_encode($monthNames); ?>;
            var alquileresByMonth = <?php echo $alquileresByMonthJson; ?>;
            var renovacionesByMonth = <?php echo $renovacionesByMonthJson; ?>;
            var consumosByMonth = <?php echo $consumosByMonthJson; ?>;
            var serviciosByMonth = <?php echo $serviciosByMonthJson; ?>;
            var topProductosLabels = <?php echo json_encode($topProductosLabels); ?>;
            var topProductosData = <?php echo json_encode($topProductosData); ?>;

            // Función para generar colores HSL
            function generateColors(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    const hue = (i * 360) / count;
                    colors.push(`hsla(${hue}, 70%, 50%, 0.7)`);
                }
                return colors;
            }

            // Formatear números como moneda
            function formatCurrency(value) {
                if (value === 0 || value === null || isNaN(value)) return '$ 0,00';
                const num = parseFloat(value);
                return '$ ' + num.toLocaleString('es-AR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).replace(',', '.').replace('.', ',');
            }

            // Gráfico de Alquileres, Renovaciones, Consumos y Servicios (Barras Apiladas)
            const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                            label: 'Alquileres',
                            data: alquileresByMonth,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Renovaciones',
                            data: renovacionesByMonth,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Consumos',
                            data: consumosByMonth,
                            backgroundColor: 'rgba(255, 206, 86, 0.5)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Servicios',
                            data: serviciosByMonth,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + formatCurrency(tooltipItem
                                        .raw);
                                }
                            }
                        }
                    }
                }
            });

            // Variable para almacenar la instancia del gráfico de dona
            let topProductosChart = null;

            // Función para crear o actualizar el gráfico de dona
            function createDoughnutChart(labels, data) {
                const ctx = document.getElementById('topProductosChart').getContext('2d');

                // Destruir el gráfico existente si ya existe
                if (topProductosChart) {
                    console.log('Destruyendo gráfico anterior...');
                    topProductosChart.destroy();
                }

                // Crear un nuevo gráfico
                console.log('Creando nuevo gráfico con:', {
                    labels,
                    data
                });
                topProductosChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Productos Más Vendidos',
                            data: data,
                            backgroundColor: generateColors(labels.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + formatCurrency(tooltipItem
                                            .raw);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Crear el gráfico inicial
            console.log('Datos iniciales:', {
                labels: topProductosLabels,
                data: topProductosData
            });
            createDoughnutChart(topProductosLabels, topProductosData);

            // Evento del selector de mes (solo para el gráfico de dona)
            document.getElementById('monthSelector').addEventListener('change', function() {
                const selectedMonth = parseInt(this.value);
                console.log('Mes seleccionado:', selectedMonth);

                fetch(`/dashboard/data-by-month?month=${selectedMonth}&_=${Date.now()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Respuesta del servidor recibida:', response);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos procesados:', data);

                        // Validar los datos recibidos
                        if (!data || !data.top_productos_labels || !data.top_productos_data) {
                            console.error('Datos inválidos recibidos:', data);
                            throw new Error('Datos inválidos recibidos del servidor');
                        }

                        // Actualizar el gráfico de dona con los nuevos datos
                        createDoughnutChart(data.top_productos_labels, data.top_productos_data);

                        // Actualizar el título del gráfico con el mes seleccionado
                        const monthNames = <?php echo json_encode($monthNames); ?>;
                        document.querySelector('#topProductosChart').closest('.card').querySelector(
                                '.card-title').textContent =
                            `Productos Más Vendidos (${monthNames[selectedMonth - 1]})`;
                    })
                    .catch(error => {
                        console.error('Error al obtener datos:', error);
                        // Mostrar "Sin datos" en caso de error
                        createDoughnutChart(['Error al cargar datos'], [0]);
                    });
            });
        });
    </script>
@stop
