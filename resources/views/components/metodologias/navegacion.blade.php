@props(['proyecto', 'activo' => 'dashboard'])

@php
    $metodologia = strtolower($proyecto->metodologia->nombre ?? '');
@endphp

<div class="bg-white rounded-lg shadow-sm border mb-6">
    <div class="px-6 py-4">
        <div class="flex flex-wrap gap-3">
            @if($metodologia === 'scrum')
                <!-- Navegación Scrum -->
                <a href="{{ route('scrum.dashboard', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'dashboard' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Sprint Board
                </a>
                <a href="{{ route('scrum.sprint-planning', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'planning' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Sprint Planning
                </a>
                <a href="{{ route('scrum.daily-scrum', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'daily' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Daily Scrum
                </a>
                <a href="{{ route('scrum.sprint-review', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'review' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Sprint Review
                </a>
                <a href="{{ route('scrum.sprint-retrospective', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'retrospective' ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Retrospective
                </a>

            @elseif($metodologia === 'cascada')
                <!-- Navegación Cascada -->
                <a href="{{ route('cascada.dashboard', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'dashboard' ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('cascada.cronograma-maestro', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'cronograma' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Cronograma Maestro
                </a>
                <a href="{{ route('cascada.hitos', $proyecto) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'hitos' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Hitos
                </a>

            @else
                <!-- Navegación genérica -->
                <a href="{{ route('proyectos.tareas.index', $proyecto) }}?vista=kanban"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'kanban' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Tablero
                </a>
                <a href="{{ route('proyectos.tareas.index', $proyecto) }}?vista=gantt"
                   class="px-4 py-2 rounded-lg font-medium {{ $activo === 'gantt' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    Cronograma
                </a>
            @endif
        </div>
    </div>
</div>
