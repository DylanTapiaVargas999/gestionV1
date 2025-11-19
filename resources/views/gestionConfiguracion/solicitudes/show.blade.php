<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Solicitud de Cambio: {{ $solicitud->titulo }}
                </h2>
                <p class="text-sm text-gray-700 mt-1">
                    {{ $proyecto->codigo }} - {{ $proyecto->nombre }}
                </p>
            </div>
            <span class="badge badge-lg
                @if($solicitud->estado === 'ABIERTA') badge-info
                @elseif($solicitud->estado === 'EN_REVISION') badge-warning
                @elseif($solicitud->estado === 'APROBADA') badge-success
                @elseif($solicitud->estado === 'RECHAZADA') badge-error
                @elseif($solicitud->estado === 'IMPLEMENTADA') badge-primary
                @else badge-ghost
                @endif">
                {{ str_replace('_', ' ', $solicitud->estado) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Columna principal --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Información de la solicitud --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-800">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold">Detalles de la Solicitud</h3>
                                <span class="badge
                                    @if($solicitud->prioridad === 'CRITICA') badge-error
                                    @elseif($solicitud->prioridad === 'ALTA') badge-warning
                                    @elseif($solicitud->prioridad === 'MEDIA') badge-info
                                    @else badge-ghost
                                    @endif">
                                    Prioridad: {{ $solicitud->prioridad }}
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="font-semibold text-sm text-gray-700">Descripción del Cambio:</label>
                                    <p class="mt-1 text-gray-900">{{ $solicitud->descripcion_cambio }}</p>
                                </div>

                                <div>
                                    <label class="font-semibold text-sm text-gray-700">Motivo / Justificación:</label>
                                    <p class="mt-1 text-gray-900">{{ $solicitud->motivo_cambio }}</p>
                                </div>

                                @if($solicitud->resumen_impacto)
                                    <div>
                                        <label class="font-semibold text-sm text-gray-700">Resumen de Impacto:</label>
                                        <pre class="mt-1 text-sm bg-gray-50 p-3 rounded border">{{ $solicitud->resumen_impacto }}</pre>
                                    </div>
                                @endif

                                @if($solicitud->origen_cambio)
                                    <div>
                                        <label class="font-semibold text-sm text-gray-700">Origen del Cambio:</label>
                                        <p class="mt-1 text-gray-900">{{ $solicitud->origen_cambio }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Elementos de configuración afectados --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-800">
                            <h3 class="text-lg font-semibold mb-4">Elementos de Configuración Afectados</h3>

                            <div class="space-y-3">
                                @foreach($solicitud->items as $item)
                                    <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-semibold">{{ $item->elementoConfiguracion->codigo_ec }}</div>
                                                <div class="text-sm text-gray-700">{{ $item->elementoConfiguracion->titulo }}</div>
                                            </div>
                                            <span class="badge">{{ $item->elementoConfiguracion->tipo }}</span>
                                        </div>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-700">Versión actual:</span>
                                            <span class="font-mono">{{ $item->versionActual?->version ?? 'Sin versión' }}</span>
                                        </div>
                                        @if($item->nota)
                                            <div class="mt-2 text-sm text-gray-700">
                                                <strong>Nota:</strong> {{ $item->nota }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Votaciones (solo si está en revisión) --}}
                    @if($solicitud->estado === 'EN_REVISION' && $solicitud->votos->isNotEmpty())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-800">
                                <h3 class="text-lg font-semibold mb-4">Votaciones del CCB</h3>

                                <div class="space-y-3">
                                    @foreach($solicitud->votos as $voto)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded text-gray-800">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                    <span class="text-xs">{{ substr($voto->usuario->nombre_completo, 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold">{{ $voto->usuario->nombre_completo }}</div>
                                                <div class="text-sm text-gray-600">{{ $voto->votado_en->format('d/m/Y H:i') }}</div>
                                                @if($voto->comentario)
                                                    <div class="text-sm mt-1">{{ $voto->comentario }}</div>
                                                @endif
                                            </div>
                                            <span class="badge {{ $voto->voto_badge }}">
                                                {{ $voto->voto_icono }} {{ $voto->voto }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Columna lateral --}}
                <div class="space-y-6">

                    {{-- Información del solicitante --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-800">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Solicitante</h3>
                            <div class="flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="bg-neutral text-neutral-content rounded-full w-12">
                                        <span>{{ substr($solicitud->solicitante->nombre_completo, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $solicitud->solicitante->nombre_completo }}</div>
                                    <div class="text-sm text-gray-600">{{ $solicitud->solicitante->correo }}</div>
                                </div>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Creada: {{ $solicitud->creado_en->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Estadísticas de votación --}}
                    @if($solicitud->estado === 'EN_REVISION' && $ccb)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-800">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Progreso de Votación</h3>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span>Votos emitidos:</span>
                                        <span class="font-semibold">{{ $estadisticasVotos['votos_emitidos'] }} / {{ $estadisticasVotos['total_miembros'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span>Quorum necesario:</span>
                                        <span class="font-semibold">{{ $estadisticasVotos['quorum'] }}</span>
                                    </div>
                                    <div class="divider my-2"></div>
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span>Aprobar:</span>
                                        <span class="font-semibold">{{ $estadisticasVotos['aprobar'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-red-600">
                                        <span>Rechazar:</span>
                                        <span class="font-semibold">{{ $estadisticasVotos['rechazar'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-orange-600">
                                        <span>Abstenerse:</span>
                                        <span class="font-semibold">{{ $estadisticasVotos['abstenerse'] }}</span>
                                    </div>
                                </div>

                                {{-- Barra de progreso --}}
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($estadisticasVotos['votos_emitidos'] / max(1, $estadisticasVotos['total_miembros'])) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Acciones</h3>

                            <div class="space-y-2">
                                {{-- Evaluar impacto --}}
                                @if(in_array($solicitud->estado, ['ABIERTA', 'EN_REVISION']))
                                    <a href="{{ route('proyectos.solicitudes.evaluar-impacto', [$proyecto, $solicitud]) }}"
                                       class="btn btn-sm btn-outline w-full text-gray-900 bg-white border-gray-300 hover:bg-gray-100">
                                        Evaluar Impacto
                                    </a>
                                @endif

                                {{-- Enviar al CCB --}}
                                @if($solicitud->estado === 'ABIERTA')
                                    <form action="{{ route('proyectos.solicitudes.enviar-ccb', [$proyecto, $solicitud]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm w-full text-gray-900 bg-white border-gray-300 hover:bg-gray-100">
                                            Enviar al CCB
                                        </button>
                                    </form>
                                @endif

                                {{-- Votar (solo miembros CCB) --}}
                                @if($esMiembroCCB && $solicitud->estado === 'EN_REVISION' && !$yaVoto)
                                    <a href="{{ route('proyectos.solicitudes.votar', [$proyecto, $solicitud]) }}" class="btn btn-sm btn-success w-full">
                                        Emitir Voto
                                    </a>
                                @elseif($esMiembroCCB && $solicitud->estado === 'EN_REVISION' && $yaVoto)
                                    <div class="text-center py-2 px-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700">
                                        Ya has emitido tu voto en esta solicitud
                                    </div>
                                @elseif(!$esMiembroCCB)
                                    <div class="text-center py-2 px-4 bg-gray-50 border border-gray-200 rounded text-sm text-gray-600">
                                        No eres miembro del CCB de este proyecto
                                    </div>
                                    <div class="text-xs bg-blue-50 border border-blue-200 rounded p-2 mt-2">
                                        <strong>Solución:</strong> Accede a <code class="bg-blue-100 px-1 rounded">/debug-ccb</code> para agregarte como miembro del CCB
                                    </div>
                                @endif

                                {{-- Implementar (solo si aprobada) --}}
                                @if($solicitud->estado === 'APROBADA')
                                    <form action="{{ route('proyectos.solicitudes.implementar', [$proyecto, $solicitud]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm w-full text-gray-900 bg-white border-gray-300 hover:bg-gray-100"
                                                onclick="return confirm('¿Estás seguro? Se crearán nuevas versiones de los EC afectados')">
                                            Implementar Cambios
                                        </button>
                                    </form>
                                @endif

                                {{-- Cerrar --}}
                                @if(in_array($solicitud->estado, ['RECHAZADA', 'IMPLEMENTADA']))
                                    <form action="{{ route('proyectos.solicitudes.cerrar', [$proyecto, $solicitud]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost w-full text-gray-900 bg-white hover:bg-gray-100 border-gray-200">
                                            Cerrar Solicitud
                                        </button>
                                    </form>
                                @endif

                                <div class="divider"></div>

                                <a href="{{ route('proyectos.solicitudes.index', $proyecto) }}"
                                   class="btn btn-sm btn-ghost w-full text-gray-900 bg-white hover:bg-gray-100 border-gray-200">
                                    ← Volver
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>



</x-app-layout>
