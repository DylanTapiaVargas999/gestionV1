<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Historial de Votos - {{ $proyecto->nombre }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $ccb->nombre }}</p>
            </div>
            <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}" class="btn btn-ghost">
                ← Volver al Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Estadísticas rápidas --}}
            <div class="stats stats-vertical lg:stats-horizontal shadow w-full bg-white mb-6">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Total de Votos</div>
                    <div class="stat-value text-primary">{{ $votos->total() }}</div>
                    <div class="stat-desc text-gray-600">En todo el historial</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Votos A Favor</div>
                    <div class="stat-value text-success">{{ $votos->where('voto', 'APROBAR')->count() }}</div>
                    <div class="stat-desc text-gray-600">Solicitudes aprobadas</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-error">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Votos en Contra</div>
                    <div class="stat-value text-error">{{ $votos->where('voto', 'RECHAZAR')->count() }}</div>
                    <div class="stat-desc text-gray-600">Solicitudes rechazadas</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="stat-title text-gray-700">Abstenciones</div>
                    <div class="stat-value text-warning">{{ $votos->where('voto', 'ABSTENERSE')->count() }}</div>
                    <div class="stat-desc text-gray-600">Sin opinión definida</div>
                </div>
            </div>

            {{-- Historial de votos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial Completo de Votaciones</h3>

                    @if($votos->isEmpty())
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay votos registrados</h4>
                            <p class="text-gray-600">Aún no se han emitido votos en ninguna solicitud de cambio.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="text-gray-900">Fecha</th>
                                        <th class="text-gray-900">Miembro CCB</th>
                                        <th class="text-gray-900">Solicitud</th>
                                        <th class="text-gray-900">Voto</th>
                                        <th class="text-gray-900">Comentario</th>
                                        <th class="text-gray-900">Estado Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($votos as $voto)
                                        <tr>
                                            <td class="text-gray-700">
                                                <div class="font-medium">{{ $voto->votado_en->format('d/m/Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $voto->votado_en->format('H:i') }}</div>
                                            </td>

                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-neutral text-neutral-content rounded-full w-8 text-sm">
                                                            <span>{{ substr($voto->usuario->name, 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $voto->usuario->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $voto->usuario->email }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="font-medium text-gray-900">{{ Str::limit($voto->solicitudCambio->titulo, 40) }}</div>
                                                <div class="text-sm text-gray-500">
                                                    <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $voto->solicitudCambio]) }}"
                                                       class="link link-primary">
                                                        Ver solicitud →
                                                    </a>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge
                                                    @if($voto->voto === 'APROBAR') badge-success
                                                    @elseif($voto->voto === 'RECHAZAR') badge-error
                                                    @else badge-warning
                                                    @endif">
                                                    @if($voto->voto === 'APROBAR')
                                                        ✓ Aprobar
                                                    @elseif($voto->voto === 'RECHAZAR')
                                                        ✗ Rechazar
                                                    @else
                                                        ? Abstenerse
                                                    @endif
                                                </span>
                                            </td>

                                            <td class="text-gray-700">
                                                @if($voto->comentario)
                                                    <div class="max-w-xs truncate" title="{{ $voto->comentario }}">
                                                        {{ Str::limit($voto->comentario, 50) }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Sin comentario</span>
                                                @endif
                                            </td>

                                            <td>
                                                @php
                                                    $estadoFinal = $voto->solicitudCambio->estado;
                                                @endphp
                                                <span class="badge
                                                    @if($estadoFinal === 'APROBADA') badge-success
                                                    @elseif($estadoFinal === 'RECHAZADA') badge-error
                                                    @elseif($estadoFinal === 'EN_REVISION') badge-warning
                                                    @elseif($estadoFinal === 'IMPLEMENTADA') badge-primary
                                                    @else badge-ghost
                                                    @endif">
                                                    {{ str_replace('_', ' ', $estadoFinal) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-6">
                            {{ $votos->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Dashboard del CCB</div>
                    <div class="text-sm text-gray-600">Ver solicitudes pendientes</div>
                </a>

                <a href="{{ route('proyectos.solicitudes.index', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Todas las Solicitudes</div>
                    <div class="text-sm text-gray-600">Ver historial completo</div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
