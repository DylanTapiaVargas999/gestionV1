<x-app-layout>
    <div class="py-8 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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
                <span class="text-gray-900 font-medium">Nueva Solicitud de Cambio</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Nueva Solicitud de Cambio</h1>
                <p class="text-gray-600 mt-1">Describe el cambio que deseas solicitar al CCB</p>
            </div>

            <form action="{{ route('proyectos.solicitudes.store', $proyecto) }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">

                        <!-- Sección 1 -->
                        <div class="mb-8 bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    1
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Información General</h3>
                            </div>
                            <p class="text-sm text-gray-600 pl-12">
                                Describe el cambio que deseas solicitar al CCB
                            </p>
                        </div>

                        {{-- Origen del cambio (opcional) --}}
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Origen del Cambio (opcional)
                            </label>
                            <input type="text" name="origen_cambio" value="{{ old('origen_cambio') }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                   placeholder="Ej: Impedimento #123, Requerimiento del cliente, etc."
                                   maxlength="500">
                            <p class="mt-1 text-sm text-gray-500">
                                Describe el origen o contexto que motiva este cambio (opcional)
                            </p>
                        </div>

                        {{-- Título --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Título de la Solicitud *
                            </label>
                            <input type="text" name="titulo" value="{{ old('titulo') }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('titulo') border-red-500 @enderror"
                                   placeholder="Ej: Agregar campo email a módulo de usuarios"
                                   required>
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción del cambio --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción del Cambio *
                            </label>
                            <textarea name="descripcion_cambio" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('descripcion_cambio') border-red-500 @enderror"
                                      placeholder="Describe en detalle qué cambios se requieren..."
                                      required>{{ old('descripcion_cambio') }}</textarea>
                            @error('descripcion_cambio')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Motivo del cambio --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo / Justificación *
                            </label>
                            <textarea name="motivo_cambio" rows="3"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('motivo_cambio') border-red-500 @enderror"
                                      placeholder="¿Por qué es necesario este cambio?"
                                      required>{{ old('motivo_cambio') }}</textarea>
                            @error('motivo_cambio')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prioridad --}}
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridad *
                            </label>
                            <select name="prioridad" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('prioridad') border-red-500 @enderror" required>
                                <option value="">Seleccionar...</option>
                                <option value="BAJA" {{ old('prioridad') === 'BAJA' ? 'selected' : '' }}>Baja - Puede esperar</option>
                                <option value="MEDIA" {{ old('prioridad') === 'MEDIA' ? 'selected' : '' }}>Media - Importante</option>
                                <option value="ALTA" {{ old('prioridad') === 'ALTA' ? 'selected' : '' }}>Alta - Urgente</option>
                                <option value="CRITICA" {{ old('prioridad') === 'CRITICA' ? 'selected' : '' }}>Crítica - Bloquea funcionalidad</option>
                            </select>
                            @error('prioridad')
                                <label class="label">
                                    <span class="label-text-alt text-red-500 font-medium">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Elementos de Configuración Afectados --}}
                        <div class="mb-6 bg-orange-50 p-5 rounded-lg border border-orange-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    2
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Elementos de Configuración Afectados</h3>
                            </div>
                            <p class="text-sm text-gray-600 pl-12">
                                Selecciona los EC que serán modificados por este cambio
                            </p>
                        </div>

                        <div id="elementos-container" class="space-y-4">
                            {{-- Se llenarán dinámicamente con JavaScript --}}
                        </div>

                        <button type="button" onclick="agregarElemento()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar Elemento de Configuración
                        </button>

                        @error('elementos')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Botones --}}
                        <div class="flex justify-between items-center">
                            <a href="{{ route('proyectos.solicitudes.index', $proyecto) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                                Continuar al Paso 2
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Datos para JavaScript -->
    <div id="elementos-data" data-elementos="{{ json_encode($elementos) }}" class="hidden"></div>

    <script>
        const elementosData = document.getElementById('elementos-data');
        const elementosDisponibles = JSON.parse(elementosData.getAttribute('data-elementos'));
        let contadorElementos = 0;

        function agregarElemento() {
            const container = document.getElementById('elementos-container');
            const index = contadorElementos++;

            const div = document.createElement('div');
            div.className = 'bg-white border-2 border-gray-200 rounded-lg p-5 hover:border-gray-300 transition';
            div.id = `elemento-${index}`;

            div.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <h4 class="font-semibold text-gray-900">Elemento ${index + 1}</h4>
                    <button type="button" onclick="eliminarElemento(${index})" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Elemento de Configuración *
                    </label>
                    <select name="elementos[${index}][ec_id]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" required>
                        <option value="">Seleccionar EC...</option>
                        ${elementosDisponibles.map(ec => `
                            <option value="${ec.id}">
                                ${ec.codigo_ec} - ${ec.titulo} (${ec.tipo}) - v${ec.version_actual ? ec.version_actual.version : '0.0.0'}
                            </option>
                        `).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notas sobre el cambio en este EC
                    </label>
                    <textarea name="elementos[${index}][nota]" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                              placeholder="Detalles específicos del cambio en este elemento..."></textarea>
                </div>
            `;

            container.appendChild(div);
        }        function eliminarElemento(index) {
            const elemento = document.getElementById(`elemento-${index}`);
            if (elemento) {
                elemento.remove();
            }
        }

        // Agregar un elemento al cargar la página
        window.addEventListener('DOMContentLoaded', () => {
            agregarElemento();

            // Debug: mostrar información en consola
            console.log('Elementos disponibles:', elementosDisponibles);
        });
    </script>
</x-app-layout>
