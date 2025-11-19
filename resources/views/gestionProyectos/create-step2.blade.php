<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <div class="text-sm breadcrumbs">
                    <ul class="text-gray-600">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a></li>
                        <li><a href="{{ route('proyectos.create') }}" class="hover:text-gray-900">Nuevo Proyecto</a></li>
                        <li class="text-gray-900 font-medium">Plantillas EC</li>
                    </ul>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Proyecto</h1>
                <p class="mt-2 text-gray-600">Paso 2 de 4: Seleccionar Elementos de Configuración</p>
            </div>

            <!-- Progress Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 - Completed -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Paso 1</div>
                            <div class="text-xs text-gray-500">Datos del Proyecto</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                    <!-- Step 2 - Current -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                            <span class="text-lg font-semibold">2</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-black">Paso 2</div>
                            <div class="text-xs text-gray-500">Plantillas EC</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

                    <!-- Step 3 - Pending -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600">
                            <span class="text-lg font-semibold">3</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-400">Paso 3</div>
                            <div class="text-xs text-gray-500">Configurar Equipo</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

                    <!-- Step 4 - Pending -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600">
                            <span class="text-lg font-semibold">4</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-400">Paso 4</div>
                            <div class="text-xs text-gray-500">Revisión</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Summary Card -->
            <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm mb-6">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">{{ $proyectoData['nombre'] }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $proyectoData['descripcion'] }}</p>
                            <div class="flex gap-4 mt-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary">{{ $proyectoData['metodologia_nombre'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($proyectoData['fecha_inicio'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($proyectoData['fecha_fin'])->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Elementos de Configuración sugeridos para {{ $proyectoData['metodologia_nombre'] }}</h2>
                        <p class="text-sm text-gray-600 mt-2">Selecciona los elementos que deseas crear. Cada uno incluye una tarea base asociada.</p>
                    </div>

                    @if($plantillas->isEmpty())
                        <div class="alert alert-warning bg-yellow-50 border border-yellow-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>No hay plantillas disponibles para esta metodología. Puedes continuar sin crear elementos de configuración.</span>
                        </div>
                    @else
                        <form action="{{ route('proyectos.store-step2') }}" method="POST" id="plantillasForm">
                            @csrf

                            <div class="space-y-4">
                                @foreach($plantillas as $plantilla)
                                    <div class="border-2 rounded-lg p-4 hover:border-blue-300 transition-colors {{ $plantilla->es_recomendado ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200' }}">
                                        <label class="flex items-start gap-4 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="plantillas[]"
                                                value="{{ $plantilla->id }}"
                                                {{ $plantilla->es_recomendado ? 'checked' : '' }}
                                                class="checkbox checkbox-primary mt-1"
                                            />
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-2xl">{{ $plantilla->tipo_icono }}</span>
                                                    <h4 class="text-lg font-semibold text-gray-900">{{ $plantilla->nombre }}</h4>
                                                    <span class="badge {{ $plantilla->tipo_badge }} badge-sm">{{ $plantilla->tipo }}</span>
                                                    @if($plantilla->es_recomendado)
                                                        <span class="badge badge-success badge-sm">Recomendado</span>
                                                    @endif
                                                </div>

                                                <p class="text-sm text-gray-600 mb-3">{{ $plantilla->descripcion }}</p>

                                                <div class="bg-white/70 rounded-lg p-3 mb-2">
                                                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="font-medium">Período aproximado:</span>
                                                        <span>{{ $plantilla->fecha_inicio_calculada->format('d/m/Y') }} - {{ $plantilla->fecha_fin_calculada->format('d/m/Y') }}</span>
                                                        <span class="text-xs text-gray-500">({{ $plantilla->fecha_inicio_calculada->diffInDays($plantilla->fecha_fin_calculada) }} días)</span>
                                                    </div>
                                                </div>

                                                @if($plantilla->tarea_nombre)
                                                    <div class="ml-4 pl-4 border-l-2 border-blue-300 bg-blue-50/50 rounded-r-lg p-3">
                                                        <div class="flex items-start gap-2">
                                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium text-blue-900">✓ Tarea incluida:</p>
                                                                <p class="text-sm text-blue-800 font-semibold mt-1">"{{ $plantilla->tarea_nombre }}"</p>
                                                                <p class="text-xs text-blue-700 mt-1">{{ $plantilla->tarea_descripcion }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Info adicional -->
                            <div class="alert alert-info bg-blue-50 border border-blue-200 mt-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm">
                                    <p class="font-medium">Nota importante:</p>
                                    <p class="mt-1">Puedes desmarcar los elementos que no necesites. También podrás crear más elementos de configuración después de finalizar el proyecto.</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-6">
                                <a
                                    href="{{ route('proyectos.create') }}"
                                    class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Volver
                                </a>
                                <button
                                    type="submit"
                                    class="btn bg-black text-white hover:bg-gray-800 border-0"
                                >
                                    Continuar al Paso 3
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    @endif

                    @if($plantillas->isEmpty())
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-6">
                            <a
                                href="{{ route('proyectos.create') }}"
                                class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Volver
                            </a>
                            <form action="{{ route('proyectos.store-step2') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn bg-black text-white hover:bg-gray-800 border-0"
                                >
                                    Omitir y Continuar
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
