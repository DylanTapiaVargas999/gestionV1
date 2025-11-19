@props(['fases', 'tareas', 'dragEnabled' => true, 'proyecto'])

<div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 60vh;">
    @foreach($fases as $fase)
        <div class="kanban-column flex-shrink-0" style="width: 320px;" data-fase-id="{{ $fase->id_fase }}">
            <!-- Header de la columna -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-t-lg p-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-gray-800">{{ $fase->nombre_fase }}</h4>
                    @php
                        $tareasDelaFase = $tareas->get($fase->id_fase, collect());
                        $totalStoryPoints = $tareasDelaFase->sum('story_points');
                    @endphp
                    <div class="flex gap-2">
                        <span class="badge badge-sm bg-gray-200 text-gray-700">
                            {{ $tareasDelaFase->count() }}
                        </span>
                        @if($totalStoryPoints > 0)
                            <span class="badge badge-sm bg-blue-100 text-blue-700">
                                {{ $totalStoryPoints }} pts
                            </span>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-1">{{ $fase->descripcion }}</p>
            </div>

            <!-- Lista de tareas -->
            <div class="kanban-tasks bg-gray-50 border-l border-r border-b border-gray-200 rounded-b-lg p-3 space-y-3" style="min-height: 400px;">
                @forelse($tareasDelaFase as $tarea)
                    <div class="kanban-card bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow border-l-4 border-blue-400"
                         @if($dragEnabled) draggable="true" @endif
                         data-tarea-id="{{ $tarea->id_tarea }}">

                        <!-- Header de la tarjeta -->
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-900 text-sm leading-tight">
                                    {{ $tarea->nombre }}
                                </h5>
                            </div>
                            <div class="flex items-center gap-1 ml-2">
                                @if($tarea->story_points)
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                        {{ $tarea->story_points }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Descripción -->
                        @if($tarea->descripcion)
                            <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $tarea->descripcion }}</p>
                        @endif

                        <!-- Información del elemento de configuración -->
                        @if($tarea->elementoConfiguracion)
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    📦 {{ $tarea->elementoConfiguracion->codigo_ec }}
                                </span>
                            </div>
                        @endif

                        <!-- Footer de la tarjeta -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                @if($tarea->responsableUsuario)
                                    <div class="flex items-center gap-1">
                                        <div class="w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-700">
                                                {{ substr($tarea->responsableUsuario->nombre, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if($tarea->prioridad)
                                    @php
                                        $prioridadColor = $tarea->prioridad >= 8 ? 'bg-red-100 text-red-800' :
                                                         ($tarea->prioridad >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                    @endphp
                                    <span class="badge badge-xs {{ $prioridadColor }}">
                                        P{{ $tarea->prioridad }}
                                    </span>
                                @endif

                                @if($tarea->sprint)
                                    <span class="badge badge-ghost badge-xs">{{ $tarea->sprint }}</span>
                                @endif
                            </div>

                            <button onclick="editarTarea({{ $tarea->id_tarea }})" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">No hay tareas en esta columna</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>

@if($dragEnabled)
<script>
let tareaEnMovimiento = null;
let estadoDestino = null;

document.addEventListener('DOMContentLoaded', function() {
    // Hacer cards arrastrables
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.dataset.tareaId);
            this.style.opacity = '0.5';
        });

        card.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
        });
    });

    // Hacer columnas como drop zones
    document.querySelectorAll('.kanban-tasks').forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#e3f2fd';
        });

        zone.addEventListener('dragleave', function(e) {
            this.style.backgroundColor = '#f8fafc';
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#f8fafc';

            const tareaId = e.dataTransfer.getData('text/plain');
            const nuevaFase = this.closest('.kanban-column').dataset.faseId;

            actualizarFaseTarea(tareaId, nuevaFase);
        });
    });
});

function actualizarFaseTarea(tareaId, nuevaFase) {
    // AJAX call para actualizar la fase
    fetch(`{{ route('proyectos.tareas.cambiar-fase', ['proyecto' => $proyecto->id, 'tarea' => ':tareaId']) }}`.replace(':tareaId', tareaId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_fase: nuevaFase
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar la página o actualizar dinámicamente
            location.reload();
        } else if (data.requiere_commit) {
            // Si requiere commit, mostrar alerta y recargar
            alert('⚠️ Esta tarea requiere un enlace de commit para ser completada. Por favor edita la tarea y agrega el commit URL.');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Error al mover la tarea'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al mover la tarea');
    });
}

function editarTarea(tareaId) {
    // Redirigir a la página de edición o abrir modal
    window.location.href = `{{ route('proyectos.tareas.edit', ['proyecto' => $proyecto->id, 'tarea' => ':tareaId']) }}`.replace(':tareaId', tareaId);
}
</script>
@endif

<style>
.kanban-column {
    background: #f8fafc;
    border-radius: 12px;
}
.kanban-card {
    cursor: move;
    transition: transform 0.2s, box-shadow 0.2s;
}
.kanban-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
