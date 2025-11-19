{{-- Cronología del proyecto (Inicio, Hoy, Fin) --}}
@if($fechaInicioProyecto && $fechaFinProyecto)
<div class="bg-white rounded-lg border border-gray-200 mb-6 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">CRONOLOGÍA DEL PROYECTO</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Inicio --}}
        <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <span class="text-green-600 text-sm font-bold">IN</span>
            </div>
            <p class="text-sm font-medium text-gray-700">INICIO</p>
            <p class="font-bold text-lg text-gray-900">{{ \Carbon\Carbon::parse($fechaInicioProyecto)->format('d/m/Y') }}</p>
        </div>

        {{-- Hoy --}}
        <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <span class="text-blue-600 text-sm font-bold">HO</span>
            </div>
            <p class="text-sm font-medium text-gray-700">HOY</p>
            <p class="font-bold text-lg text-gray-900">{{ now()->format('d/m/Y') }}</p>
        </div>

        {{-- Fin --}}
        <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <span class="text-red-600 text-sm font-bold">FI</span>
            </div>
            <p class="text-sm font-medium text-gray-700">FIN PLANIFICADO</p>
            <p class="font-bold text-lg text-gray-900">{{ \Carbon\Carbon::parse($fechaFinProyecto)->format('d/m/Y') }}</p>
        </div>
    </div>
</div>
@endif
