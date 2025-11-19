<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Matriz de Trazabilidad Integral
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $proyecto->codigo }} - {{ $proyecto->nombre }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-ghost btn-sm">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Proyecto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            @if($elementos->isEmpty())
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-gray-900">Este proyecto no tiene elementos de configuración todavía.</span>
                </div>
            @else
                <!-- Leyenda y Estadísticas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Leyenda de Relaciones -->
                    <div class="card bg-white shadow-md border border-gray-200">
                        <div class="card-body text-gray-900">
                            <h3 class="font-bold text-lg mb-3">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tipos de Relaciones
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-error badge-sm">DEPENDE_DE</span>
                                    <span class="text-xs text-gray-700">Requiere</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-sm">DERIVADO_DE</span>
                                    <span class="text-xs text-gray-700">Extensión</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-info badge-sm">REFERENCIA</span>
                                    <span class="text-xs text-gray-700">Referencia</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-sm">REQUERIDO_POR</span>
                                    <span class="text-xs text-gray-700">Es requerido</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Generales -->
                    <div class="card bg-white shadow-md border border-gray-200">
                        <div class="card-body text-gray-900">
                            <h3 class="font-bold text-lg mb-3">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Estadísticas
                            </h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Total EC:</span>
                                    <span class="font-bold ml-2">{{ $elementos->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Relaciones:</span>
                                    <span class="font-bold ml-2">{{ $elementos->sum(fn($e) => $e->relacionesDesde->count()) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Versiones:</span>
                                    <span class="font-bold ml-2">{{ $elementos->sum(fn($e) => $e->versiones->count()) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Con Commits:</span>
                                    <span class="font-bold ml-2">{{ $elementos->filter(fn($e) => $e->versiones->whereNotNull('commit_id')->count() > 0)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matriz de Trazabilidad -->
                <div class="card bg-white shadow-md border border-gray-200">
                    <div class="card-body text-gray-900 p-0">
                        <div class="sticky top-0 bg-white z-10 p-4 border-b border-gray-200">
                            <h3 class="font-bold text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                Matriz de Trazabilidad
                            </h3>
                            <p class="text-xs text-gray-600 mt-1">
                                Las filas representan elementos de origen, las columnas elementos de destino. Haz clic en las celdas para ver versiones y commits.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table table-xs table-pin-rows table-pin-cols">
                                <thead>
                                    <tr>
                                        <th class="bg-gray-100 text-gray-900 border border-gray-300 sticky left-0 z-20 min-w-[200px]">
                                            Elemento
                                        </th>
                                        @foreach($elementos as $elemento)
                                            <th class="bg-gray-100 text-gray-900 border border-gray-300 text-center min-w-[100px] rotate-text">
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="badge {{ $elemento->tipoBadge }} badge-xs">{{ $elemento->tipo }}</span>
                                                    <span class="text-xs font-normal">{{ $elemento->codigo_ec }}</span>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($elementos as $desde)
                                        <tr class="hover">
                                            <td class="bg-gray-50 border border-gray-300 sticky left-0 z-10 font-semibold">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="badge {{ $desde->tipoBadge }} badge-xs">{{ $desde->tipo }}</span>
                                                        <span class="text-xs text-gray-500">{{ $desde->codigo_ec }}</span>
                                                    </div>
                                                    <span class="text-xs truncate" title="{{ $desde->titulo }}">{{ Str::limit($desde->titulo, 30) }}</span>
                                                    @if($desde->versiones->isNotEmpty())
                                                        <span class="badge badge-outline badge-xs">
                                                            v{{ $desde->versiones->first()->version }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            @foreach($elementos as $hacia)
                                                @php
                                                    $relacion = $matriz[$desde->id][$hacia->id] ?? null;
                                                @endphp
                                                <td class="border border-gray-300 text-center p-1 {{ $desde->id === $hacia->id ? 'bg-gray-200' : '' }}">
                                                    @if($desde->id === $hacia->id)
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @elseif($relacion)
                                                        <button
                                                            onclick="mostrarDetalleRelacion('{{ $desde->id }}', '{{ $hacia->id }}')"
                                                            class="badge {{ $relacion->tipoBadge }} badge-xs cursor-pointer hover:scale-110 transition-transform"
                                                            title="{{ $relacion->tipo_relacion }} - Click para ver detalles">
                                                            {{ substr($relacion->tipo_relacion, 0, 3) }}
                                                        </button>
                                                    @else
                                                        <span class="text-gray-300 text-xs">·</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lista de Elementos con Versiones y Commits -->
                <!-- Lista de Elementos con Versiones y Commits -->
                <div class="card bg-white shadow-md border border-gray-200 mt-6">
                    <div class="card-body text-gray-900">
                        <h3 class="font-bold text-lg mb-4">
                            <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Elementos de Configuración con Versiones
                        </h3>

                        <div class="space-y-4">
                            @foreach($elementos as $elemento)
                                <div class="collapse collapse-arrow bg-gray-50 border border-gray-300">
                                    <input type="checkbox" />
                                    <div class="collapse-title font-medium">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="badge {{ $elemento->tipoBadge }} badge-sm">{{ $elemento->tipo }}</span>
                                                <span class="font-semibold">{{ $elemento->codigo_ec }}</span>
                                                <span class="text-sm">{{ $elemento->titulo }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="badge {{ $elemento->estadoBadge }} badge-sm">{{ $elemento->estado }}</span>
                                                <span class="badge badge-outline badge-sm">{{ $elemento->versiones->count() }} versiones</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="collapse-content">
                                        @if($elemento->versiones->isEmpty())
                                            <p class="text-sm text-gray-500 italic">Sin versiones registradas</p>
                                        @else
                                            <div class="overflow-x-auto mt-2">
                                                <table class="table table-xs">
                                                    <thead>
                                                        <tr class="bg-gray-100 text-gray-900">
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Versión</th>
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Estado</th>
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Creado por</th>
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Fecha</th>
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Commit</th>
                                                            <th class="px-2 py-1 text-xs font-semibold border border-gray-300">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($elemento->versiones as $version)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge badge-outline badge-sm">v{{ $version->version }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge {{
                                                                        $version->estado === 'LIBERADO' ? 'badge-success' :
                                                                        ($version->estado === 'APROBADO' ? 'badge-info' :
                                                                        ($version->estado === 'EN_REVISION' ? 'badge-warning' : 'badge-ghost'))
                                                                    }} badge-xs">
                                                                        {{ $version->estado }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-xs">{{ $version->creador->nombre ?? 'N/A' }}</td>
                                                                <td class="text-xs">{{ $version->creado_en->format('d/m/Y H:i') }}</td>
                                                                <td>
                                                                    @if($version->commit)
                                                                        <button
                                                                            onclick="verDetallesCommit('{{ $version->commit->id }}')"
                                                                            class="btn btn-xs btn-ghost gap-1"
                                                                            title="Ver detalles del commit">
                                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            {{ $version->commit->hash_corto }}
                                                                        </button>
                                                                    @else
                                                                        <span class="text-xs text-gray-400">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($version->commit)
                                                                        <a href="{{ $version->commit->url_completa }}"
                                                                           target="_blank"
                                                                           class="btn btn-xs btn-outline"
                                                                           title="Abrir en GitHub">
                                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/>
                                                                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/>
                                                                            </svg>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal para Detalles de Relación -->
    <dialog id="modalRelacion" class="modal">
        <div class="modal-box max-w-4xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="font-bold text-lg mb-4">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Detalles de Relación
            </h3>

            <div class="space-y-6">
                <!-- Elemento Origen -->
                <div class="card bg-blue-50 border-2 border-blue-300">
                    <div class="card-body p-4">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                            Elemento Origen
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Código:</span>
                                <span id="desdeCodigoRelacion" class="ml-2"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Tipo:</span>
                                <span id="desdeTipoRelacion" class="ml-2"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="font-semibold text-gray-700">Título:</span>
                                <p id="desdeTituloRelacion" class="mt-1 text-gray-800"></p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Estado:</span>
                                <span id="desdeEstadoRelacion" class="ml-2"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Versiones:</span>
                                <span id="desdeVersionesRelacion" class="ml-2"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Relación -->
                <div class="text-center">
                    <div class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-100 to-pink-100 px-6 py-3 rounded-lg border-2 border-purple-300">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span id="tipoRelacionTexto" class="font-bold text-lg text-purple-800"></span>
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                </div>

                <!-- Elemento Destino -->
                <div class="card bg-green-50 border-2 border-green-300">
                    <div class="card-body p-4">
                        <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Elemento Destino
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Código:</span>
                                <span id="haciaCodigoRelacion" class="ml-2"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Tipo:</span>
                                <span id="haciaTipoRelacion" class="ml-2"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="font-semibold text-gray-700">Título:</span>
                                <p id="haciaTituloRelacion" class="mt-1 text-gray-800"></p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Estado:</span>
                                <span id="haciaEstadoRelacion" class="ml-2"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Versiones:</span>
                                <span id="haciaVersionesRelacion" class="ml-2"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Versiones Comparadas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Versiones del Origen -->
                    <div class="card bg-white border border-gray-300">
                        <div class="card-body p-4">
                            <h5 class="font-semibold text-sm text-gray-700 mb-2">Versiones del Origen</h5>
                            <div id="desdeVersionesLista" class="space-y-1 max-h-40 overflow-y-auto">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Versiones del Destino -->
                    <div class="card bg-white border border-gray-300">
                        <div class="card-body p-4">
                            <h5 class="font-semibold text-sm text-gray-700 mb-2">Versiones del Destino</h5>
                            <div id="haciaVersionesLista" class="space-y-1 max-h-40 overflow-y-auto">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex justify-end gap-2">
                    <a id="verOrigenBtn" href="#" class="btn btn-sm btn-outline btn-primary">
                        Ver Origen Completo
                    </a>
                    <a id="verDestinoBtn" href="#" class="btn btn-sm btn-outline btn-success">
                        Ver Destino Completo
                    </a>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Modal para Detalles de Commit -->
    <dialog id="modalCommit" class="modal">
        <div class="modal-box max-w-3xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="font-bold text-lg mb-4">
                <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Detalles del Commit
            </h3>

            <div id="commitLoading" class="text-center py-8">
                <span class="loading loading-spinner loading-lg"></span>
                <p class="text-sm text-gray-600 mt-2">Consultando GitHub API...</p>
            </div>

            <div id="commitContent" class="hidden space-y-4">
                <!-- Información Básica -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-semibold text-gray-700">Hash:</span>
                            <code id="commitHash" class="ml-2 bg-gray-200 px-2 py-1 rounded"></code>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Autor:</span>
                            <span id="commitAutor" class="ml-2"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="font-semibold text-gray-700">Email:</span>
                            <span id="commitEmail" class="ml-2 text-gray-600"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="font-semibold text-gray-700">Fecha:</span>
                            <span id="commitFecha" class="ml-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Mensaje del Commit -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Mensaje:</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre id="commitMensaje" class="text-sm whitespace-pre-wrap font-mono"></pre>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Estadísticas:</h4>
                    <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
                        <div class="stat">
                            <div class="stat-title">Archivos</div>
                            <div class="stat-value text-primary" id="commitArchivos">0</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Adiciones</div>
                            <div class="stat-value text-success" id="commitAdiciones">0</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Eliminaciones</div>
                            <div class="stat-value text-error" id="commitEliminaciones">0</div>
                        </div>
                    </div>
                </div>

                <!-- Archivos Modificados -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Archivos Modificados:</h4>
                    <div id="commitArchivosLista" class="bg-gray-50 p-4 rounded-lg max-h-64 overflow-y-auto">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>

                <!-- Enlace a GitHub -->
                <div class="text-center">
                    <a id="commitUrlGithub" href="#" target="_blank" class="btn btn-primary btn-sm gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/>
                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/>
                        </svg>
                        Ver en GitHub
                    </a>
                </div>
            </div>

            <div id="commitError" class="hidden alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="commitErrorMsg">Error al cargar los detalles del commit</span>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    @push('scripts')
    <script>
        // Datos de elementos para JavaScript (preparados en el controlador)
        const elementos = @json($elementosJS);

        function mostrarDetalleRelacion(desdeId, haciaId) {
            const desde = elementos.find(e => e.id === desdeId);
            const hacia = elementos.find(e => e.id === haciaId);

            if (!desde || !hacia) return;

            // Obtener el modal
            const modal = document.getElementById('modalRelacion');

            // Llenar información del elemento origen
            document.getElementById('desdeCodigoRelacion').textContent = desde.codigo;
            document.getElementById('desdeTipoRelacion').innerHTML = `<span class="badge badge-sm badge-${getBadgeType(desde.tipo)}">${desde.tipo}</span>`;
            document.getElementById('desdeTituloRelacion').textContent = desde.titulo;
            document.getElementById('desdeEstadoRelacion').innerHTML = `<span class="badge badge-xs badge-${getBadgeEstado(desde.estado)}">${desde.estado}</span>`;
            document.getElementById('desdeVersionesRelacion').textContent = desde.versiones.length;

            // Llenar información del elemento destino
            document.getElementById('haciaCodigoRelacion').textContent = hacia.codigo;
            document.getElementById('haciaTipoRelacion').innerHTML = `<span class="badge badge-sm badge-${getBadgeType(hacia.tipo)}">${hacia.tipo}</span>`;
            document.getElementById('haciaTituloRelacion').textContent = hacia.titulo;
            document.getElementById('haciaEstadoRelacion').innerHTML = `<span class="badge badge-xs badge-${getBadgeEstado(hacia.estado)}">${hacia.estado}</span>`;
            document.getElementById('haciaVersionesRelacion').textContent = hacia.versiones.length;

            // Tipo de relación (esto se puede mejorar obteniendo el tipo real desde la matriz)
            document.getElementById('tipoRelacionTexto').textContent = 'RELACIÓN ESTABLECIDA';

            // Listar versiones del origen
            const desdeVersionesLista = document.getElementById('desdeVersionesLista');
            desdeVersionesLista.innerHTML = '';
            if (desde.versiones.length > 0) {
                desde.versiones.forEach(v => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between text-xs bg-gray-50 p-2 rounded';
                    div.innerHTML = `
                        <span class="badge badge-outline badge-xs">v${v.version}</span>
                        <span class="text-gray-600">${v.creado_en}</span>
                    `;
                    desdeVersionesLista.appendChild(div);
                });
            } else {
                desdeVersionesLista.innerHTML = '<p class="text-xs text-gray-500 italic">Sin versiones</p>';
            }

            // Listar versiones del destino
            const haciaVersionesLista = document.getElementById('haciaVersionesLista');
            haciaVersionesLista.innerHTML = '';
            if (hacia.versiones.length > 0) {
                hacia.versiones.forEach(v => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between text-xs bg-gray-50 p-2 rounded';
                    div.innerHTML = `
                        <span class="badge badge-outline badge-xs">v${v.version}</span>
                        <span class="text-gray-600">${v.creado_en}</span>
                    `;
                    haciaVersionesLista.appendChild(div);
                });
            } else {
                haciaVersionesLista.innerHTML = '<p class="text-xs text-gray-500 italic">Sin versiones</p>';
            }

            // Configurar botones de acción (usar template string con ID del proyecto desde Blade)
            const proyectoId = '{{ $proyecto->id }}';
            document.getElementById('verOrigenBtn').href = `/proyectos/${proyectoId}/elementos/${desde.id}/relaciones`;
            document.getElementById('verDestinoBtn').href = `/proyectos/${proyectoId}/elementos/${hacia.id}/relaciones`;

            // Mostrar modal
            modal.showModal();
        }

        function getBadgeType(tipo) {
            const badges = {
                'DOCUMENTO': 'info',
                'CODIGO': 'success',
                'SCRIPT_BD': 'warning',
                'CONFIGURACION': 'secondary',
                'OTRO': 'ghost'
            };
            return badges[tipo] || 'ghost';
        }

        function getBadgeEstado(estado) {
            const badges = {
                'LIBERADO': 'success',
                'APROBADO': 'info',
                'EN_REVISION': 'warning',
                'BORRADOR': 'ghost',
                'OBSOLETO': 'error'
            };
            return badges[estado] || 'ghost';
        }        async function verDetallesCommit(commitId) {
            const modal = document.getElementById('modalCommit');
            const loading = document.getElementById('commitLoading');
            const content = document.getElementById('commitContent');
            const error = document.getElementById('commitError');

            // Resetear estado
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            error.classList.add('hidden');

            // Abrir modal
            modal.showModal();

            try {
                const response = await fetch(`/proyectos/commits/${commitId}/detalles`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Error al obtener datos');
                }

                const commit = data.commit;

                // Llenar información
                document.getElementById('commitHash').textContent = commit.hash;
                document.getElementById('commitAutor').textContent = commit.autor;
                document.getElementById('commitEmail').textContent = commit.autor_email || 'N/A';
                document.getElementById('commitFecha').textContent = new Date(commit.fecha).toLocaleString('es-ES');
                document.getElementById('commitMensaje').textContent = commit.mensaje;
                document.getElementById('commitArchivos').textContent = commit.archivos_modificados;
                document.getElementById('commitAdiciones').textContent = `+${commit.stats.additions}`;
                document.getElementById('commitEliminaciones').textContent = `-${commit.stats.deletions}`;
                document.getElementById('commitUrlGithub').href = commit.url;

                // Lista de archivos
                const archivosLista = document.getElementById('commitArchivosLista');
                archivosLista.innerHTML = '';

                if (commit.archivos && commit.archivos.length > 0) {
                    commit.archivos.forEach(archivo => {
                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between py-2 border-b border-gray-200 last:border-0';
                        div.innerHTML = `
                            <span class="text-sm font-mono">${archivo.nombre}</span>
                            <div class="flex gap-2 text-xs">
                                <span class="text-success">+${archivo.additions}</span>
                                <span class="text-error">-${archivo.deletions}</span>
                            </div>
                        `;
                        archivosLista.appendChild(div);
                    });
                } else {
                    archivosLista.innerHTML = '<p class="text-sm text-gray-500 italic">Sin información de archivos</p>';
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');

            } catch (err) {
                console.error('Error:', err);
                document.getElementById('commitErrorMsg').textContent = err.message || 'Error al cargar los detalles del commit';
                loading.classList.add('hidden');
                error.classList.remove('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>
