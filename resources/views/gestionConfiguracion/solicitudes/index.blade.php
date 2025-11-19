<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Solicitudes de Cambio - {{ $proyecto->nombre }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $proyecto->codigo }}</p>
            </div>
            <a href="{{ route('proyectos.solicitudes.create', $proyecto) }}"
               class="btn btn-primary">
                + Nueva Solicitud de Cambio
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
                            Todas ({{ $solicitudes->count() }})
                        </button>
                        <button class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('ABIERTA')">
                            Abiertas ({{ $solicitudes->where('estado', 'ABIERTA')->count() }})
                        </button>
                        <button class="px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('EN_REVISION')">
                            En Revisión ({{ $solicitudes->where('estado', 'EN_REVISION')->count() }})
                        </button>
                        <button class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('APROBADA')">
                            Aprobadas ({{ $solicitudes->where('estado', 'APROBADA')->count() }})
                        </button>
                        <button class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg transition font-medium text-sm" onclick="filtrarPorEstado('RECHAZADA')">
                            Rechazadas ({{ $solicitudes->where('estado', 'RECHAZADA')->count() }})
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
                                @forelse ($solicitudes as $solicitud)
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
    </div>

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
</x-app-layout>
