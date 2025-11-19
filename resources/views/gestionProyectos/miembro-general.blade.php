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
                            <div class="avatar placeholder">
                                <div class="bg-gray-200 text-black rounded-xl w-16 h-16 flex items-center justify-center">
                                    <span class="text-xl font-bold">{{ strtoupper(substr($proyecto->codigo ?? $proyecto->nombre, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $proyecto->nombre }}</h1>
                                <p class="text-sm text-gray-600 mt-1">{{ $proyecto->codigo }} • {{ $proyecto->metodologia->nombre ?? 'Sin metodología' }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    @if($miEquipo)
                                        <span class="text-xs text-gray-600">
                                            Mi equipo: <span class="font-medium">{{ $miEquipo->nombre }}</span>
                                        </span>
                                    @endif
                                    @if($rolEnProyecto)
                                        <span class="text-xs text-gray-600">
                                            Mi rol: <span class="font-medium">{{ $rolEnProyecto->nombre }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($rolEnProyecto)
                                <span class="badge bg-blue-100 text-blue-800 border-0">{{ $rolEnProyecto->nombre }}</span>
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

            <!-- Tabs de navegación (card grid) -->
            <div class="mb-6">
                @php
                    $tabActual = request()->get('tab', 'dashboard');
                @endphp

                <div class="card bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-3 py-2">
                        <nav class="project-tabs grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2">

                            <!-- Tab Dashboard (siempre primero) -->
                            <a href="{{ route('proyectos.show', $proyecto) }}?tab=dashboard" class="{{ $tabActual === 'dashboard' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Dashboard
                            </a>

                            <!-- Tab Solicitudes Cambio -->
                            <a href="{{ route('proyectos.show', $proyecto) }}?tab=solicitudes" class="{{ $tabActual === 'solicitudes' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Solicitudes Cambio
                            </a>

                            <!-- Tab Metodología Personalizada -->
                            <a href="{{ route('proyectos.show', $proyecto) }}?tab=metodologia" class="{{ $tabActual === 'metodologia' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }} inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium">
                                @if($proyecto->metodologia && strtolower($proyecto->metodologia->nombre) === 'scrum')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Mi Sprint
                                @elseif($proyecto->metodologia && strtolower($proyecto->metodologia->nombre) === 'cascada')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Mi Fase
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Mi Metodología
                                @endif
                            </a>

                        </nav>
                    </div>
                </div>
            </div>

            <!-- Contenido según tab activo -->
            @if($tabActual === 'solicitudes')
                <!-- CONTENIDO TAB: SOLICITUDES DE CAMBIO -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Solicitudes de Cambio</h2>
                            <p class="text-sm text-gray-600 mt-1">Gestiona las solicitudes de cambio del proyecto</p>
                        </div>
                        <a href="{{ route('proyectos.solicitudes.create', $proyecto) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Solicitud
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Filtros rápidos --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Filtrar por Estado</h3>
                            <div class="flex flex-wrap gap-2">
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('TODAS')">
                                    Todas ({{ $solicitudesCambio->count() }})
                                </button>
                                <button class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('ABIERTA')">
                                    Abiertas ({{ $solicitudesCambio->where('estado', 'ABIERTA')->count() }})
                                </button>
                                <button class="px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('EN_REVISION')">
                                    En Revisión ({{ $solicitudesCambio->where('estado', 'EN_REVISION')->count() }})
                                </button>
                                <button class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('APROBADA')">
                                    Aprobadas ({{ $solicitudesCambio->where('estado', 'APROBADA')->count() }})
                                </button>
                                <button class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('RECHAZADA')">
                                    Rechazadas ({{ $solicitudesCambio->where('estado', 'RECHAZADA')->count() }})
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de solicitudes --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="table w-full">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Título</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Solicitante</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Prioridad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">EC Afectados</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Votos</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($solicitudesCambio as $solicitud)
                                            <tr data-estado="{{ $solicitud->estado }}" class="solicitud-row hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition border-b border-gray-100">
                                                <td class="py-4">
                                                    <div class="font-semibold text-gray-800">{{ $solicitud->titulo }}</div>
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        {{ \Illuminate\Support\Str::limit($solicitud->descripcion_cambio, 50) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <div class="avatar placeholder">
                                                            <div class="bg-gradient-to-br from-purple-400 to-pink-500 text-white rounded-full w-10 h-10 flex items-center justify-center shadow">
                                                                <span class="text-xs font-bold">{{ substr($solicitud->solicitante->nombre_completo, 0, 2) }}</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <span class="text-sm font-medium text-gray-700">{{ $solicitud->solicitante->nombre_completo }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($solicitud->prioridad === 'CRITICA') bg-red-100 text-red-800
                                                        @elseif($solicitud->prioridad === 'ALTA') bg-orange-100 text-orange-800
                                                        @elseif($solicitud->prioridad === 'MEDIA') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst(strtolower($solicitud->prioridad)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge
                                                        @if($solicitud->estado === 'ABIERTA') bg-blue-100 text-blue-800 border-0
                                                        @elseif($solicitud->estado === 'EN_REVISION') bg-yellow-100 text-yellow-800 border-0
                                                        @elseif($solicitud->estado === 'APROBADA') bg-green-100 text-green-800 border-0
                                                        @elseif($solicitud->estado === 'RECHAZADA') bg-red-100 text-red-800 border-0
                                                        @elseif($solicitud->estado === 'IMPLEMENTADA') bg-purple-100 text-purple-800 border-0
                                                        @else bg-gray-100 text-gray-800 border-0
                                                        @endif">
                                                        {{ str_replace('_', ' ', $solicitud->estado) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                        {{ $solicitud->items->count() }} EC
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($solicitud->estado === 'EN_REVISION')
                                                        <div class="flex items-center gap-2 text-sm">
                                                            <span class="flex items-center gap-1 text-green-600 font-semibold">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                                {{ $solicitud->votos->where('voto', 'APROBAR')->count() }}
                                                            </span>
                                                            <span class="flex items-center gap-1 text-red-600 font-semibold">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                                {{ $solicitud->votos->where('voto', 'RECHAZAR')->count() }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="text-sm text-gray-600">
                                                        <div class="font-medium">{{ $solicitud->creado_en->format('d/m/Y') }}</div>
                                                        <div class="text-xs text-gray-400">{{ $solicitud->creado_en->format('H:i') }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $solicitud]) }}"
                                                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-8 text-gray-500">
                                                    No hay solicitudes de cambio registradas
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Panel de CCB para miembros --}}
                    @if($esMiembroCCB)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mt-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-gray-900 text-white rounded-lg p-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-base text-gray-900 mb-2">Eres miembro del CCB</h3>
                                    <p class="text-sm text-gray-600 mb-4">Accede al panel de votación del Comité de Control de Cambios.</p>
                                    <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                                        Ir al Dashboard del CCB
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            @elseif($tabActual === 'metodologia')
                <!-- CONTENIDO TAB: METODOLOGÍA PERSONALIZADA -->
                @php
                    $nombreMetodologia = strtolower($proyecto->metodologia->nombre ?? '');
                @endphp

                @if($nombreMetodologia === 'scrum')
                    <!-- Vista específica para SCRUM -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Mi Vista Scrum
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Gestiona tus tareas usando la metodología Scrum</p>
                            </div>
                        </div>

                        <!-- Panel de información Scrum -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white rounded-lg p-4 text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1">Product Backlog</h3>
                                    <p class="text-sm text-gray-600">Todas las historias de usuario</p>
                                    @php
                                        $misTareasScrum = $datosIntegrados['tareas'] ?? collect();
                                        $totalBacklog = $misTareasScrum->count();
                                    @endphp
                                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ $totalBacklog }}</p>
                                </div>

                                <div class="bg-white rounded-lg p-4 text-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1">En Sprint</h3>
                                    <p class="text-sm text-gray-600">Tareas en desarrollo</p>
                                    @php
                                        $enSprint = $misTareasScrum->filter(function($tarea) {
                                            return in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso', 'en desarrollo']);
                                        })->count();
                                    @endphp
                                    <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $enSprint }}</p>
                                </div>

                                <div class="bg-white rounded-lg p-4 text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1">Completadas</h3>
                                    <p class="text-sm text-gray-600">Historias terminadas</p>
                                    @php
                                        $completadasScrum = $misTareasScrum->filter(function($tarea) {
                                            return in_array(strtolower($tarea->estado), ['completado', 'completada', 'done']);
                                        })->count();
                                    @endphp
                                    <p class="text-2xl font-bold text-green-600 mt-2">{{ $completadasScrum }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ceremonias Scrum -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Ceremonias Scrum
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Daily Scrum -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Daily Scrum</h4>
                                    <p class="text-xs text-gray-600 mb-2">Reunión diaria de 15 min</p>
                                    <span class="text-xs text-blue-600 font-medium">Próxima: Mañana 9:00 AM</span>
                                </div>

                                <!-- Sprint Planning -->
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Sprint Planning</h4>
                                    <p class="text-xs text-gray-600 mb-2">Planificación del sprint</p>
                                    <span class="text-xs text-green-600 font-medium">Cada 2 semanas</span>
                                </div>

                                <!-- Sprint Review -->
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Sprint Review</h4>
                                    <p class="text-xs text-gray-600 mb-2">Revisión del producto</p>
                                    <span class="text-xs text-purple-600 font-medium">Al final del sprint</span>
                                </div>

                                <!-- Retrospective -->
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Retrospective</h4>
                                    <p class="text-xs text-gray-600 mb-2">Mejora continua</p>
                                    <span class="text-xs text-orange-600 font-medium">Al final del sprint</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tablero Scrum simplificado -->
                        @if($misTareasScrum->count() > 0)
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Mis User Stories
                                </h3>

                                <div class="space-y-3">
                                    @foreach($misTareasScrum->sortBy('prioridad') as $userStory)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                                            User Story
                                                        </span>
                                                        @if($userStory->prioridad)
                                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                                @if(strtolower($userStory->prioridad) === 'alta') bg-red-100 text-red-800
                                                                @elseif(strtolower($userStory->prioridad) === 'media') bg-yellow-100 text-yellow-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ ucfirst($userStory->prioridad) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $userStory->nombre }}</h4>
                                                    @if($userStory->descripcion)
                                                        <p class="text-sm text-gray-600 mb-2">Como usuario, {{ $userStory->descripcion }}</p>
                                                    @endif
                                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                                        @if($userStory->estimacion_horas)
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                {{ $userStory->estimacion_horas }}h
                                                            </span>
                                                        @endif
                                                        @if($userStory->fecha_limite)
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                                {{ $userStory->fecha_limite->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                                        @if(in_array(strtolower($userStory->estado), ['completado', 'completada', 'done'])) bg-green-100 text-green-800
                                                        @elseif(in_array(strtolower($userStory->estado), ['en progreso', 'en_progreso', 'en desarrollo'])) bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ str_replace('_', ' ', ucfirst($userStory->estado)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                @elseif($proyecto->metodologia && strtolower($proyecto->metodologia->nombre) === 'cascada')
                    <!-- Vista específica para CASCADA -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Mi Vista Cascada
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Seguimiento de tu progreso en la metodología cascada</p>
                            </div>
                        </div>

                        @php
                            // Obtener las fases del proyecto cascada
                            $fases = \App\Models\FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
                                ->orderBy('orden')
                                ->get();

                            // Obtener tareas del miembro actual
                            $misTareasCascada = $datosIntegrados['tareas'] ?? collect();

                            // Calcular progreso por fase simulado
                            $progresoFases = [];
                            $totalTareas = $misTareasCascada->count();
                            $tareasPorFase = $totalTareas > 0 ? ceil($totalTareas / max(1, $fases->count())) : 0;

                            foreach($fases as $index => $fase) {
                                // Simular distribución de tareas por fase
                                $tareasSimuladas = $misTareasCascada->slice($index * $tareasPorFase, $tareasPorFase);
                                $completadasFase = $tareasSimuladas->filter(function($t) {
                                    return in_array(strtolower($t->estado), ['completado', 'completada']);
                                })->count();

                                $totalFase = $tareasSimuladas->count();

                                $progresoFases[$fase->id_fase] = [
                                    'fase' => $fase,
                                    'total' => $totalFase,
                                    'completadas' => $completadasFase,
                                    'progreso' => $totalFase > 0 ? round(($completadasFase / $totalFase) * 100) : 0,
                                    'tareas' => $tareasSimuladas
                                ];
                            }

                            // Encontrar la fase actual (primera fase no completada al 100%)
                            $faseActual = collect($progresoFases)->first(function($progreso) {
                                return $progreso['progreso'] < 100 && $progreso['total'] > 0;
                            });
                        @endphp

                        <!-- Progreso General de Fases -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Progreso por Fases</h3>
                            <div class="space-y-4">
                                @foreach($progresoFases as $progreso)
                                    @php $fase = $progreso['fase']; @endphp
                                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg
                                        {{ $faseActual && $faseActual['fase']->id_fase === $fase->id_fase ? 'bg-blue-50 border-blue-200' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                                @if($progreso['progreso'] === 100) bg-green-500 text-white
                                                @elseif($faseActual && $faseActual['fase']->id_fase === $fase->id_fase) bg-blue-500 text-white
                                                @else bg-gray-200 text-gray-600
                                                @endif">
                                                {{ $fase->orden }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $fase->nombre_fase }}</h4>
                                                <p class="text-sm text-gray-600">{{ $fase->descripcion }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-semibold
                                                @if($progreso['progreso'] === 100) text-green-600
                                                @elseif($progreso['progreso'] > 0) text-blue-600
                                                @else text-gray-500
                                                @endif">
                                                {{ $progreso['progreso'] }}%
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $progreso['completadas'] }}/{{ $progreso['total'] }} tareas
                                            </div>
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                                <div class="h-2 rounded-full transition-all
                                                    @if($progreso['progreso'] === 100) bg-green-500
                                                    @elseif($progreso['progreso'] > 0) bg-blue-500
                                                    @else bg-gray-300
                                                    @endif"
                                                    style="width: {{ $progreso['progreso'] }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($faseActual)
                            <!-- Detalles de la Fase Actual -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Fase Actual: {{ $faseActual['fase']->nombre_fase }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $faseActual['fase']->descripcion }}</p>
                                    </div>
                                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        En Progreso
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $faseActual['progreso'] }}%</div>
                                        <div class="text-sm text-gray-600">Completado</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $faseActual['completadas'] }}</div>
                                        <div class="text-sm text-gray-600">Tareas Hechas</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-orange-600">{{ $faseActual['total'] - $faseActual['completadas'] }}</div>
                                        <div class="text-sm text-gray-600">Tareas Pendientes</div>
                                    </div>
                                </div>

                                <!-- Características de la Fase -->
                                <div class="bg-white rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 mb-2">Características de esta Fase</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                            <span>Enfoque secuencial y estructurado</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            <span>Documentación detallada</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                            <span>Revisiones y aprobaciones</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                            <span>Transición a la siguiente fase</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tareas de la Fase Actual -->
                            @if($faseActual['tareas']->count() > 0)
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Mis Tareas en {{ $faseActual['fase']->nombre_fase }}
                                    </h3>

                                    <div class="space-y-3">
                                        @foreach($faseActual['tareas']->sortBy('prioridad') as $tarea)
                                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                                                Cascada
                                                            </span>
                                                            @if($tarea->prioridad)
                                                                <span class="px-2 py-1 rounded text-xs font-medium
                                                                    @if(strtolower($tarea->prioridad) === 'alta') bg-red-100 text-red-800
                                                                    @elseif(strtolower($tarea->prioridad) === 'media') bg-yellow-100 text-yellow-800
                                                                    @else bg-gray-100 text-gray-800
                                                                    @endif">
                                                                    {{ ucfirst($tarea->prioridad) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $tarea->nombre }}</h4>
                                                        @if($tarea->descripcion)
                                                            <p class="text-sm text-gray-600 mb-2">{{ $tarea->descripcion }}</p>
                                                        @endif
                                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                                            @if($tarea->estimacion_horas)
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    {{ $tarea->estimacion_horas }}h
                                                                </span>
                                                            @endif
                                                            @if($tarea->fecha_limite)
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                    </svg>
                                                                    {{ $tarea->fecha_limite->format('d/m/Y') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                                            @if(in_array(strtolower($tarea->estado), ['completado', 'completada'])) bg-green-100 text-green-800
                                                            @elseif(in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso'])) bg-yellow-100 text-yellow-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ str_replace('_', ' ', ucfirst($tarea->estado)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @else
                            <!-- Todas las fases completadas -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">¡Felicitaciones!</h3>
                                <p class="text-sm text-gray-600">Has completado todas tus tareas en las fases del proyecto cascada.</p>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- Vista genérica para otras metodologías -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ $proyecto->metodologia ? $proyecto->metodologia->nombre : 'Metodología Personalizada' }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Vista personalizada para tu metodología de proyecto</p>
                            </div>
                        </div>

                        <!-- Información de la metodología -->
                        @if($proyecto->metodologia)
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $proyecto->metodologia->nombre }}</h3>
                                @if($proyecto->metodologia->descripcion)
                                    <p class="text-sm text-gray-600 mb-4">{{ $proyecto->metodologia->descripcion }}</p>
                                @endif
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">Tipo de Metodología</h4>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                            {{ $proyecto->metodologia->tipo ?? 'No definido' }}
                                        </span>
                                    </div>
                                    <div class="bg-white rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">Estado del Proyecto</h4>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                            Activo
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Resumen de tareas personalizadas -->
                        @php
                            $misTareasGen = $datosIntegrados['tareas'] ?? collect();
                        @endphp

                        @if($misTareasGen->count() > 0)
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Mis Tareas del Proyecto
                                </h3>

                                <!-- Estadísticas rápidas -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    @php
                                        $totalGen = $misTareasGen->count();
                                        $completadasGen = $misTareasGen->filter(function($t) {
                                            return in_array(strtolower($t->estado), ['completado', 'completada']);
                                        })->count();
                                        $progresoGen = $misTareasGen->filter(function($t) {
                                            return in_array(strtolower($t->estado), ['en progreso', 'en_progreso']);
                                        })->count();
                                        $pendientesGen = $totalGen - $completadasGen - $progresoGen;
                                    @endphp

                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $totalGen }}</div>
                                        <div class="text-sm text-gray-600">Total</div>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-green-600">{{ $completadasGen }}</div>
                                        <div class="text-sm text-gray-600">Completadas</div>
                                    </div>
                                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-yellow-600">{{ $progresoGen }}</div>
                                        <div class="text-sm text-gray-600">En Progreso</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-gray-600">{{ $pendientesGen }}</div>
                                        <div class="text-sm text-gray-600">Pendientes</div>
                                    </div>
                                </div>

                                <!-- Lista de tareas -->
                                <div class="space-y-3">
                                    @foreach($misTareasGen->sortBy('prioridad') as $tarea)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $tarea->nombre }}</h4>
                                                    @if($tarea->descripcion)
                                                        <p class="text-sm text-gray-600 mb-2">{{ $tarea->descripcion }}</p>
                                                    @endif
                                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                                        @if($tarea->estimacion_horas)
                                                            <span>Estimación: {{ $tarea->estimacion_horas }}h</span>
                                                            <span>•</span>
                                                        @endif
                                                        @if($tarea->fecha_limite)
                                                            <span>Límite: {{ $tarea->fecha_limite->format('d/m/Y') }}</span>
                                                            <span>•</span>
                                                        @endif
                                                        <span>Prioridad: {{ ucfirst($tarea->prioridad) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                                        @if(in_array(strtolower($tarea->estado), ['completado', 'completada'])) bg-green-100 text-green-800
                                                        @elseif(in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso'])) bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ str_replace('_', ' ', ucfirst($tarea->estado)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- No hay tareas asignadas -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">No hay tareas asignadas</h3>
                                <p class="text-sm text-gray-600">Actualmente no tienes tareas asignadas en este proyecto.</p>
                            </div>
                        @endif
                    </div>
                @endif

            @else
                <!-- CONTENIDO TAB: DASHBOARD (contenido original) -->

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $misTareas = $datosIntegrados['tareas'] ?? collect();
                    $totalTareas = $misTareas->count();

                    // Normalizar estados - buscar tanto en mayúsculas como minúsculas
                    $tareasCompletadas = $misTareas->filter(function($tarea) {
                        return in_array(strtolower($tarea->estado), ['completado', 'completada']);
                    })->count();

                    $tareasEnProgreso = $misTareas->filter(function($tarea) {
                        return in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso', 'en desarrollo', 'en_desarrollo']);
                    })->count();

                    $tareasPendientes = $misTareas->filter(function($tarea) {
                        return in_array(strtolower($tarea->estado), ['pendiente', 'por hacer', 'nuevo']);
                    })->count();

                    $progresoPersonal = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
                @endphp

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Tareas</span>
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900" id="stat-total-tareas">{{ $totalTareas }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Completadas</span>
                        <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-600" id="stat-completadas">{{ $tareasCompletadas }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">En Progreso</span>
                        <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600" id="stat-en-progreso">{{ $tareasEnProgreso }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Mi Progreso</span>
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-purple-600" id="stat-progreso-icon">{{ $progresoPersonal }}%</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-purple-600" id="stat-mi-progreso">{{ $progresoPersonal }}%</p>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full transition-all" id="stat-progress-bar" data-width="{{ $progresoPersonal }}"></div>
                    </div>
                </div>
            </div>

            <!-- Tablero Kanban de Tareas -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Tablero de Tareas
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Arrastra las tarjetas para cambiar el estado de tus tareas</p>
                </div>

                <div class="p-6">
                    @if($misTareas->count() > 0)
                        <!-- Tablero Kanban con 3 columnas -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Columna: POR HACER -->
                            <div class="bg-gray-50 rounded-lg p-4 min-h-[500px]">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                        Por Hacer
                                    </h3>
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                        {{ $tareasPendientes }}
                                    </span>
                                </div>
                                <div class="space-y-3 kanban-column" data-estado="Pendiente" id="columna-pendiente">
                                    @php
                                        $tareasPendientesColeccion = $misTareas->filter(function($tarea) {
                                            return in_array(strtolower($tarea->estado), ['pendiente', 'por hacer', 'nuevo']);
                                        });
                                    @endphp

                                    @forelse($tareasPendientesColeccion as $tarea)
                                        @include('gestionProyectos.partials.tarea-card', ['tarea' => $tarea, 'proyecto' => $proyecto])
                                    @empty
                                        <div class="text-center py-8 text-gray-500 text-sm">
                                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p>No hay tareas pendientes</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Columna: EN PROGRESO -->
                            <div class="bg-blue-50 rounded-lg p-4 min-h-[500px]">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
                                        En Progreso
                                    </h3>
                                    <span class="px-2 py-1 bg-blue-200 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ $tareasEnProgreso }}
                                    </span>
                                </div>
                                <div class="space-y-3 kanban-column" data-estado="En Progreso" id="columna-progreso">
                                    @php
                                        $tareasProgresoColeccion = $misTareas->filter(function($tarea) {
                                            return in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso', 'en desarrollo', 'en_desarrollo']);
                                        });
                                    @endphp

                                    @forelse($tareasProgresoColeccion as $tarea)
                                        @include('gestionProyectos.partials.tarea-card', ['tarea' => $tarea, 'proyecto' => $proyecto])
                                    @empty
                                        <div class="text-center py-8 text-gray-500 text-sm">
                                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p>No hay tareas en progreso</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Columna: FINALIZADO -->
                            <div class="bg-green-50 rounded-lg p-4 min-h-[500px]">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                        Finalizado
                                    </h3>
                                    <span class="px-2 py-1 bg-green-200 text-green-700 text-xs font-semibold rounded-full">
                                        {{ $tareasCompletadas }}
                                    </span>
                                </div>
                                <div class="space-y-3 kanban-column" data-estado="Completado" id="columna-completado">
                                    @php
                                        $tareasCompletadasColeccion = $misTareas->filter(function($tarea) {
                                            return in_array(strtolower($tarea->estado), ['completado', 'completada']);
                                        });
                                    @endphp

                                    @forelse($tareasCompletadasColeccion as $tarea)
                                        @include('gestionProyectos.partials.tarea-card', ['tarea' => $tarea, 'proyecto' => $proyecto])
                                    @empty
                                        <div class="text-center py-8 text-gray-500 text-sm">
                                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <p>No hay tareas completadas</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No tienes tareas asignadas</h3>
                            <p class="text-gray-600">Actualmente no hay tareas asignadas a tu usuario en este proyecto.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Elementos de Configuración Relacionados (solo lectura) -->
            @if(isset($datosIntegrados['elementos']) && $datosIntegrados['elementos']->count() > 0)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Elementos de Configuración Relacionados
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Elementos vinculados a tus tareas</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($datosIntegrados['elementos'] as $elemento)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-semibold text-gray-900">{{ $elemento->nombre }}</h3>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                            {{ $elemento->tipo }}
                                        </span>
                                    </div>
                                    @if($elemento->descripcion)
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($elemento->descripcion, 100) }}</p>
                                    @endif
                                    @if($elemento->versionActual)
                                        <p class="text-xs text-gray-500">Versión: {{ $elemento->versionActual->version }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Script para Drag & Drop del Tablero Kanban -->
    <script>
        let draggedElement = null;
        let draggedTareaId = null;

        document.addEventListener('DOMContentLoaded', function() {
            initKanbanBoard();
            initProgressBar();
        });

        function initProgressBar() {
            // Inicializar la barra de progreso con el valor del servidor
            const progressBar = document.getElementById('stat-progress-bar');
            if (progressBar) {
                const width = progressBar.dataset.width;
                progressBar.style.width = width + '%';
            }
        }

        function initKanbanBoard() {
            const cards = document.querySelectorAll('.tarea-card');
            const columns = document.querySelectorAll('.kanban-column');

            // Configurar eventos de drag para las tarjetas
            cards.forEach(card => {
                card.addEventListener('dragstart', handleDragStart);
                card.addEventListener('dragend', handleDragEnd);
            });

            // Configurar eventos de drop para las columnas
            columns.forEach(column => {
                column.addEventListener('dragover', handleDragOver);
                column.addEventListener('drop', handleDrop);
                column.addEventListener('dragenter', handleDragEnter);
                column.addEventListener('dragleave', handleDragLeave);
            });
        }

        function handleDragStart(e) {
            draggedElement = this;
            draggedTareaId = this.dataset.tareaId;
            this.style.opacity = '0.4';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';

            // Remover clases de highlight de todas las columnas
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.classList.remove('bg-indigo-100', 'border-2', 'border-indigo-400', 'border-dashed');
            });
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDragEnter(e) {
            this.classList.add('bg-indigo-100', 'border-2', 'border-indigo-400', 'border-dashed');
        }

        function handleDragLeave(e) {
            // Solo remover si realmente salimos del elemento
            if (e.target === this) {
                this.classList.remove('bg-indigo-100', 'border-2', 'border-indigo-400', 'border-dashed');
            }
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            const targetColumn = this;
            const nuevoEstado = targetColumn.dataset.estado;

            // Si es la misma columna, no hacer nada
            if (draggedElement.parentElement === targetColumn) {
                return false;
            }

            // Verificar si el nuevo estado es "Completado"
            const estadosCompletados = ['Completado', 'Completada', 'Finalizado', 'Done', 'COMPLETADA'];
            const esCompletado = estadosCompletados.some(e => nuevoEstado.toLowerCase().includes(e.toLowerCase()));

            if (esCompletado) {
                // Mostrar modal para solicitar commit
                document.getElementById('tareaIdParaCommit').value = draggedTareaId;
                document.getElementById('estadoParaCommit').value = nuevoEstado;
                document.getElementById('inputCommitUrl').value = '';

                // Guardar referencia a la columna destino
                window.targetColumnForCommit = targetColumn;

                modalCommitUrl.showModal();
            } else {
                // Añadir la tarjeta a la nueva columna
                targetColumn.appendChild(draggedElement);

                // Actualizar el estado en el servidor
                actualizarEstadoTarea(draggedTareaId, nuevoEstado, null);

                // Actualizar contadores
                actualizarContadores();
            }

            return false;
        }

        function cancelarCommit() {
            modalCommitUrl.close();
            // Recargar para revertir el cambio visual
            window.location.reload();
        }

        function confirmarCommit() {
            const commitUrl = document.getElementById('inputCommitUrl').value.trim();
            const tareaId = document.getElementById('tareaIdParaCommit').value;
            const nuevoEstado = document.getElementById('estadoParaCommit').value;

            if (!commitUrl) {
                mostrarNotificacion('❌ Por favor, ingresa la URL del commit.', 'error');
                return;
            }

            // Validar que sea de GitHub
            if (!commitUrl.includes('github.com')) {
                mostrarNotificacion('❌ La URL debe ser de GitHub (github.com)', 'error');
                return;
            }

            // Validar que sea una URL de commit (no de tree, blob, etc)
            if (!commitUrl.includes('/commit/')) {
                let sugerencia = '';
                if (commitUrl.includes('/tree/')) {
                    sugerencia = '\n\n💡 Detectamos que es una URL de árbol (/tree/). Por favor, ve al commit específico y copia su URL.';
                } else if (commitUrl.includes('/blob/')) {
                    sugerencia = '\n\n💡 Detectamos que es una URL de archivo (/blob/). Por favor, ve al commit específico y copia su URL.';
                }

                mostrarNotificacion('❌ URL inválida. Debe ser una URL de COMMIT de GitHub.\n\n' +
                    '✅ Formato correcto: https://github.com/usuario/repo/commit/abc123...' +
                    sugerencia, 'error');
                return;
            }

            // Validar formato completo con regex
            const commitRegex = /github\.com\/[^\/]+\/[^\/]+\/commit\/[a-f0-9]+/i;
            if (!commitRegex.test(commitUrl)) {
                mostrarNotificacion('❌ URL de commit mal formada.\n\n' +
                    '✅ Formato esperado:\n' +
                    'https://github.com/usuario/repositorio/commit/hash_del_commit', 'error');
                return;
            }

            modalCommitUrl.close();

            // Añadir la tarjeta a la nueva columna
            if (window.targetColumnForCommit && draggedElement) {
                window.targetColumnForCommit.appendChild(draggedElement);
            }

            // Actualizar el estado en el servidor CON commit_url
            actualizarEstadoTarea(tareaId, nuevoEstado, commitUrl);

            // Actualizar contadores
            actualizarContadores();
        }

        function actualizarEstadoTarea(tareaId, nuevoEstado, commitUrl = null) {
            // Mostrar indicador de carga
            const card = document.querySelector(`[data-tarea-id="${tareaId}"]`);
            const originalBorder = card.style.borderColor;
            card.style.borderColor = '#6366f1';
            card.style.borderWidth = '2px';

            // Crear FormData para enviar la solicitud
            const formData = new FormData();

            // Token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', csrfToken);
            formData.append('estado', nuevoEstado);

            // Agregar commit_url si está presente
            if (commitUrl) {
                formData.append('commit_url', commitUrl);

                // Mostrar indicador de carga más prominente para commits
                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'loading-commit';
                loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                loadingDiv.innerHTML = `
                    <div class="bg-white rounded-lg p-6 text-center max-w-sm">
                        <div class="loading loading-spinner loading-lg text-blue-600 mb-4 mx-auto"></div>
                        <p class="text-gray-700 font-medium">Procesando commit...</p>
                        <p class="text-sm text-gray-500 mt-2">Consultando GitHub API y creando Elemento de Configuración</p>
                    </div>
                `;
                document.body.appendChild(loadingDiv);
            }

            fetch(`/proyectos/{{ $proyecto->id }}/tareas/${tareaId}/cambiar-fase`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remover loading si existe
                const loadingDiv = document.getElementById('loading-commit');
                if (loadingDiv) {
                    loadingDiv.remove();
                }

                if (data.success) {
                    // Éxito: mostrar feedback visual
                    card.style.borderColor = '#10b981';
                    setTimeout(() => {
                        card.style.borderColor = originalBorder;
                        card.style.borderWidth = '2px';
                    }, 1000);

                    // Actualizar contadores
                    actualizarContadores();

                    // Mensaje diferente si se procesó un commit
                    if (commitUrl) {
                        const successDiv = document.createElement('div');
                        successDiv.className = 'fixed top-4 right-4 z-50 max-w-md';
                        successDiv.innerHTML = `
                            <div class="alert alert-success shadow-lg">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-bold">¡Tarea completada!</h3>
                                        <div class="text-xs">Elemento de Configuración creado/actualizado en estado "EN REVISIÓN"</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(successDiv);

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        mostrarNotificacion('✅ Estado actualizado correctamente', 'success');
                    }
                } else {
                    throw new Error(data.error || data.message || 'Error al actualizar');
                }
            })
            .catch(error => {
                // Remover loading si existe
                const loadingDiv = document.getElementById('loading-commit');
                if (loadingDiv) {
                    loadingDiv.remove();
                }

                // Error: revertir el cambio
                console.error('Error:', error);
                card.style.borderColor = '#ef4444';
                mostrarNotificacion('❌ Error al actualizar el estado. ' + error.message + '. Recargando página...', 'error');

                // Recargar la página después de 2 segundos
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
        }

        function actualizarContadores() {
            // Actualizar contadores en las cabeceras de columnas del Kanban
            const columnas = document.querySelectorAll('.kanban-column');

            columnas.forEach(columna => {
                const estado = columna.dataset.estado;
                const contador = columna.previousElementSibling?.querySelector('.rounded-full');
                const numTareas = columna.querySelectorAll('.tarea-card').length;

                if (contador) {
                    contador.textContent = numTareas;
                }
            });

            // Contar tareas por estado en el Kanban
            const totalPendientes = document.querySelectorAll('#columna-pendiente .tarea-card').length;
            const totalProgreso = document.querySelectorAll('#columna-progreso .tarea-card').length;
            const totalCompletado = document.querySelectorAll('#columna-completado .tarea-card').length;
            const totalTareas = totalPendientes + totalProgreso + totalCompletado;

            // Calcular progreso
            const progreso = totalTareas > 0 ? Math.round((totalCompletado / totalTareas) * 100) : 0;

            // Actualizar las estadísticas usando IDs específicos
            const statTotalTareas = document.getElementById('stat-total-tareas');
            const statCompletadas = document.getElementById('stat-completadas');
            const statEnProgreso = document.getElementById('stat-en-progreso');
            const statMiProgreso = document.getElementById('stat-mi-progreso');
            const statProgresoIcon = document.getElementById('stat-progreso-icon');
            const statProgressBar = document.getElementById('stat-progress-bar');

            if (statTotalTareas) statTotalTareas.textContent = totalTareas;
            if (statCompletadas) statCompletadas.textContent = totalCompletado;
            if (statEnProgreso) statEnProgreso.textContent = totalProgreso;
            if (statMiProgreso) statMiProgreso.textContent = progreso + '%';
            if (statProgresoIcon) statProgresoIcon.textContent = progreso + '%';
            if (statProgressBar) statProgressBar.style.width = progreso + '%';

            console.log('Estadísticas actualizadas:', {
                total: totalTareas,
                completadas: totalCompletado,
                enProgreso: totalProgreso,
                progreso: progreso + '%'
            });
        }

        function mostrarNotificacion(mensaje, tipo) {
            // Crear notificación toast
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-50 ${tipo === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = mensaje;
            document.body.appendChild(toast);

            // Animar entrada
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                toast.style.transition = 'all 0.3s ease';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 10);

            // Remover después de 3 segundos
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>

    <style>
        .tarea-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .tarea-card:hover {
            transform: translateY(-2px);
        }

        .tarea-card:active {
            cursor: grabbing;
        }

        .kanban-column {
            transition: background-color 0.2s, border 0.2s;
            min-height: 100px;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        function filtrarPorEstado(estado) {
            const filas = document.querySelectorAll('.solicitud-row');
            filas.forEach(fila => {
                if (estado === 'TODAS' || fila.dataset.estado === estado) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }
    </script>

    @endif

    <!-- Modal para solicitar Commit URL cuando se completa una tarea -->
    <dialog id="modalCommitUrl" class="modal">
        <div class="modal-box bg-white max-w-3xl">
            <h3 class="font-bold text-lg text-gray-900 mb-2">✅ Completar Tarea</h3>
            <p class="text-sm text-gray-600 mb-4">
                Esta tarea está siendo marcada como <span class="font-bold text-green-600">COMPLETADA</span>.
                Por favor, proporciona la URL del commit de GitHub que resuelve esta tarea.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 mb-2">Cómo obtener la URL correcta:</p>
                        <ol class="text-xs text-blue-800 space-y-1 list-decimal list-inside">
                            <li>Ve a tu repositorio en GitHub</li>
                            <li>Click en "<strong>Commits</strong>" o "<strong>History</strong>"</li>
                            <li>Busca tu commit y haz click en él</li>
                            <li>Copia la URL de la barra de direcciones</li>
                        </ol>
                        <div class="mt-3 p-2 bg-blue-100 rounded">
                            <p class="text-xs font-medium text-blue-900">✅ Formato correcto:</p>
                            <code class="text-xs text-blue-800 block mt-1">
                                https://github.com/<span class="text-green-700">usuario</span>/<span class="text-green-700">repo</span>/<span class="font-bold text-red-600">/commit/</span><span class="text-purple-700">abc123...</span>
                            </code>
                        </div>
                        <div class="mt-2 p-2 bg-red-50 rounded">
                            <p class="text-xs font-medium text-red-900">❌ NO usar URLs de:</p>
                            <code class="text-xs text-red-800 block">/tree/ (rama) • /blob/ (archivo) • /pull/ (PR)</code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text text-gray-700 font-semibold">URL del Commit *</span>
                </label>
                <input
                    type="url"
                    id="inputCommitUrl"
                    class="input input-bordered w-full bg-white text-gray-900 font-mono text-sm"
                    placeholder="https://github.com/usuario/repositorio/commit/abc123..."
                    required
                />
                <label class="label">
                    <span class="label-text-alt text-gray-500">
                        Pega aquí la URL completa del commit desde GitHub
                    </span>
                </label>
            </div>

            <input type="hidden" id="tareaIdParaCommit">
            <input type="hidden" id="estadoParaCommit">

            <div class="modal-action">
                <button type="button" onclick="cancelarCommit()" class="btn btn-ghost">Cancelar</button>
                <button type="button" onclick="confirmarCommit()" class="btn bg-green-600 text-white hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completar Tarea
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="cancelarCommit()">Cerrar</button>
        </form>
    </dialog>
</x-app-layout>
