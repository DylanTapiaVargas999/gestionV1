<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

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
                <span class="text-gray-900 font-medium">Emitir Voto</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Emitir Voto</h1>
                <p class="text-gray-600 mt-1">Solicitud: {{ $solicitud->titulo }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-8">

                    <!-- Información de la solicitud -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Detalles de la Solicitud</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Título:</strong> {{ $solicitud->titulo }}</p>
                            <p><strong>Descripción:</strong> {{ Str::limit($solicitud->descripcion_cambio, 100) }}</p>
                            <p><strong>Prioridad:</strong>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($solicitud->prioridad === 'CRITICA') bg-red-100 text-red-800
                                    @elseif($solicitud->prioridad === 'ALTA') bg-orange-100 text-orange-800
                                    @elseif($solicitud->prioridad === 'MEDIA') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $solicitud->prioridad }}
                                </span>
                            </p>
                            @if($solicitud->origen_cambio)
                                <p><strong>Origen:</strong> {{ $solicitud->origen_cambio }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Formulario de votación -->
                    <form action="{{ route('proyectos.solicitudes.votar', [$proyecto, $solicitud]) }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Tu Voto *
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-4 border border-green-200 rounded-lg cursor-pointer hover:bg-green-50 bg-green-50">
                                    <input type="radio" name="voto" value="APROBAR" class="radio radio-success" required>
                                    <div>
                                        <div class="font-semibold text-green-900">Aprobar</div>
                                        <div class="text-sm text-green-700">Estoy de acuerdo con implementar este cambio</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-4 border border-red-200 rounded-lg cursor-pointer hover:bg-red-50">
                                    <input type="radio" name="voto" value="RECHAZAR" class="radio radio-error" required>
                                    <div>
                                        <div class="font-semibold text-red-900">Rechazar</div>
                                        <div class="text-sm text-red-700">No estoy de acuerdo con este cambio</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-4 border border-yellow-200 rounded-lg cursor-pointer hover:bg-yellow-50">
                                    <input type="radio" name="voto" value="ABSTENERSE" class="radio radio-warning" required>
                                    <div>
                                        <div class="font-semibold text-yellow-900">Abstenerse</div>
                                        <div class="text-sm text-yellow-700">No tengo suficiente información para decidir</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Comentario (opcional)
                            </label>
                            <textarea name="comentario" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                      placeholder="Justifica tu voto..."></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('proyectos.solicitudes.show', [$proyecto, $solicitud]) }}"
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                ← Cancelar
                            </a>

                            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Confirmar Voto
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
