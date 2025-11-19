<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Informes de Estado</h1>
                        <p class="text-gray-600 mt-1">Proyecto: {{ $proyecto->nombre }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Generado: {{ now()->format('d/m/Y H:i') }}</p>
                        <p class="text-sm font-semibold text-gray-900">Código: {{ $proyecto->codigo }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs de Informes -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="mostrarInforme('general')" id="tab-general"
                            class="tab-informe tab-active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Estado General
                        </button>
                        <button onclick="mostrarInforme('tareas')" id="tab-tareas"
                            class="tab-informe whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Requerimientos
                        </button>
                        <button onclick="mostrarInforme('equipo')" id="tab-equipo"
                            class="tab-informe whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Carga de Trabajo
                        </button>
                    </nav>
                </div>
            </div>

            <!-- ========== INFORME 01: ESTADO GENERAL ========== -->
            <div id="informe-general" class="informe-content">

                <!-- Métricas Principales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-blue-700 uppercase">Avance General</span>
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-lg font-bold">%</span>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeGeneral['proyecto']['avance_general'] }}%</p>
                        <p class="text-xs text-gray-700 mt-1">{{ $informeGeneral['tareas']['completadas'] }}/{{ $informeGeneral['tareas']['total'] }} tareas completadas</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-green-700 uppercase">Elementos Config</span>
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-lg font-bold">EC</span>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['total'] }}</p>
                        <p class="text-xs text-gray-700 mt-1">Madurez: {{ $informeGeneral['elementos_configuracion']['nivel_madurez'] }}%</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-purple-700 uppercase">Cumpl. Hitos</span>
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-lg font-bold">✓</span>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeGeneral['hitos']['cumplimiento'] }}%</p>
                        <p class="text-xs text-gray-700 mt-1">{{ $informeGeneral['hitos']['completados'] }}/{{ $informeGeneral['hitos']['totales'] }} hitos</p>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-red-700 uppercase">Riesgos Altos</span>
                            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-lg font-bold">!</span>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeGeneral['riesgos']['criticos'] + $informeGeneral['riesgos']['altos'] }}</p>
                        <p class="text-xs text-gray-700 mt-1">{{ $informeGeneral['riesgos']['total'] }} riesgos totales</p>
                    </div>
                </div>

                <!-- Información del Proyecto -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Información del Proyecto</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">ID Proyecto</p>
                            <p class="text-sm font-bold text-gray-900">{{ $informeGeneral['proyecto']['codigo'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Metodología</p>
                            <p class="text-sm font-bold text-gray-900">{{ $informeGeneral['proyecto']['metodologia'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Jefe de Proyecto</p>
                            <p class="text-sm font-bold text-gray-900">{{ $informeGeneral['proyecto']['jefe_proyecto'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Fecha Inicio</p>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $informeGeneral['proyecto']['fecha_inicio'] ? \Carbon\Carbon::parse($informeGeneral['proyecto']['fecha_inicio'])->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Distribución de Elementos de Configuración -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Por Tipo -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📦 Elementos de Configuración por Tipo</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Código Fuente</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_tipo']['codigo_fuente'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['total'] > 0 ? ($informeGeneral['elementos_configuracion']['por_tipo']['codigo_fuente'] / $informeGeneral['elementos_configuracion']['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Documentación</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_tipo']['documentacion'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['total'] > 0 ? ($informeGeneral['elementos_configuracion']['por_tipo']['documentacion'] / $informeGeneral['elementos_configuracion']['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Scripts BD</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_tipo']['scripts_bd'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['total'] > 0 ? ($informeGeneral['elementos_configuracion']['por_tipo']['scripts_bd'] / $informeGeneral['elementos_configuracion']['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Casos de Prueba</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_tipo']['casos_prueba'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['total'] > 0 ? ($informeGeneral['elementos_configuracion']['por_tipo']['casos_prueba'] / $informeGeneral['elementos_configuracion']['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Configuración</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_tipo']['configuracion'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['total'] > 0 ? ($informeGeneral['elementos_configuracion']['por_tipo']['configuracion'] / $informeGeneral['elementos_configuracion']['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Por Estado -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Estado de Control de Configuración</h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-xs text-green-700 font-medium mb-1">Aprobados</p>
                                <p class="text-2xl font-bold text-green-900">{{ $informeGeneral['elementos_configuracion']['por_estado_porcentaje']['aprobados'] }}%</p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-700 font-medium mb-1">Liberados</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $informeGeneral['elementos_configuracion']['por_estado_porcentaje']['liberados'] }}%</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-xs text-yellow-700 font-medium mb-1">En Revisión</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $informeGeneral['elementos_configuracion']['por_estado_porcentaje']['en_revision'] }}%</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-xs text-gray-700 font-medium mb-1">Obsoletos</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $informeGeneral['elementos_configuracion']['por_estado_porcentaje']['obsoletos'] }}%</p>
                            </div>
                        </div>
                        <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg p-4 mt-4">
                            <p class="text-sm font-semibold text-indigo-900 mb-1">Nivel de Madurez del Control</p>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-3">
                                    <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $informeGeneral['elementos_configuracion']['nivel_madurez'] }}%"></div>
                                </div>
                                <span class="text-2xl font-bold text-indigo-900">{{ $informeGeneral['elementos_configuracion']['nivel_madurez'] }}%</span>
                            </div>
                            <p class="text-xs text-indigo-700 mt-2">
                                @if($informeGeneral['elementos_configuracion']['nivel_madurez'] >= 90)
                                    Alto - Procesos estables y trazables
                                @elseif($informeGeneral['elementos_configuracion']['nivel_madurez'] >= 70)
                                    Medio-Alto - Control en evolución
                                @else
                                    En desarrollo - Requiere fortalecimiento
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Riesgos Identificados -->
                <div class="bg-white border-2 border-red-200 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Riesgos Técnicos Identificados</h3>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="bg-red-50 border border-red-300 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-black">{{ $informeGeneral['riesgos']['criticos'] }}</p>
                            <p class="text-sm font-semibold text-red-700">Críticos</p>
                        </div>
                        <div class="bg-orange-50 border border-orange-300 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-black">{{ $informeGeneral['riesgos']['altos'] }}</p>
                            <p class="text-sm font-semibold text-orange-700">Altos</p>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-black">{{ $informeGeneral['riesgos']['medios'] }}</p>
                            <p class="text-sm font-semibold text-yellow-700">Medios</p>
                        </div>
                    </div>
                    @if($informeGeneral['riesgos']['total'] > 0)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-red-900">
                            ⚡ {{ $informeGeneral['riesgos']['criticos'] + $informeGeneral['riesgos']['altos'] }} riesgos de alto impacto requieren atención inmediata
                        </p>
                        <p class="text-xs text-red-700 mt-1">Revisar el módulo de Cronograma Inteligente para ver detalles y acciones recomendadas</p>
                    </div>
                    @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-green-900">No se han identificado riesgos en este momento</p>
                    </div>
                    @endif
                </div>

            </div>

            <!-- ========== INFORME 02: REQUERIMIENTOS ========== -->
            <div id="informe-tareas" class="informe-content hidden">

                <!-- Alertas Críticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $informeTareas['alertas']['proximas_vencer'] }}</p>
                                <p class="text-sm font-semibold text-red-700">Próximas a Vencer</p>
                                <p class="text-xs text-black">(&lt;7 días)</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $informeTareas['alertas']['bloqueadas'] }}</p>
                                <p class="text-sm font-semibold text-yellow-700">Bloqueadas</p>
                                <p class="text-xs text-black">Requieren acción</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $informeTareas['alertas']['completadas_recientes'] }}</p>
                                <p class="text-sm font-semibold text-green-700">Completadas</p>
                                <p class="text-xs text-black">Últimos 10 días</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métricas de Implementación -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Métricas de Implementación por Prioridad</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Prioridad Alta -->
                        <div class="border-2 border-red-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-full">ALTA</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $informeTareas['metricas']['por_prioridad']['alta']['completadas'] }}/{{ $informeTareas['metricas']['por_prioridad']['alta']['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $porcentajeAlta = $informeTareas['metricas']['por_prioridad']['alta']['total'] > 0
                                        ? round(($informeTareas['metricas']['por_prioridad']['alta']['completadas'] / $informeTareas['metricas']['por_prioridad']['alta']['total']) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="bg-red-600 h-3 rounded-full" style="width: {{ $porcentajeAlta }}%"></div>
                            </div>
                            <p class="text-xs text-black mt-2">{{ $porcentajeAlta }}% completadas</p>
                        </div>

                        <!-- Prioridad Media -->
                        <div class="border-2 border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-bold rounded-full">MEDIA</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $informeTareas['metricas']['por_prioridad']['media']['completadas'] }}/{{ $informeTareas['metricas']['por_prioridad']['media']['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $porcentajeMedia = $informeTareas['metricas']['por_prioridad']['media']['total'] > 0
                                        ? round(($informeTareas['metricas']['por_prioridad']['media']['completadas'] / $informeTareas['metricas']['por_prioridad']['media']['total']) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="bg-yellow-600 h-3 rounded-full" style="width: {{ $porcentajeMedia }}%"></div>
                            </div>
                            <p class="text-xs text-black mt-2">{{ $porcentajeMedia }}% completadas</p>
                        </div>

                        <!-- Prioridad Baja -->
                        <div class="border-2 border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full">BAJA</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $informeTareas['metricas']['por_prioridad']['baja']['completadas'] }}/{{ $informeTareas['metricas']['por_prioridad']['baja']['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $porcentajeBaja = $informeTareas['metricas']['por_prioridad']['baja']['total'] > 0
                                        ? round(($informeTareas['metricas']['por_prioridad']['baja']['completadas'] / $informeTareas['metricas']['por_prioridad']['baja']['total']) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="bg-green-600 h-3 rounded-full" style="width: {{ $porcentajeBaja }}%"></div>
                            </div>
                            <p class="text-xs text-black mt-2">{{ $porcentajeBaja }}% completadas</p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Requerimientos -->
                <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Estado de Requerimientos Funcionales</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Descripción</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Fase</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Prioridad</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Responsable</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Vencimiento</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Días Restantes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($informeTareas['tareas_detalle']->take(20) as $tarea)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">RF-{{ str_pad($tarea['id'], 3, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ $tarea['nombre'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $tarea['fase'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($tarea['prioridad_texto'] == 'Alta')
                                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">Alta</span>
                                        @elseif($tarea['prioridad_texto'] == 'Media')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Media</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Baja</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $tarea['estado'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $tarea['responsable'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $tarea['fecha_vencimiento'] ? \Carbon\Carbon::parse($tarea['fecha_vencimiento'])->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($tarea['dias_restantes'] !== null)
                                            @if($tarea['dias_restantes'] < 0)
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded">{{ abs($tarea['dias_restantes']) }} días ATRASADA</span>
                                            @elseif($tarea['dias_restantes'] <= 7)
                                                <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded">{{ $tarea['dias_restantes'] }} días</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded">{{ $tarea['dias_restantes'] }} días</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ========== INFORME 03: CARGA DE TRABAJO ========== -->
            <div id="informe-equipo" class="informe-content hidden">

                <!-- Resumen de Recursos -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
                        <p class="text-xs font-bold text-blue-700 uppercase mb-2">Total Miembros</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeEquipo['resumen']['total_miembros'] }}</p>
                    </div>
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5">
                        <p class="text-xs font-bold text-green-700 uppercase mb-2">Utilización Promedio</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeEquipo['resumen']['utilizacion_promedio'] }}%</p>
                    </div>
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5">
                        <p class="text-xs font-bold text-red-700 uppercase mb-2">Sobrecargados</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeEquipo['resumen']['sobrecargados'] }}</p>
                    </div>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5">
                        <p class="text-xs font-bold text-gray-700 uppercase mb-2">Subutilizados</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $informeEquipo['resumen']['subutilizados'] }}</p>
                    </div>
                </div>

                <!-- Tabla de Miembros -->
                <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Distribución de Carga de Trabajo</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($informeEquipo['miembros'] as $miembro)
                        <div class="border-2 border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $miembro['nombre'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $miembro['email'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Equipo: {{ $miembro['equipo'] }}</p>
                                </div>
                                <div class="text-right">
                                    @if($miembro['nivel_carga'] == 'sobrecarga')
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-full">SOBRECARGA</span>
                                    @elseif($miembro['nivel_carga'] == 'subutilizado')
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-bold rounded-full">SUBUTILIZADO</span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full">NORMAL</span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-3">
                                <div class="text-center bg-blue-50 rounded-lg p-2">
                                    <p class="text-2xl font-bold text-blue-900">{{ $miembro['tareas_activas'] }}</p>
                                    <p class="text-xs text-black">Tareas Activas</p>
                                </div>
                                <div class="text-center bg-green-50 rounded-lg p-2">
                                    <p class="text-2xl font-bold text-green-900">{{ $miembro['tareas_completadas'] }}</p>
                                    <p class="text-xs text-black">Completadas</p>
                                </div>
                                <div class="text-center bg-purple-50 rounded-lg p-2">
                                    <p class="text-2xl font-bold text-purple-900">{{ round($miembro['horas_asignadas'], 1) }}h</p>
                                    <p class="text-xs text-black">Horas Asignadas</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-semibold text-black">Utilización</span>
                                    <span class="text-sm font-bold
                                        {{ $miembro['utilizacion'] > 100 ? 'text-red-700' : ($miembro['utilizacion'] < 50 ? 'text-gray-700' : 'text-green-700') }}">
                                        {{ $miembro['utilizacion'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $miembro['utilizacion'] > 100 ? 'bg-red-600' : ($miembro['utilizacion'] < 50 ? 'bg-gray-400' : 'bg-green-600') }}"
                                         style="width: {{ min(100, $miembro['utilizacion']) }}%"></div>
                                </div>
                                <p class="text-xs text-black mt-1">{{ round($miembro['horas_asignadas'], 1) }}h de {{ $miembro['horas_disponibles'] }}h semanales</p>
                            </div>
                        </div>
                        @endforeach

                        @if(count($informeEquipo['miembros']) == 0)
                        <div class="text-center py-8">
                            <p class="text-black">No hay miembros asignados al proyecto</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function mostrarInforme(tipo) {
            // Ocultar todos los informes
            document.querySelectorAll('.informe-content').forEach(el => el.classList.add('hidden'));

            // Remover clase activa de todos los tabs
            document.querySelectorAll('.tab-informe').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('border-transparent', 'text-gray-600');
                tab.classList.remove('border-indigo-600', 'text-indigo-600');
            });

            // Mostrar informe seleccionado
            document.getElementById('informe-' + tipo).classList.remove('hidden');

            // Activar tab
            const tab = document.getElementById('tab-' + tipo);
            tab.classList.add('tab-active', 'border-indigo-600', 'text-indigo-600');
            tab.classList.remove('border-transparent', 'text-gray-600');
        }

        // CSS para tabs
        const style = document.createElement('style');
        style.textContent = `
            .tab-informe {
                border-bottom-width: 2px;
                border-color: transparent;
                color: #4B5563;
                transition: all 0.2s;
            }
            .tab-informe:hover {
                color: #1F2937;
                border-color: #D1D5DB;
            }
            .tab-active {
                border-color: #4F46E5 !important;
                color: #4F46E5 !important;
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>
