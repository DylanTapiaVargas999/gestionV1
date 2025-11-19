{{-- Progreso por fases con indicador visual --}}
<div class="bg-white rounded-lg border border-gray-200 mb-6">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">PROGRESO POR FASES</h3>

        <div class="space-y-4">
            @foreach($fases as $index => $fase)
                @php
                    $progreso = $progresoPorFase[$fase->id_fase];
                    $esFaseActual = $faseActual && $faseActual->id_fase === $fase->id_fase;
                    $faseCompletada = $progreso['fase_completada'];
                    $porcentaje = $progreso['porcentaje'];
                @endphp

                <div class="relative">
                    {{-- Línea conectora (excepto para la última fase) --}}
                    @if($index < $fases->count() - 1)
                        <div class="absolute left-6 top-12 w-0.5 h-8 {{ $faseCompletada ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                    @endif

                    <div class="flex items-start gap-4 p-4 rounded-lg {{ $esFaseActual ? 'bg-blue-50 border border-blue-200' : ($faseCompletada ? 'bg-white border border-green-200' : 'bg-white border border-gray-200') }}">
                        {{-- Icono de estado --}}
                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $faseCompletada ? 'bg-green-100 text-green-600' : ($esFaseActual ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600') }}">
                            @if($faseCompletada)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <span class="text-lg font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>

                        {{-- Contenido de la fase --}}
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-base font-bold text-gray-900">{{ $fase->nombre_fase }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold {{ $faseCompletada ? 'text-green-600' : ($esFaseActual ? 'text-blue-600' : 'text-gray-600') }}">
                                        {{ $porcentaje }}%
                                    </span>
                                    @if($esFaseActual)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">En Progreso</span>
                                    @elseif($faseCompletada)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Completada</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <p class="text-sm text-gray-600 mb-3">{{ $fase->descripcion }}</p>

                            {{-- Barra de progreso --}}
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="h-2 rounded-full {{ $faseCompletada ? 'bg-green-500' : ($esFaseActual ? 'bg-blue-500' : 'bg-gray-400') }}"
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>

                            {{-- Información adicional --}}
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">
                                    {{ $progreso['completadas'] }}/{{ $progreso['total'] }} actividades
                                </span>
                                @if($esFaseActual || !$faseCompletada)
                                    <a href="{{ route('cascada.ver-fase', [$proyecto, $fase]) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Ver detalles →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
