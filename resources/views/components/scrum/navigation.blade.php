@props(['proyecto', 'active' => 'dashboard'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Gestión Scrum
            </h3>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Proyecto
            </a>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('scrum.dashboard', $proyecto) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ $active === 'dashboard' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Sprint Board
                </span>
            </a>

            <a href="{{ route('scrum.sprint-planning', $proyecto) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ $active === 'planning' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Sprint Planning
                </span>
            </a>

            <a href="{{ route('scrum.daily-scrum', $proyecto) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ $active === 'daily' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Daily Scrum
                </span>
            </a>

            <a href="{{ route('scrum.sprint-review', $proyecto) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ $active === 'review' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sprint Review
                </span>
            </a>

            <a href="{{ route('scrum.sprint-retrospective', $proyecto) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ $active === 'retrospective' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Retrospective
                </span>
            </a>
        </div>
    </div>
</div>
