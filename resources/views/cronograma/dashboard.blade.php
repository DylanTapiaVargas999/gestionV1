<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Mensajes Flash -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

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
                <span class="text-gray-700 font-semibold">Cronograma Inteligente</span>
            </div>

            <!-- Header -->
            <div class="bg-white border-2 border-indigo-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Cronograma Inteligente</h1>
                            <p class="text-sm text-gray-600 mt-1">Ajuste automático y optimización del cronograma</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('proyectos.cronograma.historial', $proyecto) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg border border-gray-300 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Historial
                        </a>
                        <form action="{{ route('proyectos.cronograma.generar', $proyecto) }}" method="POST" id="formGenerarAjuste">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg shadow-md transition flex items-center gap-2" id="btnGenerarAjuste">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span id="btnText">Generar Ajuste Automático</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Estado General del Proyecto -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-{{ $analisis['salud_proyecto']['nivel'] == 'optimo' ? 'green' : ($analisis['salud_proyecto']['nivel'] == 'bueno' ? 'yellow' : ($analisis['salud_proyecto']['nivel'] == 'regular' ? 'orange' : 'red')) }}-200 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Salud del Proyecto</span>
                        <div class="w-8 h-8 rounded-lg bg-{{ $analisis['salud_proyecto']['nivel'] == 'optimo' ? 'green' : ($analisis['salud_proyecto']['nivel'] == 'bueno' ? 'yellow' : ($analisis['salud_proyecto']['nivel'] == 'regular' ? 'orange' : 'red')) }}-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-{{ $analisis['salud_proyecto']['nivel'] == 'optimo' ? 'green' : ($analisis['salud_proyecto']['nivel'] == 'bueno' ? 'yellow' : ($analisis['salud_proyecto']['nivel'] == 'regular' ? 'orange' : 'red')) }}-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ round($analisis['salud_proyecto']['score']) }}<span class="text-lg text-gray-600">/100</span></p>
                    <p class="text-sm font-medium text-gray-700 mt-1 capitalize">{{ $analisis['salud_proyecto']['nivel'] }}</p>
                </div>

                <div class="bg-white border border-blue-200 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Ruta Crítica</span>
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ round($analisis['ruta_critica']['duracion_proyecto']) }}</p>
                    <p class="text-sm font-medium text-gray-700 mt-1">días de duración</p>
                </div>

                <div class="bg-white border border-{{ $analisis['salud_proyecto']['total_desviaciones'] > 0 ? 'red' : 'green' }}-200 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Desviaciones</span>
                        <svg class="w-6 h-6 text-{{ $analisis['salud_proyecto']['total_desviaciones'] > 0 ? 'red' : 'green' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $analisis['salud_proyecto']['total_desviaciones'] }}</p>
                    <p class="text-sm font-medium text-gray-700 mt-1">{{ $analisis['salud_proyecto']['total_desviaciones'] == 1 ? 'problema' : 'problemas' }} detectados</p>
                </div>

                <div class="bg-white border border-{{ $analisis['salud_proyecto']['total_sobrecarga'] > 0 ? 'orange' : 'green' }}-200 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Recursos</span>
                        <svg class="w-6 h-6 text-{{ $analisis['salud_proyecto']['total_sobrecarga'] > 0 ? 'orange' : 'green' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $analisis['salud_proyecto']['total_sobrecarga'] }}</p>
                    <p class="text-sm font-medium text-gray-700 mt-1">{{ $analisis['salud_proyecto']['total_sobrecarga'] == 1 ? 'sobrecargado' : 'sobrecargados' }}</p>
                </div>
            </div>

            <!-- Desviaciones Detectadas -->
            @if($analisis['desviaciones']->isNotEmpty())
            <div class="bg-white border-2 border-red-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-red-100">
                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Desviaciones Detectadas</h2>
                        <p class="text-sm text-gray-600">Tareas con atraso o en riesgo que requieren atención</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($analisis['desviaciones']->take(5) as $desv)
                    <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-{{ $desv['tipo'] == 'atraso' ? 'red' : 'yellow' }}-200 bg-{{ $desv['tipo'] == 'atraso' ? 'red' : 'yellow' }}-50">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-{{ $desv['severidad'] == 'critica' ? 'red' : ($desv['severidad'] == 'alta' ? 'orange' : ($desv['severidad'] == 'media' ? 'yellow' : 'blue')) }}-600 shadow-md">
                            @if($desv['tipo'] == 'atraso')
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-bold text-gray-900">{{ $desv['tarea']->nombre }}</h4>
                                @if($desv['en_ruta_critica'])
                                <span class="px-2 py-0.5 bg-red-600 text-white text-xs font-bold rounded-full">RUTA CRÍTICA</span>
                                @endif
                                <span class="px-2 py-0.5 bg-gray-700 text-white text-xs font-semibold rounded-full uppercase">{{ $desv['severidad'] }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $desv['tarea']->fase->nombre_fase ?? 'Sin fase' }}</p>
                            @if($desv['tipo'] == 'atraso')
                            <p class="text-sm font-medium text-red-700 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                {{ round($desv['dias_atraso']) }} días de atraso - {{ $desv['impacto'] }}
                            </p>
                            @else
                            <p class="text-sm font-medium text-yellow-700 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ round($desv['probabilidad_atraso']) }}% de probabilidad de atraso - {{ abs(round($desv['dias_restantes'])) }} días restantes
                            </p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($desv['tarea']->responsableUsuario)
                            <p class="text-xs font-semibold text-gray-700">Responsable:</p>
                            <p class="text-sm font-bold text-gray-900">{{ $desv['tarea']->responsableUsuario->nombre }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Ajustes Pendientes -->
            @if($ajustesPendientes->isNotEmpty())
            <div class="bg-white border-2 border-amber-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Ajustes Pendientes de Aprobación</h2>
                            <p class="text-sm text-gray-600">Requieren revisión y aprobación del líder del proyecto</p>
                        </div>
                    </div>
                    <span class="text-3xl font-bold text-amber-600">{{ $ajustesPendientes->count() }}</span>
                </div>

                <div class="space-y-3">
                    @foreach($ajustesPendientes as $ajuste)
                    <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-amber-200 bg-amber-50 hover:shadow-md transition">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-3 py-1 bg-amber-600 text-white text-xs font-bold rounded-full uppercase">{{ $ajuste->estrategia }}</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">Score: {{ $ajuste->score_solucion }}/100</span>
                            </div>
                            <p class="text-sm text-gray-700 font-medium">{{ $ajuste->motivo_ajuste }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $ajuste->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                    Recupera {{ $ajuste->dias_recuperados }} días
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    Afecta {{ $ajuste->recursos_afectados }} {{ $ajuste->recursos_afectados == 1 ? 'recurso' : 'recursos' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('proyectos.cronograma.ver-ajuste', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition">
                                Revisar →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Ajustes Recientes -->
            @if($ajustesRecientes->isNotEmpty())
            <div class="bg-white border-2 border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-gray-200">
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Historial Reciente</h2>
                        <p class="text-sm text-gray-600">Últimos ajustes procesados</p>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach($ajustesRecientes as $ajuste)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-{{ $ajuste->estado == 'aplicado' ? 'green' : ($ajuste->estado == 'rechazado' ? 'red' : 'blue') }}-600"></span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 capitalize">{{ $ajuste->estrategia }} - {{ $ajuste->estado }}</p>
                                <p class="text-xs text-gray-600">{{ $ajuste->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('proyectos.cronograma.ver-ajuste', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Ver →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formGenerarAjuste');
            const btn = document.getElementById('btnGenerarAjuste');
            const btnText = document.getElementById('btnText');
            
            console.log('Dashboard loaded:', {form, btn, btnText});
            console.log('Form action:', form?.action);
            console.log('Form method:', form?.method);
            
            if (form && btn) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submit triggered!', e);
                    console.log('Form data:', new FormData(form));
                    
                    // Deshabilitar botón y mostrar loading
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btnText.textContent = 'Generando ajuste...';
                    
                    // Mostrar spinner
                    const spinner = document.createElement('svg');
                    spinner.classList.add('animate-spin', 'h-5', 'w-5', 'text-white');
                    spinner.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
                    btn.querySelector('svg').replaceWith(spinner);
                    
                    console.log('Loading state applied');
                });
            } else {
                console.error('Form elements not found!');
            }
        });
    </script>
    @endpush
</x-app-layout>
