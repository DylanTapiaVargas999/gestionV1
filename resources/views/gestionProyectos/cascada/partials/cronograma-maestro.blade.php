{{-- Cronograma Maestro: Lista de todas las actividades por fase --}}
<div class="bg-white rounded-lg border border-gray-200 mb-6">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">CRONOGRAMA MAESTRO</h3>

        @if($tareas->count() > 0)
            <div class="space-y-4">
                @foreach($fases as $fase)
                    @php
                        $tareasDelaFase = $tareas->where('id_fase', $fase->id_fase);
                    @endphp

                    @if($tareasDelaFase->count() > 0)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            {{-- Encabezado de la fase --}}
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-gray-900">{{ $fase->nombre_fase }}</h4>
                                    <span class="text-sm text-gray-600 font-medium">{{ $tareasDelaFase->count() }} actividades</span>
                                </div>
                            </div>

                            {{-- Lista de actividades --}}
                            <div class="divide-y divide-gray-100">
                                @foreach($tareasDelaFase as $tarea)
                                    <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3 flex-1">
                                                {{-- Estado --}}
                                                <div class="mt-1">
                                                    @if(in_array($tarea->estado, ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada']))
                                                        <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-5 h-5 bg-gray-200 rounded-full"></div>
                                                    @endif
                                                </div>

                                                {{-- Información de la tarea --}}
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900">{{ $tarea->nombre }}</p>
                                                    <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600">
                                                        {{-- Elemento de Configuración --}}
                                                        @if($tarea->elementoConfiguracion)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                                {{ $tarea->elementoConfiguracion->codigo_ec }}
                                                            </span>
                                                        @endif

                                                        {{-- Responsable --}}
                                                        @if($tarea->responsableUsuario)
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                                {{ $tarea->responsableUsuario->nombre }}
                                                            </span>
                                                        @endif

                                                        {{-- Fechas --}}
                                                        @if($tarea->fecha_inicio && $tarea->fecha_fin)
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                                {{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d/m/Y') }}
                                                            </span>
                                                        @endif

                                                        {{-- Horas --}}
                                                        @if($tarea->horas_estimadas)
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                {{ $tarea->horas_estimadas }}h
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Prioridad --}}
                                            @if($tarea->prioridad)
                                                @php
                                                    $prioridadColor = [
                                                        10 => 'text-red-600', 9 => 'text-red-600', 8 => 'text-orange-600',
                                                        7 => 'text-yellow-600', 6 => 'text-yellow-600', 5 => 'text-green-600',
                                                        4 => 'text-green-600', 3 => 'text-green-600', 2 => 'text-gray-600', 1 => 'text-gray-600'
                                                    ];
                                                    $prioridadTexto = [
                                                        10 => 'P10', 9 => 'P9', 8 => 'P8',
                                                        7 => 'P7', 6 => 'P6', 5 => 'P5',
                                                        4 => 'P4', 3 => 'P3', 2 => 'P2', 1 => 'P1'
                                                    ];
                                                @endphp
                                                <span class="text-xs font-bold {{ $prioridadColor[$tarea->prioridad] ?? 'text-gray-600' }}">
                                                    {{ $prioridadTexto[$tarea->prioridad] ?? 'P5' }}
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
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay actividades</h3>
                <p class="text-gray-600 mb-4">Crea la primera actividad para comenzar</p>
                <button onclick="modalNuevaTarea.showModal()" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                    + Nueva Actividad
                </button>
            </div>
        @endif
    </div>
</div>
