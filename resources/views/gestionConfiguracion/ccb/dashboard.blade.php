<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard del CCB - {{ $proyecto->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Estadísticas --}}
            <div class="stats stats-vertical lg:stats-horizontal shadow w-full bg-white">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="text-black">Total Solicitudes</div>
                    <div class="stat-value text-primary">{{ $estadisticas['total_solicitudes'] }}</div>
                    <div class="text-black">En revisión</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Pendientes Mi Voto</div>
                    <div class="stat-value text-warning">{{ $estadisticas['pendientes_mi_voto'] }}</div>
                    <div class="text-black">Requieren tu atención</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Ya Voté</div>
                    <div class="stat-value text-success">{{ $estadisticas['ya_vote'] }}</div>
                    <div class="text-black">Votos emitidos</div>
                </div>
            </div>

            {{-- Información del CCB --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-black">{{ $ccb->nombre }}</h3>
                            <p class="text-sm text-gray-600">Quorum necesario: {{ $ccb->quorum }} votos</p>
                        </div>
                        <a href="{{ route('proyectos.ccb.miembros', $proyecto) }}" class="text-black">
                            Ver Miembros ({{ $ccb->miembros()->count() }})
                        </a>
                    </div>
                </div>
            </div>

            {{-- Solicitudes pendientes de votación --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-black">Solicitudes Pendientes de Revisión</h3>

                    @if($solicitudesPendientes->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No hay solicitudes pendientes de revisión</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($solicitudesPendientes as $solicitud)
                                @php
                                    $yaVote = in_array($solicitud->id, $misVotos);
                                    $totalVotos = $solicitud->votos->count();
                                    $votosAprobar = $solicitud->votos->where('voto', 'APROBAR')->count();
                                    $votosRechazar = $solicitud->votos->where('voto', 'RECHAZAR')->count();
                                @endphp

                                <div class="border rounded-lg p-4 {{ $yaVote ? 'bg-gray-50' : 'bg-white' }}">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-lg">{{ $solicitud->titulo }}</h4>
                                                    <p class="text-sm text-gray-600 mt-1">{{ \Str::limit($solicitud->descripcion_cambio, 100) }}</p>
                                                </div>
                                                <span class="badge
                                                    @if($solicitud->prioridad === 'CRITICA') badge-error
                                                    @elseif($solicitud->prioridad === 'ALTA') badge-warning
                                                    @elseif($solicitud->prioridad === 'MEDIA') badge-info
                                                    @else badge-ghost
                                                    @endif ml-2">
                                                    {{ $solicitud->prioridad }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-neutral text-neutral-content rounded-full w-6 text-xs">
                                                            <span>{{ substr($solicitud->solicitante->nombre_completo, 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <span>{{ $solicitud->solicitante->nombre_completo }}</span>
                                                </div>
                                                <span>•</span>
                                                <span>{{ $solicitud->items->count() }} EC afectados</span>
                                                <span>•</span>
                                                <span>{{ $solicitud->creado_en->diffForHumans() }}</span>
                                            </div>

                                            {{-- Progreso de votación --}}
                                            <div class="flex items-center gap-4">
                                                <div class="flex-1">
                                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                        <span>Progreso de votación</span>
                                                        <span>{{ $totalVotos }} / {{ $ccb->miembros()->count() }}</span>
                                                    </div>
                                                    @php
                                                        $porcentajeVotos = $ccb->miembros()->count() > 0 ? round(($totalVotos / $ccb->miembros()->count()) * 100) : 0;
                                                    @endphp
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full progress-bar" data-width="{{ $porcentajeVotos }}"></div>
                                                    </div>
                                                </div>
                                                <div class="flex gap-3 text-sm">
                                                    <span class="text-green-600">✓ {{ $votosAprobar }}</span>
                                                    <span class="text-red-600">✗ {{ $votosRechazar }}</span>
                                                </div>
                                            </div>

                                            @if($yaVote)
                                                <div class="mt-3">
                                                    <span class="badge badge-success badge-sm">✓ Ya votaste en esta solicitud</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $solicitud]) }}"
                                               class="btn btn-sm {{ $yaVote ? 'btn-ghost' : 'btn-primary' }}">
                                                {{ $yaVote ? 'Ver Detalles' : 'Votar' }}
                                            </a>
                                            <a href="{{ route('proyectos.solicitudes.evaluar-impacto', [$proyecto, $solicitud]) }}"
                                               class="btn btn-sm btn-outline inline-flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M3 13h18M7 16l4-4 4 4 4-4"/>
                                                </svg>
                                                Ver Impacto
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('proyectos.solicitudes.index', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-black">Todas las Solicitudes</div>
                    <div class="text-sm text-gray-600">Ver historial completo</div>
                </a>

                <a href="{{ route('proyectos.ccb.miembros', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-black">Gestionar Miembros</div>
                    <div class="text-sm text-gray-600">Agregar/quitar miembros</div>
                </a>

                <a href="{{ route('proyectos.ccb.historial-votos', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3-3 4 4 4-4"/>
                        </svg>
                    </div>
                    <div class="text-black">Historial de Votos</div>
                    <div class="text-sm text-gray-600">Ver todas las votaciones</div>
                </a>

                <a href="{{ route('proyectos.show', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a2 2 0 002 2h12a2 2 0 002-2V10"/>
                        </svg>
                    </div>
                    <div class="text-black">Dashboard Proyecto</div>
                    <div class="text-sm text-gray-600">Volver al proyecto</div>
                </a>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set progress bar widths
            document.querySelectorAll('.progress-bar').forEach(function(bar) {
                const width = bar.getAttribute('data-width');
                bar.style.width = width + '%';
            });
        });
    </script>
</x-app-layout>
