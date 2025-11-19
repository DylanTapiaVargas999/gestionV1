{{-- Métricas del proyecto Cascada --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    {{-- Fase Actual --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">FASE ACTUAL</p>
                <p class="text-xl font-bold text-gray-900">{{ $faseActual->nombre_fase ?? 'Sin definir' }}</p>
            </div>
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Progreso General --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">PROGRESO GENERAL</p>
                @php
                    // Calcular progreso promedio de todas las fases
                    $totalFases = $fases->count();

                    if ($totalFases > 0) {
                        $sumaPorcentajes = 0;
                        foreach ($progresoPorFase as $progreso) {
                            $sumaPorcentajes += $progreso['porcentaje'];
                        }
                        $progresoGeneral = round($sumaPorcentajes / $totalFases);
                    } else {
                        $progresoGeneral = 0;
                    }
                @endphp
                <p class="text-2xl font-bold text-gray-900">{{ $progresoGeneral }}%</p>
            </div>
            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progresoGeneral }}%"></div>
            </div>
        </div>
    </div>

    {{-- Duración Total --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">DURACIÓN TOTAL</p>
                <p class="text-2xl font-bold text-gray-900">{{ $duracionTotal }} días</p>
            </div>
            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Total Tareas --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">TOTAL TAREAS</p>
                <p class="text-2xl font-bold text-gray-900">{{ $tareas->count() }}</p>
            </div>
            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>
</div>
