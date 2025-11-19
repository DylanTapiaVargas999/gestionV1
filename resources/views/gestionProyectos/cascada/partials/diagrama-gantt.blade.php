{{-- Diagrama de Gantt: Visualización temporal de actividades --}}
<div class="bg-white rounded-lg border border-gray-200 mb-6">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">DIAGRAMA DE GANTT</h3>

        @if($tareas->count() > 0 && $fechaInicioProyecto && $fechaFinProyecto)
            {{-- Información del proyecto --}}
            <div class="mb-4 grid grid-cols-3 gap-3">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700 font-medium mb-1">INICIO</p>
                    <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($fechaInicioProyecto)->format('d/m/Y') }}</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-xs text-green-700 font-medium mb-1">FIN</p>
                    <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($fechaFinProyecto)->format('d/m/Y') }}</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                    <p class="text-xs text-purple-700 font-medium mb-1">DURACIÓN</p>
                    <p class="text-lg font-bold text-gray-900">{{ $duracionTotal }} días</p>
                </div>
            </div>

            {{-- Diagrama Gantt --}}
            <div class="overflow-x-auto bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="min-w-[800px]">
                    <div class="space-y-2">
                        @foreach($tareas as $tarea)
                            @php
                                $inicioTarea = \Carbon\Carbon::parse($tarea->fecha_inicio);
                                $finTarea = \Carbon\Carbon::parse($tarea->fecha_fin);
                                $duracionTarea = $inicioTarea->diffInDays($finTarea) + 1;
                                $diasDesdeInicio = \Carbon\Carbon::parse($fechaInicioProyecto)->diffInDays($inicioTarea);
                                $porcentajeInicio = min(100, ($diasDesdeInicio / max(1, $duracionTotal)) * 100);
                                $porcentajeDuracion = min(100, ($duracionTarea / max(1, $duracionTotal)) * 100);
                                $estadoCompletado = in_array($tarea->estado, ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada']);
                            @endphp

                            <div class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                                <div class="flex items-center gap-3">
                                    {{-- Información de la tarea --}}
                                    <div class="w-48 flex-shrink-0">
                                        <div class="flex items-center gap-2">
                                            {{-- Estado --}}
                                            @if($estadoCompletado)
                                                <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-5 h-5 bg-gray-300 rounded-full"></div>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 truncate" title="{{ $tarea->nombre }}">{{ $tarea->nombre }}</p>
                                                <p class="text-xs text-gray-600 truncate">{{ $tarea->fase->nombre_fase }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Barra temporal --}}
                                    <div class="flex-1 relative h-10 bg-gradient-to-r from-gray-100 to-gray-50 rounded-lg border border-gray-200">
                                        <div class="absolute top-1/2 transform -translate-y-1/2 h-6 {{ $estadoCompletado ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-blue-500 to-blue-600' }} rounded-md text-xs text-white font-bold flex items-center justify-center px-2 shadow-md"
                                             style="width: {{ max(8, $porcentajeDuracion) }}%; left: {{ $porcentajeInicio }}%">
                                            {{ $duracionTarea }}d
                                        </div>
                                    </div>

                                    {{-- Información adicional --}}
                                    <div class="w-32 text-right flex-shrink-0">
                                        @if($tarea->responsableUsuario)
                                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $tarea->responsableUsuario->nombre }}</p>
                                        @endif
                                        <p class="text-xs text-gray-600">{{ $inicioTarea->format('d/m') }} - {{ $finTarea->format('d/m') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-blue-600 rounded"></div>
                        <span class="text-gray-700 font-medium">En Progreso</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-gradient-to-r from-green-500 to-green-600 rounded"></div>
                        <span class="text-gray-700 font-medium">Completada</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-gray-300 rounded"></div>
                        <span class="text-gray-700 font-medium">Pendiente</span>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay actividades con fechas</h3>
                <p class="text-gray-600 mb-4">Agrega fechas a las actividades para visualizar el diagrama de Gantt</p>
                <button onclick="modalNuevaTarea.showModal()" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                    + Nueva Actividad
                </button>
            </div>
        @endif
    </div>
</div>
