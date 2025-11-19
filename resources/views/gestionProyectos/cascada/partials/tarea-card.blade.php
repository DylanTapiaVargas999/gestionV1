{{-- Tarjeta de tarea para drag and drop --}}
<div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow cursor-move"
     draggable="true"
     ondragstart="drag(event)"
     data-tarea-id="{{ $tarea->id_tarea }}">

    <div class="flex items-start justify-between gap-2 mb-2">
        <h4 class="font-semibold text-gray-900 text-sm flex-1">{{ $tarea->nombre }}</h4>
        <div class="flex items-center gap-2">
            <a href="{{ route('proyectos.tareas.edit', [$tarea->id_proyecto ?? $tarea->id_proyecto, $tarea->id_tarea]) }}" class="text-xs text-gray-500 hover:text-gray-700" title="Editar tarea" onclick="event.stopPropagation();">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
        </div>
        @if($tarea->prioridad)
            @php
                $colorPrioridad = $tarea->prioridad >= 8 ? 'bg-red-100 text-red-700' : ($tarea->prioridad >= 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700');
            @endphp
            <span class="px-2 py-0.5 {{ $colorPrioridad }} rounded-full font-medium text-[10px]">
                P{{ $tarea->prioridad }}
            </span>
        @endif
    </div>

    @if($tarea->elementoConfiguracion)
        <div class="mb-2">
            <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs rounded-md flex items-center gap-1 w-fit">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                {{ $tarea->elementoConfiguracion->codigo_ec }}
            </span>
        </div>
    @endif

    @if($tarea->descripcion)
        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $tarea->descripcion }}</p>
    @endif

    <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
        <span class="flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            {{ $tarea->horas_estimadas ?? 0 }}h
        </span>
        @if($tarea->fecha_inicio && $tarea->fecha_fin)
            <span class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m') }}
            </span>
        @endif
    </div>

    @if($tarea->responsableUsuario)
        <div class="flex items-center gap-2 text-xs text-gray-600 pt-2 border-t border-gray-100">
            <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center font-medium text-white text-[10px]">
                {{ substr($tarea->responsableUsuario->name, 0, 1) }}
            </div>
            <span class="truncate">{{ $tarea->responsableUsuario->name }}</span>
        </div>
    @else
        <div class="flex items-center gap-2 text-xs text-amber-600 pt-2 border-t border-amber-100 bg-amber-50 -mx-3 -mb-3 px-3 py-2 rounded-b-lg">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">Sin asignar</span>
        </div>
    @endif
</div>
