<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard CCB - {{ $proyecto->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">

                    <div class="mb-4">
                        <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold mb-4 text-gray-900">
                        Acceso Restringido al CCB
                    </h3>

                    <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                        No tienes permisos para acceder al Dashboard del Comité de Control de Cambios de este proyecto.
                    </p>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-left max-w-2xl mx-auto">
                        <h4 class="font-semibold mb-3 text-yellow-800">¿Por qué no puedo acceder?</h4>
                        <ul class="list-disc list-inside space-y-2 text-sm text-yellow-700">
                            <li>Solo los miembros del CCB pueden acceder al dashboard</li>
                            <li>El líder del proyecto también tiene acceso completo</li>
                            <li>Los miembros del CCB son designados por el líder del proyecto</li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left max-w-2xl mx-auto">
                        <h4 class="font-semibold mb-3 text-blue-800">¿Qué puedes hacer?</h4>
                        <ul class="list-disc list-inside space-y-2 text-sm text-blue-700">
                            @php
                                $equipoLider = $proyecto->equipos()->whereNotNull('lider_id')->first();
                                $liderProyecto = $equipoLider ? $equipoLider->lider : null;
                            @endphp
                            @if($liderProyecto)
                                <li>Contacta al líder del proyecto: <strong>{{ $liderProyecto->nombre_completo }}</strong></li>
                            @else
                                <li>Contacta al líder del equipo del proyecto</li>
                            @endif
                            <li>Pídele que te agregue como miembro del CCB</li>
                            <li>El líder puede configurar el CCB desde: <a href="{{ route('proyectos.ccb.configurar', $proyecto) }}" class="underline">Configurar CCB</a></li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        @if($liderProyecto)
                            <div class="text-sm text-gray-500 mb-4">
                                Líder del proyecto: {{ $liderProyecto->nombre_completo }}
                            </div>
                        @endif

                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-primary">
                                ← Volver al Proyecto
                            </a>

                            @if($proyecto->esLider(Auth::id()))
                                <a href="{{ route('proyectos.ccb.configurar', $proyecto) }}" class="btn btn-secondary">
                                    Configurar CCB
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
