<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    CRONOGRAMA MAESTRO
                </h2>
                <p class="text-sm text-gray-600 mt-1 font-medium">
                    {{ $proyecto->nombre }} • Plan detallado del proyecto
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cascada.dashboard', $proyecto) }}" class="btn btn-ghost btn-sm">
                    ← Volver al Dashboard
                </a>
                <button class="btn btn-sm btn-outline">
                    Exportar PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Navegación Jerárquica -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg shadow-sm border border-blue-200 mb-6">
                <div class="px-6 py-4">
                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                        <a href="{{ route('cascada.dashboard', $proyecto) }}" class="font-semibold text-purple-700 hover:text-purple-800">NIVEL 1: Gestión Cascada</a>
                        <span class="text-gray-400">→</span>
                        <span class="font-semibold text-blue-700">NIVEL 2: Cronograma Maestro</span>
                        <span class="text-gray-400">→</span>
                        <a href="{{ route('proyectos.tareas.index', [$proyecto, 'vista' => 'gantt']) }}" class="font-semibold text-green-700 hover:text-green-800">NIVEL 3: Diagrama Gantt</a>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('cascada.dashboard', $proyecto) }}"
                           class="px-4 py-2 bg-white text-gray-700 hover:bg-purple-50 rounded-lg font-medium border-2 border-gray-300 hover:border-purple-300 transition-colors">
                            GESTIÓN CASCADA
                        </a>
                        <a href="{{ route('cascada.cronograma-maestro', $proyecto) }}"
                           class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold border-2 border-blue-300">
                            CRONOGRAMA MAESTRO
                        </a>
                        <a href="{{ route('proyectos.tareas.index', [$proyecto, 'vista' => 'gantt']) }}"
                           class="px-4 py-2 bg-white text-gray-700 hover:bg-green-50 rounded-lg font-medium border-2 border-gray-300 hover:border-green-300 transition-colors">
                            DIAGRAMA GANTT
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del cronograma -->
            <div class="bg-white rounded-lg shadow-sm border mb-6 p-6">
                                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">CRONOGRAMA DEL PROYECTO</h3>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span class="font-semibold">Inicio: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</span>
                        <span class="font-semibold">Fin: {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
                        <span class="font-semibold">Duración: {{ $rangoFechas }} días</span>
                    </div>
                </div>
            </div>

            <!-- Lista de Tareas por Fases -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">ACTIVIDADES DEL CRONOGRAMA</h3>

                    @if($tareas->count() > 0)
                        <div class="space-y-6">
                            @foreach($fases as $fase)
                                @php
                                    $tareasDelaFase = $tareas->where('id_fase', $fase->id_fase);
                                @endphp

                                @if($tareasDelaFase->count() > 0)
                                    <!-- Fase -->
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                            <h4 class="font-bold text-gray-900">{{ $fase->nombre_fase }}</h4>
                                            <p class="text-sm text-gray-600 font-medium">{{ $tareasDelaFase->count() }} actividades</p>
                                        </div>

                                        <div class="divide-y divide-gray-100">
                                            @foreach($tareasDelaFase as $tarea)
                                                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            @php
                                                                $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
                                                            @endphp
                                                            @if(in_array($tarea->estado, $estadosCompletados))
                                                                <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            @else
                                                                <div class="w-5 h-5 bg-gray-300 rounded-full"></div>
                                                            @endif
                                                            <div>
                                                                <p class="font-medium text-gray-900">{{ $tarea->nombre }}</p>
                                                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                                                    @if($tarea->responsableUsuario)
                                                                        <span>Responsable: {{ $tarea->responsableUsuario->nombre }}</span>
                                                                    @endif
                                                                    @if($tarea->fecha_inicio && $tarea->fecha_fin)
                                                                        <span>Del {{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d/m/Y') }}</span>
                                                                    @endif
                                                                    @if($tarea->horas_estimadas)
                                                                        <span>{{ $tarea->horas_estimadas }} horas</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($tarea->prioridad)
                                                            @php
                                                                $prioridadColor = $tarea->prioridad >= 8 ? 'bg-red-100 text-red-800' :
                                                                                 ($tarea->prioridad >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                                            @endphp
                                                            <span class="badge {{ $prioridadColor }} border-0">
                                                                Prioridad {{ $tarea->prioridad }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="text-6xl mb-4">📋</div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay actividades programadas</h3>
                            <p class="text-gray-600 mb-4">Agrega actividades con fechas para visualizar el cronograma</p>
                            <a href="{{ route('cascada.dashboard', $proyecto) }}" class="btn bg-purple-600 text-white hover:bg-purple-700">
                                Agregar Actividades
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Botón para Diagrama de Gantt -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg shadow-sm border border-green-200 mt-6 p-6">
                <div class="text-center">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">VISUALIZACIÓN GRÁFICA COMPLETA</h3>
                    <p class="text-gray-700 mb-4 font-medium">Accede al Diagrama de Gantt interactivo para ver dependencias, progreso visual y gestión detallada del tiempo</p>
                    <a href="{{ route('proyectos.tareas.index', [$proyecto, 'vista' => 'gantt']) }}"
                       class="inline-flex items-center gap-3 btn btn-lg bg-gradient-to-r from-green-500 to-blue-600 text-white border-0 hover:shadow-lg transition-all duration-300 transform hover:scale-105 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        VER DIAGRAMA DE GANTT COMPLETO
                    </a>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="bg-white rounded-lg shadow-sm border mt-6 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">LEYENDA</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-purple-500 rounded"></div>
                        <span class="text-sm text-gray-700">Actividad en progreso</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-sm text-gray-700">Actividad completada</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span class="text-sm text-gray-700">Línea de tiempo actual</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-gray-300 rounded"></div>
                        <span class="text-sm text-gray-700">Actividad pendiente</span>
                    </div>
                </div>
            </div>

            <!-- Resumen de métricas -->
            <div class="bg-white rounded-lg shadow-sm border mt-6 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">MÉTRICAS DEL CRONOGRAMA</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $tareas->count() }}</div>
                        <p class="text-sm text-gray-600">Total Actividades</p>
                    </div>
                    <div class="text-center">
                        @php
                            $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
                        @endphp
                        <div class="text-2xl font-bold text-green-600">{{ $tareas->whereIn('estado', $estadosCompletados)->count() }}</div>
                        <p class="text-sm text-gray-600">Completadas</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $tareas->sum('horas_estimadas') }}</div>
                        <p class="text-sm text-gray-600">Horas Totales</p>
                    </div>
                    <div class="text-center">
                        @php
                            $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
                            $progresoTotal = $tareas->count() > 0 ? round(($tareas->whereIn('estado', $estadosCompletados)->count() / $tareas->count()) * 100) : 0;
                        @endphp
                        <div class="text-2xl font-bold text-yellow-600">{{ $progresoTotal }}%</div>
                        <p class="text-sm text-gray-600">Progreso</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</x-app-layout>
