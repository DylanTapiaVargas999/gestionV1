<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Elementos de Configuración - {{ $proyecto->nombre }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Código del Proyecto: <span class="font-mono font-bold">{{ $proyecto->codigo }}</span>
                </p>
            </div>
            <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-ghost btn-sm">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success mb-6 shadow-lg">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Barra de acciones -->
            <div class="flex justify-between items-center mb-6">
                <div class="stats shadow">
                    <div class="stat bg-white text-black border border-gray-200">
                        <div class="stat-title text-black">Total Elementos</div>
                        <div class="stat-value text-blue-600">{{ $elementos->count() }}</div>
                    </div>
                    <div class="stat bg-white text-black border border-gray-200">
                        <div class="stat-title text-black">Aprobados</div>
                        <div class="stat-value text-blue-600">{{ $elementos->where('estado', 'APROBADO')->count() }}</div>
                    </div>
                    <div class="stat bg-white text-black border border-gray-200">
                        <div class="stat-title text-black">En Revisión</div>
                        <div class="stat-value text-blue-600">{{ $elementos->where('estado', 'EN_REVISION')->count() }}</div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('proyectos.elementos.verGrafo', ['proyecto' => $proyecto->id]) }}" class="btn btn-info gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Ver Grafo de Trazabilidad
                    </a>
                    <a href="{{ route('proyectos.elementos.create', $proyecto) }}" class="btn bg-black text-white gap-2 hover:bg-gray-800 border-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Elemento
                    </a>
                </div>
            </div>

            <!-- Tabla de elementos -->
            <div class="card w-full bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4">
                    @if($elementos->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay elementos de configuración</h3>
                            <p class="mt-2 text-sm text-gray-500">Comienza creando el primer elemento de configuración del proyecto.</p>
                            <div class="mt-6">
                                <a href="{{ route('proyectos.elementos.create', $proyecto) }}" class="btn btn-primary">
                                    Crear Primer Elemento
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <th class="px-4 py-3">Código EC</th>
                                        <th class="px-4 py-3">Título</th>
                                        <th class="px-4 py-3">Tipo</th>
                                        <th class="px-4 py-3">Estado</th>
                                        <th class="px-4 py-3">Versión</th>
                                        <th class="px-4 py-3">Commit</th>
                                        <th class="px-4 py-3">Creado Por</th>
                                        <th class="px-4 py-3">Fecha Creación</th>
                                        <th class="px-4 py-3 w-64 sm:w-56 text-center" style="min-width:220px">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($elementos as $elemento)
                                        <tr>
                                            @php
                                                // Normalize tipo -> badge class
                                                $tipoName = strtoupper(trim($elemento->tipo ?? ''));
                                                if ($tipoName === 'CODIGO') {
                                                    $tipoClass = 'badge-success';
                                                } elseif ($tipoName === 'DOCUMENTO') {
                                                    $tipoClass = 'badge-info';
                                                } elseif (in_array($tipoName, ['SCRIPT_BD', 'SCRIPT-BD', 'SCRIPT BD'])) {
                                                    $tipoClass = 'badge-warning';
                                                } else {
                                                    $tipoClass = 'badge-ghost';
                                                }

                                                // Normalize estado -> badge class
                                                $estadoName = strtoupper(trim($elemento->estado ?? ''));
                                                if ($estadoName === 'PENDIENTE') {
                                                    $estadoClass = 'badge-neutral';
                                                } elseif ($estadoName === 'APROBADO') {
                                                    $estadoClass = 'badge-success';
                                                } elseif (strpos($estadoName, 'REV') !== false) {
                                                    $estadoClass = 'badge-warning';
                                                } else {
                                                    $estadoClass = 'badge-ghost';
                                                }
                                            @endphp
                                            <td class="px-4 py-3 align-middle text-gray-700">
                                                <span class="font-mono font-bold text-sm">{{ $elemento->codigo_ec }}</span>
                                            </td>
                                            <td class="px-4 py-3 align-middle text-gray-700">
                                                <div class="font-medium text-gray-900">{{ $elemento->titulo }}</div>
                                                <div class="text-sm text-gray-500 line-clamp-1">{{ Str::limit($elemento->descripcion, 50) }}</div>
                                            </td>
                                            <td class="px-4 py-3 align-middle">
                                                <span class="badge {{ $tipoClass }}">{{ $elemento->tipo }}</span>
                                            </td>
                                            <td class="px-4 py-3 align-middle">
                                                <span class="badge {{ $estadoClass }}">{{ $elemento->estado }}</span>
                                            </td>
                                            <td class="px-4 py-3 align-middle">
                                                <span class="badge badge-outline text-black">v{{ $elemento->versionActual?->version ?? '1.0' }}</span>
                                            </td>
                                            <td class="px-4 py-3 align-middle">
                                                @if($elemento->versionActual?->commit)
                                                    <a href="{{ $elemento->versionActual->commit->url_repositorio }}/commit/{{ $elemento->versionActual->commit->hash_commit }}" target="_blank" class="tooltip tooltip-left" data-tip="{{ Str::limit($elemento->versionActual->commit->mensaje, 50) }}">
                                                        <span class="badge badge-success badge-sm gap-1 inline-flex items-center px-4 py-1 whitespace-nowrap">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM1.5 8a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0z"/>
                                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                            </svg>
                                                            {{ Str::limit($elemento->versionActual->commit->hash_commit, 7, '') }}
                                                        </span>
                                                    </a>
                                                @else
                                                    <span class="badge badge-ghost badge-sm px-4 py-1 whitespace-nowrap">Sin commit</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 align-middle">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    @if($elemento->creador)
                                                        <div class="avatar placeholder">
                                                            <div class="bg-white text-black border border-gray-300 rounded-full w-8">
                                                                @php
                                                                    $partes = explode(' ', $elemento->creador->nombre_completo);
                                                                    $inicial_nombre = isset($partes[0]) ? substr($partes[0], 0, 1) : '';
                                                                    $inicial_apellido = isset($partes[1]) ? substr($partes[1], 0, 1) : '';
                                                                @endphp
                                                                <span class="text-xs">{{ $inicial_nombre }}{{ $inicial_apellido }}</span>
                                                            </div>
                                                        </div>
                                                        <span class="text-sm">{{ $elemento->creador->nombre_completo }}</span>
                                                    @else
                                                        <span class="text-sm text-gray-400">Sin autor</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 align-middle text-gray-700">
                                                <span class="text-sm text-gray-600">{{ $elemento->creado_en->format('d/m/Y') }}</span>
                                            </td>
                                            <td class="px-4 py-3 align-middle text-center">
                                                <div class="flex gap-2 justify-center items-center whitespace-nowrap">
                                                    <a href="{{ route('proyectos.elementos.relaciones.index', [$proyecto, $elemento]) }}" class="btn btn-ghost text-black border border-black hover:bg-transparent hover:text-black px-3 py-2" title="Relaciones">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('proyectos.elementos.review', [$proyecto, $elemento]) }}" class="btn btn-ghost text-black border border-black hover:bg-transparent hover:text-black px-3 py-2" title="Revisar/Aprobar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('proyectos.elementos.edit', [$proyecto, $elemento]) }}" class="btn btn-ghost text-black border border-black hover:bg-transparent hover:text-black px-3 py-2" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('proyectos.elementos.destroy', [$proyecto, $elemento]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este elemento de configuración?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-ghost text-red-600 border border-red-300 hover:bg-red-50 hover:text-red-700 px-3 py-2" title="Eliminar">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
