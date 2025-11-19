<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Estadísticas y Análisis</h1>
                                <p class="text-sm text-gray-600 mt-1">Métricas y progreso de todos tus proyectos</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 font-medium">Última actualización</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicadores Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Proyectos -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Total Proyectos</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalProyectos }}</p>
                    <div class="mt-3 flex gap-2">
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded font-medium">{{ $proyectosActivos }} activos</span>
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded font-medium">{{ $proyectosCompletados }} completos</span>
                    </div>
                </div>

                <!-- Total Tareas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Total Tareas</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalTareas }}</p>
                    <div class="mt-3">
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded font-medium">{{ $tareasCompletadas }} completadas</span>
                    </div>
                </div>

                <!-- Total Liberaciones -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Liberaciones</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalLiberaciones }}</p>
                </div>

                <!-- Ítems de Cambio -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-pink-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Ítems de Cambio</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCambios }}</p>
                    <div class="mt-3">
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-700 rounded font-medium">{{ $cambiosAprobados }} aprobados</span>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Progreso de Proyectos -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 p-5 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Progreso de Proyectos</h2>
                                <p class="text-xs text-gray-600">Porcentaje de completitud de tareas</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="progresoProyectosChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Distribución de Tareas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 p-5 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Distribución de Tareas</h2>
                                <p class="text-xs text-gray-600">Por estado actual</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="distribucionTareasChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Liberaciones por Mes -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 p-5 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Liberaciones por Mes</h2>
                                <p class="text-xs text-gray-600">Últimos 6 meses</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="liberacionesMesChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Proyectos por Metodología -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 p-5 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Proyectos por Metodología</h2>
                                <p class="text-xs text-gray-600">Distribución</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="metodologiasChart" height="300"></canvas>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // ============================================
        // 📈 GRÁFICA: Progreso de Proyectos (Barras Horizontales)
        // ============================================
        const proyectosData = @json($proyectosConProgreso);

        new Chart(document.getElementById('progresoProyectosChart'), {
            type: 'bar',
            data: {
                labels: proyectosData.map(p => p.codigo),
                datasets: [{
                    label: 'Progreso (%)',
                    data: proyectosData.map(p => p.progreso),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const proyecto = proyectosData[context.dataIndex];
                                return `${context.parsed.x}% - ${proyecto.tareas_completadas}/${proyecto.total_tareas} tareas`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // ============================================
        // 🎯 GRÁFICA: Distribución de Tareas (Pastel)
        // ============================================
        const distribucionData = @json($distribucionTareas);

        new Chart(document.getElementById('distribucionTareasChart'), {
            type: 'doughnut',
            data: {
                labels: distribucionData.map(d => d.estado),
                datasets: [{
                    data: distribucionData.map(d => d.cantidad),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',  // Verde - Completadas
                        'rgba(251, 191, 36, 0.8)', // Amarillo - En Progreso
                        'rgba(239, 68, 68, 0.8)'   // Rojo - Pendientes
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // ============================================
        // 🚀 GRÁFICA: Liberaciones por Mes (Línea)
        // ============================================
        const liberacionesData = @json($liberacionesPorMes);

        new Chart(document.getElementById('liberacionesMesChart'), {
            type: 'line',
            data: {
                labels: liberacionesData.map(l => l.mes),
                datasets: [{
                    label: 'Liberaciones',
                    data: liberacionesData.map(l => l.cantidad),
                    backgroundColor: 'rgba(20, 184, 166, 0.2)',
                    borderColor: 'rgba(20, 184, 166, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // ============================================
        // 📋 GRÁFICA: Proyectos por Metodología (Pastel)
        // ============================================
        const metodologiasData = @json($proyectosPorMetodologia);

        new Chart(document.getElementById('metodologiasChart'), {
            type: 'pie',
            data: {
                labels: metodologiasData.map(m => m.nombre),
                datasets: [{
                    data: metodologiasData.map(m => m.cantidad),
                    backgroundColor: [
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)'
                    ],
                    borderColor: [
                        'rgba(236, 72, 153, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(251, 146, 60, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
