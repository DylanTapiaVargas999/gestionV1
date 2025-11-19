<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumb -->
            <div class="mb-4 flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.show', $proyecto) }}" class="hover:text-gray-900">{{ $proyecto->nombre }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $solicitud]) }}" class="hover:text-gray-900">Solicitud</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">Evaluación de Impacto</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Evaluación de Impacto</h1>
                <p class="text-gray-600 mt-1">{{ $solicitud->titulo }}</p>
            </div>

            {{-- Resumen de impacto --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-8">

                    <!-- Sección 1 -->
                    <div class="mb-8 bg-blue-50 p-5 rounded-lg border border-blue-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                1
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Análisis de Impacto</h3>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">
                            Resumen del impacto estimado del cambio solicitado
                        </p>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Resumen Ejecutivo</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($analisisImpacto['nivel_impacto'] === 'CRITICO') bg-red-100 text-red-800
                            @elseif($analisisImpacto['nivel_impacto'] === 'ALTO') bg-orange-100 text-orange-800
                            @elseif($analisisImpacto['nivel_impacto'] === 'MEDIO') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            IMPACTO {{ $analisisImpacto['nivel_impacto'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-medium text-gray-600">EC Originales</div>
                            <div class="text-2xl font-bold text-blue-600">{{ count($analisisImpacto['ec_originales']) }}</div>
                            <div class="text-xs text-gray-500">A modificar</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-medium text-gray-600">Afectados Directos</div>
                            <div class="text-2xl font-bold text-orange-600">{{ count($analisisImpacto['ec_afectados_directos']) }}</div>
                            <div class="text-xs text-gray-500">Nivel 1</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-medium text-gray-600">Afectados Indirectos</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ count($analisisImpacto['ec_afectados_indirectos']) }}</div>
                            <div class="text-xs text-gray-500">Nivel 2+</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="text-sm font-medium text-gray-600">Total Afectados</div>
                            <div class="text-2xl font-bold text-purple-600">{{ $analisisImpacto['total_afectados'] }}</div>
                            <div class="text-xs text-gray-500">En cascada</div>
                        </div>
                    </div>

                    {{-- Recomendaciones --}}
                    @if(!empty($analisisImpacto['recomendaciones']))
                        <div class="mb-6 p-4 rounded-lg border
                            @if($analisisImpacto['nivel_impacto'] === 'CRITICO') bg-red-50 border-red-200
                            @elseif($analisisImpacto['nivel_impacto'] === 'ALTO') bg-orange-50 border-orange-200
                            @elseif($analisisImpacto['nivel_impacto'] === 'MEDIO') bg-blue-50 border-blue-200
                            @else bg-green-50 border-green-200
                            @endif">
                            <div class="flex items-start gap-3 text-gray-800">
                                <svg class="w-5 h-5 text-current mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-2 text-gray-900">Recomendaciones:</h4>
                                    <ul class="text-sm space-y-1 text-gray-700">
                                        @foreach($analisisImpacto['recomendaciones'] as $recomendacion)
                                            <li class="flex items-start gap-2">
                                                <span class="text-gray-700 mt-1">•</span>
                                                <span class="text-gray-700">{{ $recomendacion }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Advertencia de dependencias circulares --}}
                    @if(!empty($analisisImpacto['dependencias_circulares']))
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-yellow-900">Dependencias Circulares Detectadas</h4>
                                    <p class="text-sm text-yellow-800">Se encontraron {{ count($analisisImpacto['dependencias_circulares']) }} ciclo(s) de dependencias. Revisa la estructura.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Grafo de impacto --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-8">

                    <!-- Sección 2 -->
                    <div class="mb-8 bg-orange-50 p-5 rounded-lg border border-orange-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                2
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Visualización de Impacto</h3>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">
                            Grafo interactivo mostrando las relaciones de dependencia
                        </p>
                    </div>

                    <div id="grafo-impacto" style="height: 500px; border: 1px solid #e5e7eb; border-radius: 0.5rem;"></div>

                    <div class="mt-4 flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-red-500"></div>
                            <span class="text-gray-700">EC a Modificar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-orange-500"></div>
                            <span class="text-gray-700">Afectados Directos</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                            <span class="text-gray-700">Afectados Indirectos</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalles de EC afectados --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- EC afectados directos --}}
                @if(!empty($analisisImpacto['ec_afectados_directos']))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                        <div class="p-8">

                            <!-- Sección 3 -->
                            <div class="mb-6 bg-green-50 p-5 rounded-lg border border-green-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        3
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">EC Afectados Directamente</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-13">
                                    Elementos que serán impactados directamente por el cambio
                                </p>
                            </div>

                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($analisisImpacto['ec_afectados_directos'] as $ec)
                                    <div class="border rounded-lg p-4 bg-orange-50 border-orange-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $ec['codigo'] }}</div>
                                                <div class="text-sm text-gray-600">{{ $ec['titulo'] }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $ec['tipo'] }}</span>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        @if($ec['criticidad'] === 'CRITICA') bg-red-100 text-red-800
                                                        @elseif($ec['criticidad'] === 'ALTA') bg-orange-100 text-orange-800
                                                        @elseif($ec['criticidad'] === 'MEDIA') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ $ec['criticidad'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600 mb-1">
                                            <span class="font-medium">Relación:</span> {{ str_replace('_', ' ', $ec['tipo_relacion']) }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Estado:</span> {{ $ec['estado'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- EC afectados indirectos --}}
                @if(!empty($analisisImpacto['ec_afectados_indirectos']))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                        <div class="p-8">

                            <!-- Sección 4 -->
                            <div class="mb-6 bg-purple-50 p-5 rounded-lg border border-purple-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        4
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">EC Afectados Indirectamente</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-13">
                                    Elementos que serán impactados en cascada
                                </p>
                            </div>

                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($analisisImpacto['ec_afectados_indirectos'] as $ec)
                                    <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $ec['codigo'] }}</div>
                                                <div class="text-sm text-gray-600">{{ $ec['titulo'] }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $ec['tipo'] }}</span>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Nivel {{ $ec['nivel'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600 mb-1">
                                            <span class="font-medium">Estado:</span> {{ $ec['estado'] }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <span class="font-medium">Criticidad:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($ec['criticidad'] === 'CRITICA') bg-red-100 text-red-800
                                                @elseif($ec['criticidad'] === 'ALTA') bg-orange-100 text-orange-800
                                                @elseif($ec['criticidad'] === 'MEDIA') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $ec['criticidad'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Botones de acción --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $solicitud]) }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver a la Solicitud
                        </a>

                        @if($solicitud->estado === 'ABIERTA')
                            <form action="{{ route('proyectos.solicitudes.enviar-ccb', [$proyecto, $solicitud]) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Enviar al CCB con este Análisis
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Script del grafo --}}
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('grafo-impacto');
            const data = @json($analisisImpacto['grafo_impacto']);

            const options = {
                nodes: {
                    shape: 'box',
                    margin: 10,
                    widthConstraint: {
                        maximum: 200
                    },
                    font: {
                        size: 12,
                        face: 'monospace'
                    }
                },
                edges: {
                    arrows: {
                        to: {
                            enabled: true,
                            scaleFactor: 0.5
                        }
                    },
                    smooth: {
                        type: 'cubicBezier',
                        forceDirection: 'horizontal'
                    },
                    font: {
                        size: 10,
                        align: 'middle'
                    }
                },
                layout: {
                    hierarchical: {
                        enabled: true,
                        direction: 'LR',
                        sortMethod: 'directed',
                        levelSeparation: 200,
                        nodeSpacing: 100
                    }
                },
                physics: {
                    enabled: false
                }
            };

            const network = new vis.Network(container, data, options);

            network.on('click', function(params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    const node = data.nodes.find(n => n.id === nodeId);
                    if (node) {
                        alert(`${node.label}\n\n${node.title || 'Sin detalles'}`);
                    }
                }
            });
        });
    </script>
</x-app-layout>
