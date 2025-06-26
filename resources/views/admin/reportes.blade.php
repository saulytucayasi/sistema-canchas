<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reportes y Analíticas Avanzadas') }}
            </h2>
            <div class="flex items-center space-x-4">
                <select class="text-sm border-gray-300 rounded-lg" id="reportPeriod">
                    <option value="7">Últimos 7 días</option>
                    <option value="30" selected>Últimos 30 días</option>
                    <option value="90">Últimos 3 meses</option>
                    <option value="365">Último año</option>
                </select>
                <button onclick="exportToPDF()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center space-x-2 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>
    </x-slot>

    <x-slot name="breadcrumbs">
        @php
        $breadcrumbs = [
            ['title' => 'Admin', 'url' => route('admin.dashboard')],
            ['title' => 'Reportes y Analíticas']
        ];
        @endphp
        <x-breadcrumbs :items="$breadcrumbs" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Ingresos Totales -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90">Ingresos Totales</h3>
                            @php
                            $ingresosTotales = \App\Models\Reserva::where('estado', '!=', 'cancelada')->sum('precio_total');
                            $ingresosMesAnterior = \App\Models\Reserva::whereMonth('fecha', now()->subMonth()->month)
                                ->where('estado', '!=', 'cancelada')->sum('precio_total');
                            $ingresosMesActual = \App\Models\Reserva::whereMonth('fecha', now()->month)
                                ->where('estado', '!=', 'cancelada')->sum('precio_total');
                            $crecimientoIngresos = $ingresosMesAnterior > 0 ? (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 : 0;
                            @endphp
                            <p class="text-3xl font-bold mt-1">S/ {{ number_format($ingresosTotales, 2) }}</p>
                            <p class="text-xs opacity-75 mt-1">
                                {{ $crecimientoIngresos >= 0 ? '+' : '' }}{{ number_format($crecimientoIngresos, 1) }}% vs mes anterior
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tasa de Ocupación -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            @php
                            $totalSlots = \App\Models\Cancha::count() * 12 * 30; // 12 horas x 30 días
                            $reservasOcupadas = \App\Models\Reserva::whereMonth('fecha', now()->month)->count();
                            $tasaOcupacion = $totalSlots > 0 ? ($reservasOcupadas / $totalSlots) * 100 : 0;
                            @endphp
                            <h3 class="text-sm font-medium opacity-90">Tasa de Ocupación</h3>
                            <p class="text-3xl font-bold mt-1">{{ number_format($tasaOcupacion, 1) }}%</p>
                            <p class="text-xs opacity-75 mt-1">{{ $reservasOcupadas }} de {{ $totalSlots }} slots</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ticket Promedio -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            @php
                            $totalReservas = \App\Models\Reserva::where('estado', '!=', 'cancelada')->count();
                            $ticketPromedio = $totalReservas > 0 ? $ingresosTotales / $totalReservas : 0;
                            @endphp
                            <h3 class="text-sm font-medium opacity-90">Ticket Promedio</h3>
                            <p class="text-3xl font-bold mt-1">S/ {{ number_format($ticketPromedio, 2) }}</p>
                            <p class="text-xs opacity-75 mt-1">Por reserva completada</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1V7a2 2 0 00-2-2H5zM5 14a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tasa de Cancelación -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            @php
                            $totalReservasCompletas = \App\Models\Reserva::count();
                            $reservasCanceladas = \App\Models\Reserva::where('estado', 'cancelada')->count();
                            $tasaCancelacion = $totalReservasCompletas > 0 ? ($reservasCanceladas / $totalReservasCompletas) * 100 : 0;
                            @endphp
                            <h3 class="text-sm font-medium opacity-90">Tasa de Cancelación</h3>
                            <p class="text-3xl font-bold mt-1">{{ number_format($tasaCancelacion, 1) }}%</p>
                            <p class="text-xs opacity-75 mt-1">{{ $reservasCanceladas }} canceladas</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos Avanzados Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Evolución de Ingresos -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Evolución de Ingresos</h3>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm px-3 py-1 bg-blue-100 text-blue-700 rounded-md" onclick="changeIngresosChart('monthly')">Mensual</button>
                            <button class="text-sm px-3 py-1 text-gray-600 rounded-md" onclick="changeIngresosChart('weekly')">Semanal</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="evolucionIngresosChart"></canvas>
                    </div>
                </div>

                <!-- Análisis de Rentabilidad por Cancha -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Rentabilidad por Cancha</h3>
                    <div class="h-80">
                        <canvas id="rentabilidadCanchaChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráficos Avanzados Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Heatmap de Reservas -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Mapa de Calor - Reservas</h3>
                    <div class="space-y-2">
                        @php
                        $horas = ['6:00', '8:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];
                        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                        @endphp
                        <div class="grid grid-cols-8 gap-1 text-xs">
                            <div></div>
                            @foreach($dias as $dia)
                            <div class="text-center font-medium text-gray-600">{{ $dia }}</div>
                            @endforeach
                        </div>
                        @foreach($horas as $hora)
                        <div class="grid grid-cols-8 gap-1">
                            <div class="text-xs text-gray-600 pr-2">{{ $hora }}</div>
                            @for($d = 1; $d <= 7; $d++)
                                @php
                                $reservas = \App\Models\Reserva::whereRaw('DAYOFWEEK(fecha) = ?', [$d])
                                    ->whereTime('hora_inicio', '>=', $hora)
                                    ->whereTime('hora_inicio', '<', date('H:i', strtotime($hora . ' +2 hours')))
                                    ->count();
                                $intensidad = min($reservas * 10, 100);
                                @endphp
                                <div class="h-6 rounded" style="background-color: rgba(59, 130, 246, {{ $intensidad / 100 }});" title="{{ $reservas }} reservas"></div>
                            @endfor
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Análisis de Clientes -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Segmentación de Clientes</h3>
                    <div class="h-80">
                        <canvas id="segmentacionClientesChart"></canvas>
                    </div>
                </div>

                <!-- Tendencias de Horarios -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Tendencias de Horarios</h3>
                    <div class="h-80">
                        <canvas id="tendenciasHorariosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Análisis Detallado Row 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Clientes VIP -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Clientes VIP</h3>
                    <div class="space-y-4">
                        @php
                        $clientesVIP = \App\Models\Cliente::withCount('reservas')
                            ->withSum(['reservas' => function($query) {
                                $query->where('estado', '!=', 'cancelada');
                            }], 'precio_total')
                            ->orderBy('reservas_sum_precio_total', 'desc')
                            ->take(8)
                            ->get();
                        @endphp
                        @foreach($clientesVIP as $index => $cliente)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg hover:from-gray-100 hover:to-gray-200 transition-all">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-gradient-to-r {{ $index < 3 ? 'from-yellow-400 to-yellow-500' : 'from-blue-400 to-blue-500' }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                    <p class="text-sm text-gray-600">{{ $cliente->reservas_count }} reservas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600">S/ {{ number_format($cliente->reservas_sum_precio_total ?? 0, 2) }}</p>
                                <p class="text-xs text-gray-500">Total gastado</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Performance por Horario -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance por Franjas Horarias</h3>
                    <div class="space-y-4">
                        @php
                        $horarios = [
                            ['nombre' => 'Mañana Temprana (6-9)', 'inicio' => '06:00', 'fin' => '09:00', 'color' => 'from-yellow-400 to-yellow-500'],
                            ['nombre' => 'Mañana (9-12)', 'inicio' => '09:00', 'fin' => '12:00', 'color' => 'from-orange-400 to-orange-500'],
                            ['nombre' => 'Mediodía (12-15)', 'inicio' => '12:00', 'fin' => '15:00', 'color' => 'from-red-400 to-red-500'],
                            ['nombre' => 'Tarde (15-18)', 'inicio' => '15:00', 'fin' => '18:00', 'color' => 'from-purple-400 to-purple-500'],
                            ['nombre' => 'Noche (18-21)', 'inicio' => '18:00', 'fin' => '21:00', 'color' => 'from-blue-400 to-blue-500'],
                            ['nombre' => 'Noche Tardía (21-24)', 'inicio' => '21:00', 'fin' => '24:00', 'color' => 'from-indigo-400 to-indigo-500']
                        ];
                        @endphp
                        @foreach($horarios as $horario)
                        @php
                        $reservasHorario = \App\Models\Reserva::whereTime('hora_inicio', '>=', $horario['inicio'])
                            ->whereTime('hora_inicio', '<', $horario['fin'])
                            ->where('estado', '!=', 'cancelada')
                            ->count();
                        $ingresosHorario = \App\Models\Reserva::whereTime('hora_inicio', '>=', $horario['inicio'])
                            ->whereTime('hora_inicio', '<', $horario['fin'])
                            ->where('estado', '!=', 'cancelada')
                            ->sum('precio_total');
                        $maxReservas = 100; // Para calcular el porcentaje de la barra
                        $porcentaje = min(($reservasHorario / $maxReservas) * 100, 100);
                        @endphp
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $horario['nombre'] }}</h4>
                                <span class="text-sm font-medium text-green-600">S/ {{ number_format($ingresosHorario, 2) }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r {{ $horario['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $reservasHorario }} reservas</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Métricas de Rendimiento -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Métricas de Rendimiento del Negocio</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                    $reservasHoy = \App\Models\Reserva::whereDate('fecha', today())->count();
                    $ingresosMesActual = \App\Models\Reserva::whereMonth('fecha', now()->month)->where('estado', '!=', 'cancelada')->sum('precio_total');
                    $promedioReservasDiarias = \App\Models\Reserva::whereMonth('fecha', now()->month)->count() / now()->day;
                    $canchasMasPopular = \App\Models\Cancha::withCount('reservas')->orderBy('reservas_count', 'desc')->first();
                    @endphp
                    
                    <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <div class="text-3xl font-bold text-blue-600">{{ $reservasHoy }}</div>
                        <div class="text-sm text-blue-700 mt-1">Reservas Hoy</div>
                        <div class="text-xs text-blue-500 mt-2">{{ number_format($promedioReservasDiarias, 1) }} promedio/día</div>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                        <div class="text-3xl font-bold text-green-600">S/ {{ number_format($ingresosMesActual) }}</div>
                        <div class="text-sm text-green-700 mt-1">Ingresos del Mes</div>
                        <div class="text-xs text-green-500 mt-2">Meta: S/ 50,000</div>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                        <div class="text-3xl font-bold text-purple-600">{{ $canchasMasPopular ? $canchasMasPopular->reservas_count : 0 }}</div>
                        <div class="text-sm text-purple-700 mt-1">Top Cancha</div>
                        <div class="text-xs text-purple-500 mt-2">{{ $canchasMasPopular->nombre ?? 'N/A' }}</div>
                    </div>

                    <div class="text-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                        <div class="text-3xl font-bold text-orange-600">{{ number_format(100 - $tasaCancelacion, 1) }}%</div>
                        <div class="text-sm text-orange-700 mt-1">Tasa de Éxito</div>
                        <div class="text-xs text-orange-500 mt-2">Reservas completadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        // Configuración global de Chart.js
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.color = '#6B7280';

        // Variables globales para filtros
        let currentPeriod = 30;
        let reportsCharts = {};

        // Función para destruir gráficos existentes
        function destroyExistingReportsCharts() {
            Object.values(reportsCharts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            reportsCharts = {};
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            destroyExistingReportsCharts();
            initializeReportsCharts();
            setupEventListeners();
        });

        function initializeReportsCharts() {

            // Gráfico de Evolución de Ingresos
            const evolucionIngresosElement = document.getElementById('evolucionIngresosChart');
            if (evolucionIngresosElement) {
                const evolucionIngresosCtx = evolucionIngresosElement.getContext('2d');
                reportsCharts.evolucionIngresos = new Chart(evolucionIngresosCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Ingresos S/',
                    data: [
                        @php
                        $ingresosPorMes = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $ingresosPorMes[] = \App\Models\Reserva::whereYear('fecha', now()->year)
                                ->whereMonth('fecha', $i)
                                ->where('estado', '!=', 'cancelada')
                                ->sum('precio_total');
                        }
                        echo implode(',', $ingresosPorMes);
                        @endphp
                    ],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Rentabilidad por Cancha
        const rentabilidadCanchaCtx = document.getElementById('rentabilidadCanchaChart').getContext('2d');
        @php
        $canchasRentabilidad = \App\Models\Cancha::withSum(['reservas' => function($query) {
            $query->where('estado', '!=', 'cancelada');
        }], 'precio_total')->get();
        @endphp
        new Chart(rentabilidadCanchaCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($canchasRentabilidad as $cancha)
                        '{{ $cancha->nombre }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Ingresos S/',
                    data: [
                        @foreach($canchasRentabilidad as $cancha)
                            {{ $cancha->reservas_sum_precio_total ?? 0 }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(6, 182, 212, 0.8)'
                    ],
                    borderColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#06B6D4'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Segmentación de Clientes
        const segmentacionClientesCtx = document.getElementById('segmentacionClientesChart').getContext('2d');
        @php
        $clientesNuevos = \App\Models\Cliente::whereMonth('created_at', now()->month)->count();
        
        // Solución simple y segura para evitar problemas con GROUP BY
        $clientesConReservasMultiples = \App\Models\Cliente::has('reservas', '>=', 2)->count();
        $clientesRecurrentes = $clientesConReservasMultiples;
        
        $clientesInactivos = \App\Models\Cliente::whereDoesntHave('reservas', function($query) {
            $query->where('fecha', '>=', now()->subMonths(3));
        })->count();
        @endphp
        new Chart(segmentacionClientesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Recurrentes', 'Nuevos', 'Inactivos'],
                datasets: [{
                    data: [{{ $clientesRecurrentes }}, {{ $clientesNuevos }}, {{ $clientesInactivos }}],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        '#10B981',
                        '#3B82F6',
                        '#EF4444'
                    ],
                    borderWidth: 2,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });

        // Gráfico de Tendencias de Horarios
        const tendenciasHorariosCtx = document.getElementById('tendenciasHorariosChart').getContext('2d');
        new Chart(tendenciasHorariosCtx, {
            type: 'radar',
            data: {
                labels: ['6-9', '9-12', '12-15', '15-18', '18-21', '21-24'],
                datasets: [{
                    label: 'Reservas',
                    data: [
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '06:00')->whereTime('hora_inicio', '<', '09:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '09:00')->whereTime('hora_inicio', '<', '12:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '12:00')->whereTime('hora_inicio', '<', '15:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '15:00')->whereTime('hora_inicio', '<', '18:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '18:00')->whereTime('hora_inicio', '<', '21:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '21:00')->whereTime('hora_inicio', '<', '24:00')->count() }}
                    ],
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    borderColor: '#8B5CF6',
                    borderWidth: 3,
                    pointBackgroundColor: '#8B5CF6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        },
                        pointLabels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
                });
            }
        }

        // Configurar event listeners
        function setupEventListeners() {
            // Filtro de período
            const reportPeriodSelect = document.getElementById('reportPeriod');
            if (reportPeriodSelect) {
                reportPeriodSelect.addEventListener('change', function() {
                    currentPeriod = parseInt(this.value);
                    updateChartsWithPeriod(currentPeriod);
                });
            }

            // El botón de exportar PDF ya tiene onclick="exportToPDF()" en el HTML
        }

        // Función para cambiar vista de ingresos
        function changeIngresosChart(period) {
            // Actualizar botones activos
            document.querySelectorAll('[onclick^="changeIngresosChart"]').forEach(btn => {
                btn.className = 'text-sm px-3 py-1 text-gray-600 rounded-md';
            });
            event.target.className = 'text-sm px-3 py-1 bg-blue-100 text-blue-700 rounded-md';
            
            console.log('Cambiando vista a:', period);
            // Aquí se puede implementar lógica adicional para cambiar datos
        }

        // Función para actualizar gráficos según el período
        function updateChartsWithPeriod(days) {
            // Mostrar indicador de carga
            showLoadingIndicator();
            
            // Simular actualización de datos (en un caso real, harías una petición AJAX)
            setTimeout(() => {
                // Aquí actualizarías los datos de los gráficos
                console.log(`Actualizando gráficos para los últimos ${days} días`);
                hideLoadingIndicator();
                
                // Mostrar notificación
                showNotification(`Reportes actualizados para los últimos ${days} días`);
            }, 1000);
        }

        // Función para exportar a PDF
        async function exportToPDF() {
            try {
                showLoadingIndicator('Generando PDF...');
                
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                
                // Configurar el PDF
                pdf.setFontSize(16);
                pdf.text('Reportes y Analíticas - Sistema de Canchas', 20, 20);
                
                pdf.setFontSize(12);
                const today = new Date().toLocaleDateString('es-PE');
                pdf.text(`Fecha de generación: ${today}`, 20, 30);
                
                // Capturar las estadísticas principales
                const statsSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4.gap-6').parentElement;
                if (statsSection) {
                    const canvas = await html2canvas(statsSection, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff'
                    });
                    
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = 170;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    pdf.addImage(imgData, 'PNG', 20, 40, imgWidth, imgHeight);
                }
                
                // Agregar una nueva página para los gráficos
                pdf.addPage();
                pdf.text('Gráficos de Análisis', 20, 20);
                
                // Capturar el primer set de gráficos
                const chartsRow1 = document.querySelector('.grid.grid-cols-1.lg\\:grid-cols-2.gap-8');
                if (chartsRow1) {
                    const canvas = await html2canvas(chartsRow1, {
                        scale: 1.5,
                        useCORS: true,
                        backgroundColor: '#ffffff'
                    });
                    
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = 170;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    pdf.addImage(imgData, 'PNG', 20, 30, imgWidth, Math.min(imgHeight, 200));
                }
                
                // Guardar el PDF
                pdf.save(`reporte-canchas-${today.replace(/\\//g, '-')}.pdf`);
                
                hideLoadingIndicator();
                showNotification('PDF generado exitosamente');
                
            } catch (error) {
                console.error('Error generando PDF:', error);
                hideLoadingIndicator();
                showNotification('Error al generar el PDF', 'error');
            }
        }

        // Funciones auxiliares
        function showLoadingIndicator(message = 'Cargando...') {
            const indicator = document.createElement('div');
            indicator.id = 'loadingIndicator';
            indicator.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            indicator.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-800">${message}</span>
                </div>
            `;
            document.body.appendChild(indicator);
        }

        function hideLoadingIndicator() {
            const indicator = document.getElementById('loadingIndicator');
            if (indicator) {
                indicator.remove();
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>