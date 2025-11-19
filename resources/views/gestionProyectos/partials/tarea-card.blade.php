<div class="bg-white border-2 border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-all cursor-move tarea-card"
     data-tarea-id="{{ $tarea->id_tarea }}"
     draggable="true">
    
    <!-- Header de la tarjeta -->
    <div class="flex items-start justify-between mb-3">
        <h4 class="font-semibold text-gray-900 text-sm flex-1 pr-2">{{ $tarea->nombre }}</h4>
        
        @if($tarea->prioridad >= 8)
            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded flex-shrink-0">
                🔥 Alta
            </span>
        @elseif($tarea->prioridad >= 5)
            <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded flex-shrink-0">
                ⚠️ Media
            </span>
        @endif
    </div>

    <!-- Descripción (truncada) -->
    @if($tarea->descripcion)
        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $tarea->descripcion }}</p>
    @endif

    <!-- Información adicional -->
    <div class="space-y-2 text-xs text-gray-600 mb-3">
        @if(isset($tarea->fase))
            <div class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="font-medium">{{ $tarea->fase->nombre_fase ?? 'Sin fase' }}</span>
            </div>
        @endif

        @if($tarea->fecha_fin)
            <div class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ \Carbon\Carbon::parse($tarea->fecha_fin)->format('d/m/Y') }}</span>
            </div>
        @endif

        @if($tarea->horas_estimadas)
            <div class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $tarea->horas_estimadas }}h</span>
            </div>
        @endif
    </div>

    <!-- Footer: botón ver detalles -->
    <div class="pt-3 border-t border-gray-200">
        <a href="{{ route('proyectos.tareas.edit', [$proyecto, $tarea]) }}" 
           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver detalles
        </a>
    </div>
</div>
