@props(['tareas', 'fechaInicio', 'fechaFin', 'rangoFechas'])

<div class="space-y-3" style="max-height: 600px; overflow-y: auto;">
    @if($tareas->count() > 0)
        @foreach($tareas as $tarea)
            @php
                $inicioTarea = \Carbon\Carbon::parse($tarea->fecha_inicio);
                $finTarea = \Carbon\Carbon::parse($tarea->fecha_fin);
                $duracionTarea = $inicioTarea->diffInDays($finTarea) + 1;
                $diasDesdeInicio = \Carbon\Carbon::parse($fechaInicio)->diffInDays($inicioTarea);
                $porcentajeInicio = $rangoFechas > 0 ? ($diasDesdeInicio / $rangoFechas) * 100 : 0;
                $porcentajeDuracion = $rangoFechas > 0 ? ($duracionTarea / $rangoFechas) * 100 : 0;
            @endphp

            <div class="flex items-center gap-4 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                <!-- Información de la tarea -->
                <div class="w-64 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        @if($tarea->estado === 'Completado')
                            <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs">✓</span>
                            </div>
                        @else
                            <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-900 truncate" style="max-width: 200px;">
                                {{ $tarea->nombre }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                @if($tarea->responsableUsuario)
                                    <span>👤 {{ $tarea->responsableUsuario->nombre }}</span>
                                @endif
                                @if($tarea->horas_estimadas)
                                    <span>⏱️ {{ $tarea->horas_estimadas }}h</span>
                                @endif
                                @if($tarea->fase)
                                    <span>📋 {{ $tarea->fase->nombre_fase }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barra de Gantt -->
                <div class="flex-1 bg-gray-100 rounded-full h-8 relative min-w-0">
                    <div class="absolute h-full rounded-full flex items-center justify-center text-xs text-white font-medium shadow-sm {{ $tarea->estado === 'Completado' ? 'bg-green-500' : 'bg-purple-500' }}"
                         style="left: {{ $porcentajeInicio }}%; width: {{ max(3, $porcentajeDuracion) }}%;"
                         title="{{ $tarea->nombre }} ({{ $duracionTarea }} días)">
                        @if($porcentajeDuracion > 8)
                            {{ $duracionTarea }}d
                        @endif
                    </div>

                    <!-- Fechas -->
                    <div class="absolute -bottom-6 text-xs text-gray-500 z-10"
                         style="left: {{ $porcentajeInicio }}%;">
                        {{ $inicioTarea->format('d/m') }}
                    </div>
                    <div class="absolute -bottom-6 text-xs text-gray-500 z-10"
                         style="left: {{ $porcentajeInicio + $porcentajeDuracion }}%;">
                        {{ $finTarea->format('d/m') }}
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="w-20 flex-shrink-0 text-right">
                    @if($tarea->prioridad)
                        @php
                            $prioridadColor = $tarea->prioridad >= 8 ? 'bg-red-100 text-red-800' :
                                             ($tarea->prioridad >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        @endphp
                        <span class="badge badge-xs {{ $prioridadColor }}">
                            P{{ $tarea->prioridad }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Línea de tiempo actual -->
        @php
            $hoy = now();
            if ($hoy->between(\Carbon\Carbon::parse($fechaInicio), \Carbon\Carbon::parse($fechaFin))) {
                $diasDesdeInicio = \Carbon\Carbon::parse($fechaInicio)->diffInDays($hoy);
                $porcentajeHoy = ($diasDesdeInicio / $rangoFechas) * 100;
            } else {
                $porcentajeHoy = null;
            }
        @endphp

        @if($porcentajeHoy !== null)
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute bg-red-500 w-0.5 opacity-75 z-20"
                     style="left: calc(256px + {{ $porcentajeHoy }}%); top: 0; bottom: 0;">
                    <div class="absolute -top-6 -left-8 bg-red-500 text-white text-xs px-2 py-1 rounded">
                        HOY
                    </div>
                </div>
            </div>
        @endif

    @else
        <div class="text-center py-12 text-gray-500">
            <div class="text-6xl mb-4">📅</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay actividades programadas</h3>
            <p class="text-gray-600">Agrega actividades con fechas para visualizar el cronograma</p>
        </div>
    @endif
</div>

<style>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Mejorar la visualización del Gantt en pantallas pequeñas */
@media (max-width: 768px) {
    .w-64 {
        width: 200px;
    }
}

/* Animación suave para las barras de Gantt */
.gantt-bar {
    transition: all 0.3s ease;
}

.gantt-bar:hover {
    transform: scaleY(1.1);
}
</style>
