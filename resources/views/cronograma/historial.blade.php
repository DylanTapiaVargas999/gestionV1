<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-4 flex items-center gap-2 text-sm">
                <a href="{{ route('proyectos.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Proyectos</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.show', $proyecto) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $proyecto->nombre }}</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.cronograma.dashboard', $proyecto) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Cronograma Inteligente</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-700 font-semibold">Historial</span>
            </div>

            <!-- Header -->
            <div class="bg-white border-2 border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Historial de Ajustes</h1>
                            <p class="text-sm text-gray-600 mt-1">Registro completo de todos los ajustes del cronograma</p>
                        </div>
                    </div>
                    <a href="{{ route('proyectos.cronograma.dashboard', $proyecto) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg border border-gray-300 transition">
                        ← Volver al Dashboard
                    </a>
                </div>

                <!-- Estadísticas Generales -->
                <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t-2 border-gray-200">
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Total Ajustes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $ajustes->total() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Aplicados</p>
                        <p class="text-3xl font-bold text-green-700">{{ $estadisticas['aplicados'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-amber-600">{{ $estadisticas['propuestos'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Rechazados</p>
                        <p class="text-3xl font-bold text-red-700">{{ $estadisticas['rechazados'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white border-2 border-gray-200 rounded-xl p-4 mb-6 shadow-sm">
                <form method="GET" action="{{ route('proyectos.cronograma.historial', $proyecto) }}" class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            <option value="propuesto" {{ request('estado') == 'propuesto' ? 'selected' : '' }}>Propuesto</option>
                            <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="aplicado" {{ request('estado') == 'aplicado' ? 'selected' : '' }}>Aplicado</option>
                            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Estrategia</label>
                        <select name="estrategia" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas</option>
                            <option value="compresion" {{ request('estrategia') == 'compresion' ? 'selected' : '' }}>Compresión</option>
                            <option value="paralelizacion" {{ request('estrategia') == 'paralelizacion' ? 'selected' : '' }}>Paralelización</option>
                            <option value="reasignacion" {{ request('estrategia') == 'reasignacion' ? 'selected' : '' }}>Reasignación</option>
                            <option value="mixta" {{ request('estrategia') == 'mixta' ? 'selected' : '' }}>Mixta</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tipo</label>
                        <select name="tipo" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            <option value="automatico" {{ request('tipo') == 'automatico' ? 'selected' : '' }}>Automático</option>
                            <option value="manual" {{ request('tipo') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="solicitud_cambio" {{ request('tipo') == 'solicitud_cambio' ? 'selected' : '' }}>Solicitud de Cambio</option>
                        </select>
                    </div>
                    <div class="pt-5">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition">
                            🔍 Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de Ajustes -->
            @if($ajustes->isNotEmpty())
            <div class="space-y-3">
                @foreach($ajustes as $ajuste)
                <div class="bg-white border-2 border-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-200 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-600 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                            @if($ajuste->estado == 'aplicado')
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            @elseif($ajuste->estado == 'rechazado')
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            @elseif($ajuste->estado == 'aprobado')
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @else
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Ajuste #{{ $ajuste->id }}</h3>
                                <span class="px-3 py-1 bg-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-600 text-white text-xs font-bold rounded-full uppercase">{{ $ajuste->estado }}</span>
                                <span class="px-2 py-1 bg-gray-700 text-white text-xs font-semibold rounded uppercase">{{ $ajuste->estrategia }}</span>
                                <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-medium rounded capitalize">{{ $ajuste->tipo_ajuste }}</span>
                            </div>

                            <p class="text-gray-800 font-medium mb-3">{{ $ajuste->motivo_ajuste }}</p>

                            <div class="grid grid-cols-5 gap-3 mb-3">
                                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600">Score</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $ajuste->score_solucion }}/100</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-2 border border-green-200">
                                    <p class="text-xs font-semibold text-green-700">Días recuperados</p>
                                    <p class="text-lg font-bold text-green-800">+{{ $ajuste->dias_recuperados }}</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-2 border border-blue-200">
                                    <p class="text-xs font-semibold text-blue-700">Recursos</p>
                                    <p class="text-lg font-bold text-blue-800">{{ $ajuste->recursos_afectados }}</p>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-2 border border-orange-200">
                                    <p class="text-xs font-semibold text-orange-700">Riesgo</p>
                                    <p class="text-lg font-bold text-orange-800 capitalize">{{ $ajuste->nivel_riesgo }}</p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-2 border border-purple-200">
                                    <p class="text-xs font-semibold text-purple-700">Tareas</p>
                                    <p class="text-lg font-bold text-purple-800">{{ $ajuste->historialTareas->count() }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <div class="flex items-center gap-4">
                                    <span>📅 {{ $ajuste->created_at->format('d/m/Y H:i') }}</span>
                                    <span>👤 {{ $ajuste->creador->nombre ?? 'Sistema' }}</span>
                                    @if($ajuste->aprobador && $ajuste->fecha_aprobacion)
                                    <span>✓ {{ $ajuste->aprobador->nombre }} ({{ \Carbon\Carbon::parse($ajuste->fecha_aprobacion)->format('d/m/Y') }})</span>
                                    @endif
                                    @if($ajuste->fecha_aplicacion)
                                    <span>🚀 Aplicado: {{ \Carbon\Carbon::parse($ajuste->fecha_aplicacion)->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('proyectos.cronograma.ver-ajuste', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                    Ver Detalle →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $ajustes->withQueryString()->links() }}
            </div>
            @else
            <div class="bg-white border-2 border-gray-200 rounded-xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No hay ajustes registrados</h3>
                <p class="text-gray-600">Aún no se han generado ajustes para este proyecto.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
