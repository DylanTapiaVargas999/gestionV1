<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-4 flex items-center gap-2 text-sm">
                <a href="{{ route('proyectos.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Proyectos</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.show', $proyecto) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $proyecto->nombre }}</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('proyectos.cronograma.dashboard', $proyecto) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Cronograma Inteligente</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-700 font-semibold">Ajuste #{{ $ajuste->id }}</span>
            </div>

            <!-- Header -->
            <div class="bg-white border-2 border-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-200 rounded-xl p-6 mb-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($ajuste->estado == 'aplicado')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @elseif($ajuste->estado == 'rechazado')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h1 class="text-3xl font-bold text-gray-900 capitalize">Ajuste {{ $ajuste->estrategia }}</h1>
                                <span class="px-3 py-1 bg-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-600 text-white text-sm font-bold rounded-full uppercase">{{ $ajuste->estado }}</span>
                            </div>
                            <p class="text-sm text-gray-600">Ajuste #{{ $ajuste->id }} - {{ $ajuste->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        @if($ajuste->estado == 'propuesto')
                        <form action="{{ route('proyectos.cronograma.rechazar', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('¿Está seguro de rechazar este ajuste?')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-md transition">
                                ✕ Rechazar
                            </button>
                        </form>
                        <form action="{{ route('proyectos.cronograma.aprobar', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition">
                                ✓ Aprobar
                            </button>
                        </form>
                        @elseif($ajuste->estado == 'aprobado')
                        <form action="{{ route('proyectos.cronograma.aplicar', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('⚠️ Esta acción modificará las tareas del proyecto. ¿Está seguro de aplicar este ajuste?')" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg shadow-md transition">
                                🚀 Aplicar al Cronograma
                            </button>
                        </form>
                        @elseif($ajuste->estado == 'aplicado')
                        <form action="{{ route('proyectos.cronograma.revertir', ['proyecto' => $proyecto, 'ajuste' => $ajuste]) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('⚠️ Esta acción revertirá todas las modificaciones realizadas por este ajuste. ¿Está seguro?')" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg shadow-md transition">
                                ↶ Revertir
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('proyectos.cronograma.dashboard', $proyecto) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg border border-gray-300 transition">
                            ← Volver
                        </a>
                    </div>
                </div>

                <!-- Métricas del Ajuste -->
                <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t-2 border-{{ $ajuste->estado == 'propuesto' ? 'amber' : ($ajuste->estado == 'aprobado' ? 'blue' : ($ajuste->estado == 'aplicado' ? 'green' : 'red')) }}-100">
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Score de Solución</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $ajuste->score_solucion }}<span class="text-lg text-gray-600">/100</span></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Días Recuperados</p>
                        <p class="text-3xl font-bold text-green-700">+{{ $ajuste->dias_recuperados }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Recursos Afectados</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $ajuste->recursos_afectados }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Costo Estimado</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($ajuste->costo_estimado, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Motivo del Ajuste -->
            <div class="bg-white border-2 border-blue-200 rounded-xl p-6 mb-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Motivo del Ajuste</h2>
                </div>
                <p class="text-gray-800 font-medium pl-13">{{ $ajuste->motivo_ajuste }}</p>
                @if($ajuste->comentarios_aprobacion)
                <div class="mt-4 pt-4 border-t-2 border-blue-100">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Comentarios de {{ $ajuste->estado == 'rechazado' ? 'Rechazo' : 'Aprobación' }}:</p>
                    <p class="text-gray-800 italic">"{{ $ajuste->comentarios_aprobacion }}"</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $ajuste->aprobador->nombre ?? 'Sistema' }} - {{ $ajuste->fecha_aprobacion?->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            <!-- Cambios Propuestos -->
            <div class="bg-white border-2 border-purple-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b-2 border-purple-100">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Cambios en las Tareas</h2>
                        <p class="text-sm text-gray-600">{{ $ajuste->historialTareas->count() }} {{ $ajuste->historialTareas->count() == 1 ? 'tarea modificada' : 'tareas modificadas' }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($ajuste->historialTareas as $historial)
                    <div class="border-2 border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $historial->tarea->nombre }}</h3>
                                <p class="text-sm text-gray-600">{{ $historial->tarea->fase->nombre_fase ?? 'Sin fase' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-bold rounded-full uppercase">{{ $historial->tipo_cambio }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($historial->fecha_inicio_anterior != $historial->fecha_inicio_nueva)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">📅 Fecha Inicio</p>
                                <p class="text-sm line-through text-red-700 font-medium">{{ \Carbon\Carbon::parse($historial->fecha_inicio_anterior)->format('d/m/Y') }}</p>
                                <p class="text-base font-bold text-green-700">{{ \Carbon\Carbon::parse($historial->fecha_inicio_nueva)->format('d/m/Y') }}</p>
                            </div>
                            @endif

                            @if($historial->fecha_fin_anterior != $historial->fecha_fin_nueva)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">📅 Fecha Fin</p>
                                <p class="text-sm line-through text-red-700 font-medium">{{ \Carbon\Carbon::parse($historial->fecha_fin_anterior)->format('d/m/Y') }}</p>
                                <p class="text-base font-bold text-green-700">{{ \Carbon\Carbon::parse($historial->fecha_fin_nueva)->format('d/m/Y') }}</p>
                            </div>
                            @endif

                            @if($historial->horas_estimadas_anterior != $historial->horas_estimadas_nueva)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">⏱️ Horas Estimadas</p>
                                <p class="text-sm line-through text-red-700 font-medium">{{ $historial->horas_estimadas_anterior }}h</p>
                                <p class="text-base font-bold text-green-700">{{ $historial->horas_estimadas_nueva }}h</p>
                            </div>
                            @endif

                            @if($historial->responsable_anterior_id != $historial->responsable_nuevo_id)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">👤 Responsable</p>
                                <p class="text-sm line-through text-red-700 font-medium">{{ $historial->responsableAnteriorUsuario->nombre ?? 'Sin asignar' }}</p>
                                <p class="text-base font-bold text-green-700">{{ $historial->responsableNuevoUsuario->nombre ?? 'Sin asignar' }}</p>
                            </div>
                            @endif

                            @if($historial->prioridad_anterior != $historial->prioridad_nueva)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-1">🎯 Prioridad</p>
                                <p class="text-sm line-through text-red-700 font-medium capitalize">{{ $historial->prioridad_anterior }}</p>
                                <p class="text-base font-bold text-green-700 capitalize">{{ $historial->prioridad_nueva }}</p>
                            </div>
                            @endif
                        </div>

                        @if($historial->razon_cambio)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs font-semibold text-gray-600 mb-1">Razón del cambio:</p>
                            <p class="text-sm text-gray-800 italic">"{{ $historial->razon_cambio }}"</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
