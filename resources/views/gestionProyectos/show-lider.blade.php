<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-4 flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('proyectos.index') }}" class="hover:text-gray-900">Proyectos</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ $proyecto->nombre }}</span>
            </div>

            <!-- Header del Proyecto -->
            <div class="card bg-white shadow-sm mb-6">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            @include('partials.avatar-proyecto', ['proyecto' => $proyecto])
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $proyecto->nombre }}</h1>
                                <p class="text-sm text-gray-600 mt-1">{{ $proyecto->codigo }} • {{ $proyecto->metodologia->nombre ?? 'Sin metodología' }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs text-gray-600">
                                        Creado por: <span class="font-medium">{{ $proyecto->creador->nombre_completo }}</span>
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        {{ $proyecto->creado_en->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($esLider)
                                <span class="badge bg-blue-100 text-blue-800 border-0">Líder del Equipo</span>
                            @endif
                            <span class="badge bg-green-100 text-green-800 border-0">Activo</span>
                        </div>
                    </div>

                    @if($proyecto->descripcion)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-700">{{ $proyecto->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabs de navegación según metodología (LÍDER tiene acceso completo) -->
            <div class="mb-6">
                <div class="card bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-3 py-2">
                        @php
                            $metodologia = strtolower($proyecto->metodologia->nombre ?? '');
                            $tabActual = request()->get('tab', 'dashboard');
                        @endphp

                        <nav class="project-tabs grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">

                            <!-- Tab Dashboard (siempre primero) -->
                            <a href="{{ route('proyectos.show', $proyecto) }}?tab=dashboard" class="{{ $tabActual === 'dashboard' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Dashboard
                            </a>

                            @if($metodologia === 'cascada')
                                <!-- Tab Gestión Cascada (solo para proyectos Cascada) -->
                                <a href="{{ route('cascada.dashboard', $proyecto) }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Gestión Cascada
                                    </a>
                            @endif

                                <a href="{{ route('proyectos.miembros.index', $proyecto) }}" class="{{ $tabActual === 'miembros' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                Miembros
                            </a>

                            @if($metodologia === 'scrum')
                                <!-- Navegación específica para Scrum -->
                                <a href="{{ route('scrum.dashboard', $proyecto) }}" class="{{ $tabActual === 'scrum' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Gestión Scrum
                                    </a>
                            @else
                                <!-- Navegación genérica -->
                                    <a href="{{ route('proyectos.tareas.index', $proyecto) }}?vista=gantt" class="{{ $tabActual === 'tareas' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Cronograma
                                </a>
                            @endif

                            <!-- Navegación común para todos -->
                            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="{{ $tabActual === 'elementos' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Elementos Config.
                            </a>
                            <a href="{{ route('proyectos.trazabilidad', $proyecto) }}" class="{{ $tabActual === 'trazabilidad' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Trazabilidad
                            </a>
                            <a href="{{ route('proyectos.solicitudes.index', $proyecto) }}" class="{{ $tabActual === 'solicitudes' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Solicitudes Cambio
                            </a>
                            <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}" class="{{ $tabActual === 'ccb' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Dashboard CCB
                            </a>
                            <a href="{{ route('proyectos.cronograma.dashboard', $proyecto) }}" class="{{ $tabActual === 'cronograma' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Cronograma Inteligente
                            </a>
                            <a href="{{ route('proyectos.informes.dashboard', $proyecto) }}" class="{{ $tabActual === 'informes' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Informes
                            </a>
                            <a href="{{ route('proyectos.liberaciones.index', $proyecto) }}" class="{{ $tabActual === 'liberaciones' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Liberaciones
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <style>
                /* Tabs grid: make each tab a full-width cell and style icon/text spacing */
                .project-tabs a { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border-radius: 0.375rem; width: 100%; justify-content: flex-start; }
                .project-tabs a svg { flex: 0 0 auto; }
                .project-tabs a .tab-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            </style>

            <!-- Contenido: Dashboard del LÍDER según tab activo -->
            @php
                $tabActual = request()->get('tab', 'dashboard');
            @endphp

            @if($tabActual === 'dashboard')
                <!-- ===== TAB: DASHBOARD ===== -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Dashboard</h2>
                    <p class="text-sm text-gray-600">Resumen general del proyecto</p>
                </div>

                <!-- Métricas Generales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Equipos</span>
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $proyecto->equipos->count() }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Miembros</span>
                        <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $proyecto->equipos->sum(function($equipo) { return $equipo->miembros->count(); }) }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Elementos</span>
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $elementosConfiguracion ? $elementosConfiguracion->count() : 0 }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cambios</span>
                        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $solicitudesCambio ? $solicitudesCambio->count() : 0 }}</p>
                </div>
            </div>

            @if($metodologia === 'scrum')
                <!-- Métricas Scrum adicionales (estilo consistente con tarjetas superiores) -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Sprint Activo -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sprint Activo</span>
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $sprintActivo ? $sprintActivo->nombre : 'Sin sprint activo' }}</p>
                    </div>

                    <!-- Story Points -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Story Points</span>
                            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $storyPointsCompletados ?? 0 }}/{{ $storyPointsTotal ?? 0 }}</p>
                    </div>

                    <!-- Equipos Scrum -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Equipos Dev</span>
                            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $proyecto->equipos->count() }}</p>
                    </div>

                    <!-- Impedimentos -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Impedimentos</span>
                            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            @endif

            @if($metodologia === 'cascada' && isset($fases))
                <!-- Tarjetas de Cascada en el Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fase Actual</span>
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ $faseActual->nombre_fase ?? 'Sin definir' }}</p>
                </div>                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Progreso</span>
                        @php
                            // Calcular progreso promedio de todas las fases (igual que en Cascada)
                            $totalFases = $fases->count();
                            if ($totalFases > 0) {
                                $sumaPorcentajes = 0;
                                foreach ($progresoPorFase as $progreso) {
                                    $sumaPorcentajes += $progreso['porcentaje'];
                                }
                                $progresoGeneral = round($sumaPorcentajes / $totalFases);
                            } else {
                                $progresoGeneral = 0;
                            }
                        @endphp
                        <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $progresoGeneral }}%</p>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progresoGeneral }}%"></div>
                        </div>
                    </div>
                </div>                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Duración Total</span>
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $duracionTotal }} días</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Tareas</span>
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $tareas->count() }}</p>
                    </div>
                </div>
            @endif

            <!-- Acciones Rápidas -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-5">Acciones Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('proyectos.elementos.create', $proyecto) }}" title="Crear Elemento de Configuración" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-800 hover:bg-gray-50 transition-colors font-medium justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Crear Elemento
                        </a>

                        <a href="{{ route('proyectos.solicitudes.create', $proyecto) }}" title="Nueva Solicitud de Cambio" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-800 hover:bg-gray-50 transition-colors font-medium justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Nueva Solicitud
                        </a>

                        <a href="{{ route('proyectos.liberaciones.index', $proyecto) }}" title="Ver Liberaciones" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-800 hover:bg-gray-50 transition-colors font-medium justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Ver Liberaciones
                        </a>
                    </div>
            </div>

            <!-- Equipos del Proyecto (removed per UX request) -->

            @elseif($tabActual === 'cascada' && $metodologia === 'cascada')
                <!-- ===== REDIRECCIÓN: Gestión Cascada está en su propio módulo ===== -->
                <script>window.location.href = "{{ route('cascada.dashboard', $proyecto) }}";</script>

            @endif
            <!-- Fin de tabs condicionales -->

            @if($tabActual === 'dashboard')
            <!-- Lista de Equipos (solo en Dashboard) -->
            <div class="card bg-white shadow-sm mt-6">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Equipos del Proyecto</h2>
                        <a href="{{ route('proyectos.miembros.index', $proyecto) }}" class="btn btn-sm bg-gradient-to-r from-indigo-500 to-purple-600 text-white border-0 hover:shadow-lg transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Gestionar Miembros
                        </a>
                    </div>

                    <div class="space-y-4">
                        @foreach($proyecto->equipos as $equipo)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $equipo->nombre }}</h3>
                                        <p class="text-sm text-gray-600">{{ $equipo->miembros->count() }} miembros</p>
                                    </div>
                                    @if($equipo->lider)
                                        <span class="badge bg-orange-100 text-orange-800 border-0">
                                            Líder: {{ $equipo->lider->nombre_completo }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Miembros del equipo -->
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($equipo->miembros as $miembro)
                                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-full">
                                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-gray-700">{{ substr($miembro->nombre_completo, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $miembro->nombre_completo }}</span>
                                            <span class="text-xs text-gray-500">• {{ $miembro->rol_proyecto->nombre ?? 'Sin rol' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <script>
        // Set progress bar widths
        document.querySelectorAll('.progress-bar').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            bar.style.width = width + '%';
        });
    </script>

    <style>
        .project-tabs a svg {
            flex-shrink: 0;
        }
    </style>
</x-app-layout>
