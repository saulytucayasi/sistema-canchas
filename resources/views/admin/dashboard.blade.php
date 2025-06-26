<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Administrador') }}
            </h2>
            <div class="text-sm text-gray-500">
                Panel de control y analíticas avanzadas
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Estadísticas Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Usuarios -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90">Total Usuarios</h3>
                            <p class="text-3xl font-bold mt-1">{{ \App\Models\User::count() }}</p>
                            <p class="text-xs opacity-75 mt-1">+12% vs mes anterior</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Canchas -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90">Total Canchas</h3>
                            <p class="text-3xl font-bold mt-1">{{ \App\Models\Cancha::count() }}</p>
                            <p class="text-xs opacity-75 mt-1">{{ \App\Models\Cancha::where('activa', true)->count() }} activas</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m11 0a2 2 0 01-2 2H7a2 2 0 01-2-2m2-8h2m-2 0v8m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8M7 1h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Clientes -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90">Total Clientes</h3>
                            <p class="text-3xl font-bold mt-1">{{ \App\Models\Cliente::count() }}</p>
                            <p class="text-xs opacity-75 mt-1">{{ \App\Models\Cliente::where('activo', true)->count() }} activos</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ingresos del Mes -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium opacity-90">Ingresos del Mes</h3>
                            <p class="text-3xl font-bold mt-1">S/ {{ number_format(\App\Models\Reserva::whereMonth('fecha', now()->month)->where('estado', '!=', 'cancelada')->sum('precio_total'), 2) }}</p>
                            <p class="text-xs opacity-75 mt-1">{{ \App\Models\Reserva::whereDate('fecha', today())->count() }} reservas hoy</p>
                        </div>
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Gráfico de Reservas por Mes -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Reservas por Mes</h3>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">2024</span>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="reservasPorMesChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Estados de Reservas -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Estados de Reservas</h3>
                        <select class="text-sm border-gray-300 rounded-md" id="estadosFilter">
                            <option value="all">Todos los tiempos</option>
                            <option value="month" selected>Este mes</option>
                            <option value="week">Esta semana</option>
                        </select>
                    </div>
                    <div class="h-80">
                        <canvas id="estadosReservasChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráficos Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Top Canchas más Utilizadas -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Canchas Más Populares</h3>
                    <div class="h-80">
                        <canvas id="topCanchasChart"></canvas>
                    </div>
                </div>

                <!-- Ingresos por Semana -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Ingresos Semanales</h3>
                    <div class="h-80">
                        <canvas id="ingresosSemanaChart"></canvas>
                    </div>
                </div>

                <!-- Horarios Más Demandados -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Horarios Populares</h3>
                    <div class="h-80">
                        <canvas id="horariosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráficos Row 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Ocupación por Día de la Semana -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Ocupación por Día</h3>
                    <div class="h-80">
                        <canvas id="ocupacionDiaChart"></canvas>
                    </div>
                </div>

                <!-- Clientes Más Frecuentes -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Clientes</h3>
                    <div class="space-y-4">
                        @php
                        $topClientes = \App\Models\Cliente::withCount('reservas')
                            ->orderBy('reservas_count', 'desc')
                            ->take(5)
                            ->get();
                        @endphp
                        @foreach($topClientes as $index => $cliente)
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-r {{ $index === 0 ? 'from-yellow-400 to-yellow-500' : ($index === 1 ? 'from-gray-400 to-gray-500' : ($index === 2 ? 'from-orange-400 to-orange-500' : 'from-blue-400 to-blue-500')) }} rounded-full flex items-center justify-center text-white font-bold">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                <p class="text-sm text-gray-600">{{ $cliente->reservas_count }} reservas</p>
                            </div>
                            <div class="text-sm font-medium text-green-600">
                                S/ {{ number_format($cliente->reservas()->where('estado', '!=', 'cancelada')->sum('precio_total'), 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos Mejorados -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Accesos Rápidos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="{{ route('admin.usuarios') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:from-blue-100 hover:to-blue-200 transition-all duration-200 transform hover:scale-105">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Gestionar Usuarios</h4>
                                <p class="text-sm text-gray-600">Administrar roles y permisos</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.canchas') }}" class="group bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 hover:from-green-100 hover:to-green-200 transition-all duration-200 transform hover:scale-105">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-colors">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Gestionar Canchas</h4>
                                <p class="text-sm text-gray-600">Horarios y configuración</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.reservas') }}" class="group bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 hover:from-orange-100 hover:to-orange-200 transition-all duration-200 transform hover:scale-105">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-orange-500 rounded-xl flex items-center justify-center group-hover:bg-orange-600 transition-colors">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Gestionar Reservas</h4>
                                <p class="text-sm text-gray-600">Control de reservaciones</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Configuración global de Chart.js
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.color = '#6B7280';

        // Función para destruir gráficos existentes antes de crear nuevos
        function destroyExistingCharts() {
            if (window.dashboardCharts) {
                Object.values(window.dashboardCharts).forEach(chart => {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });
            }
            window.dashboardCharts = {};
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            destroyExistingCharts();
            initializeCharts();
        });

        function initializeCharts() {

            // Gráfico de Reservas por Mes
            const reservasPorMesElement = document.getElementById('reservasPorMesChart');
            if (reservasPorMesElement) {
                const reservasPorMesCtx = reservasPorMesElement.getContext('2d');
                window.dashboardCharts.reservasPorMes = new Chart(reservasPorMesCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Reservas',
                    data: [
                        @php
                        $reservasPorMes = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $reservasPorMes[] = \App\Models\Reserva::whereYear('fecha', now()->year)
                                ->whereMonth('fecha', $i)->count();
                        }
                        echo implode(',', $reservasPorMes);
                        @endphp
                    ],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
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
                        }
                    }
                }
            }
                });
            }

            // Gráfico de Estados de Reservas (Doughnut)
            const estadosReservasElement = document.getElementById('estadosReservasChart');
            if (estadosReservasElement) {
                const estadosReservasCtx = estadosReservasElement.getContext('2d');
                window.dashboardCharts.estadosReservas = new Chart(estadosReservasCtx, {
            type: 'doughnut',
            data: {
                labels: ['Confirmadas', 'Pendientes', 'Canceladas', 'Completadas'],
                datasets: [{
                    data: [
                        {{ \App\Models\Reserva::where('estado', 'confirmada')->count() }},
                        {{ \App\Models\Reserva::where('estado', 'pendiente')->count() }},
                        {{ \App\Models\Reserva::where('estado', 'cancelada')->count() }},
                        {{ \App\Models\Reserva::where('estado', 'completada')->count() }}
                    ],
                    backgroundColor: [
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#3B82F6'
                    ],
                    borderWidth: 0,
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
            }

            // Gráfico de Top Canchas (Bar horizontal)
            const topCanchasElement = document.getElementById('topCanchasChart');
            if (topCanchasElement) {
                const topCanchasCtx = topCanchasElement.getContext('2d');
        @php
        $topCanchas = \App\Models\Cancha::withCount('reservas')
            ->orderBy('reservas_count', 'desc')
            ->take(5)
            ->get();
                @endphp
                window.dashboardCharts.topCanchas = new Chart(topCanchasCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topCanchas as $cancha)
                        '{{ $cancha->nombre }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Reservas',
                    data: [
                        @foreach($topCanchas as $cancha)
                            {{ $cancha->reservas_count }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#8B5CF6',
                        '#06B6D4',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
                });
            }

            // Gráfico de Ingresos por Semana
            const ingresosSemanaElement = document.getElementById('ingresosSemanaChart');
            if (ingresosSemanaElement) {
                const ingresosSemanaCtx = ingresosSemanaElement.getContext('2d');
                window.dashboardCharts.ingresosSemana = new Chart(ingresosSemanaCtx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Ingresos S/',
                    data: [
                        @php
                        $ingresosPorDia = [];
                        for ($i = 1; $i <= 7; $i++) {
                            $ingresosPorDia[] = \App\Models\Reserva::whereRaw('DAYOFWEEK(fecha) = ?', [$i])
                                ->where('estado', '!=', 'cancelada')
                                ->sum('precio_total');
                        }
                        echo implode(',', $ingresosPorDia);
                        @endphp
                    ],
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10B981',
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
                        }
                    }
                }
            }
                });
            }

            // Gráfico de Horarios Más Demandados (Polar Area)
            const horariosElement = document.getElementById('horariosChart');
            if (horariosElement) {
                const horariosCtx = horariosElement.getContext('2d');
                window.dashboardCharts.horarios = new Chart(horariosCtx, {
            type: 'polarArea',
            data: {
                labels: ['Mañana (6-12)', 'Tarde (12-18)', 'Noche (18-24)'],
                datasets: [{
                    data: [
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '06:00')->whereTime('hora_inicio', '<', '12:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '12:00')->whereTime('hora_inicio', '<', '18:00')->count() }},
                        {{ \App\Models\Reserva::whereTime('hora_inicio', '>=', '18:00')->whereTime('hora_inicio', '<', '24:00')->count() }}
                    ],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        '#F59E0B',
                        '#3B82F6',
                        '#8B5CF6'
                    ],
                    borderWidth: 2
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
                            padding: 15
                        }
                    }
                }
            }
                });
            }

            // Gráfico de Ocupación por Día de la Semana (Radar)
            const ocupacionDiaElement = document.getElementById('ocupacionDiaChart');
            if (ocupacionDiaElement) {
                const ocupacionDiaCtx = ocupacionDiaElement.getContext('2d');
                window.dashboardCharts.ocupacionDia = new Chart(ocupacionDiaCtx, {
            type: 'radar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                datasets: [{
                    label: 'Ocupación %',
                    data: [
                        @php
                        $ocupacionPorDia = [];
                        $totalCanchas = \App\Models\Cancha::count();
                        for ($i = 1; $i <= 7; $i++) {
                            $reservas = \App\Models\Reserva::whereRaw('DAYOFWEEK(fecha) = ?', [$i])->count();
                            $ocupacion = $totalCanchas > 0 ? ($reservas / ($totalCanchas * 10)) * 100 : 0; // Asumiendo 10 slots por día
                            $ocupacionPorDia[] = round($ocupacion, 1);
                        }
                        echo implode(',', $ocupacionPorDia);
                        @endphp
                    ],
                    backgroundColor: 'rgba(236, 72, 153, 0.2)',
                    borderColor: '#EC4899',
                    borderWidth: 3,
                    pointBackgroundColor: '#EC4899',
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
                        max: 100,
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
    </script>
</x-app-layout>