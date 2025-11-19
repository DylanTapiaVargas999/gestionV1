<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
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
                <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="hover:text-gray-900">Elementos</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">Editar {{ $elemento->codigo_ec }}</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Editar Elemento de Configuración</h1>
                <p class="text-gray-600 mt-1">
                    Código: <span class="font-mono font-bold bg-gray-100 px-2 py-1 rounded">{{ $elemento->codigo_ec }}</span>
                </p>
            </div>

            <form action="{{ route('proyectos.elementos.update', [$proyecto, $elemento]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-8">

                        <!-- Sección 1 -->
                        <div class="mb-8 bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    1
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Información Básica</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                Modifica la información principal del elemento de configuración
                            </p>
                        </div>

                        {{-- Código EC (solo lectura) --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Código EC
                            </label>
                            <input
                                type="text"
                                value="{{ $elemento->codigo_ec }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                                disabled
                            />
                            <p class="mt-1 text-sm text-gray-500">El código EC no se puede modificar</p>
                        </div>

                        {{-- Título --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Título del Elemento *
                            </label>
                            <input
                                type="text"
                                name="titulo"
                                value="{{ old('titulo', $elemento->titulo) }}"
                                placeholder="Título del elemento"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('titulo') border-red-500 @enderror"
                                required
                            />
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción *
                            </label>
                            <textarea name="descripcion" rows="4"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('descripcion') border-red-500 @enderror"
                                      placeholder="Descripción del elemento..."
                                      required>{{ old('descripcion', $elemento->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipo --}}
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Elemento *
                            </label>
                            <select name="tipo" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('tipo') border-red-500 @enderror" required>
                                <option value="DOCUMENTO" {{ old('tipo', $elemento->tipo) === 'DOCUMENTO' ? 'selected' : '' }}>📄 Documento</option>
                                <option value="CODIGO" {{ old('tipo', $elemento->tipo) === 'CODIGO' ? 'selected' : '' }}>💻 Código</option>
                                <option value="SCRIPT_BD" {{ old('tipo', $elemento->tipo) === 'SCRIPT_BD' ? 'selected' : '' }}>🗃️ Script de Base de Datos</option>
                                <option value="CONFIGURACION" {{ old('tipo', $elemento->tipo) === 'CONFIGURACION' ? 'selected' : '' }}>⚙️ Configuración</option>
                                <option value="OTRO" {{ old('tipo', $elemento->tipo) === 'OTRO' ? 'selected' : '' }}>📦 Otro</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sección 2 -->
                        <div class="mb-8 bg-orange-50 p-5 rounded-lg border border-orange-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    2
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Estado y Control</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                Define el estado actual del elemento
                            </p>
                        </div>

                        {{-- Estado --}}
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estado *
                            </label>
                            <select name="estado" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('estado') border-red-500 @enderror" required>
                                <option value="BORRADOR" {{ old('estado', $elemento->estado) === 'BORRADOR' ? 'selected' : '' }}>📝 Borrador</option>
                                <option value="EN_REVISION" {{ old('estado', $elemento->estado) === 'EN_REVISION' ? 'selected' : '' }}>🔍 En Revisión</option>
                                <option value="APROBADO" {{ old('estado', $elemento->estado) === 'APROBADO' ? 'selected' : '' }}>✅ Aprobado</option>
                                <option value="LIBERADO" {{ old('estado', $elemento->estado) === 'LIBERADO' ? 'selected' : '' }}>🚀 Liberado</option>
                                <option value="OBSOLETO" {{ old('estado', $elemento->estado) === 'OBSOLETO' ? 'selected' : '' }}>📁 Obsoleto</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Información de versión -->
                        <div class="mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Información de Versionado</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-3 rounded border">
                                    <div class="text-sm text-gray-600">Versión Actual</div>
                                    <div class="text-lg font-bold text-blue-600">v{{ $elemento->versionActual?->version ?? '1.0' }}</div>
                                </div>
                                <div class="bg-white p-3 rounded border">
                                    <div class="text-sm text-gray-600">Total Versiones</div>
                                    <div class="text-lg font-bold text-blue-600">{{ $elemento->versiones->count() }}</div>
                                </div>
                                <div class="bg-white p-3 rounded border">
                                    <div class="text-sm text-gray-600">Última Modificación</div>
                                    <div class="text-sm font-bold text-gray-900">{{ $elemento->actualizado_en?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="mb-8 bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-2">ℹ️ Edición de Metadatos</h4>
                                    <p class="text-sm text-blue-800 mb-2">
                                        Esta vista permite editar la información básica del elemento (título, descripción, tipo, estado).
                                    </p>
                                    <p class="text-sm text-blue-800">
                                        <strong>Para crear una nueva versión aprobada:</strong>
                                        <a href="{{ route('proyectos.elementos.review', [$proyecto, $elemento]) }}" class="text-blue-600 hover:text-blue-800 underline ml-1">
                                            Ir a Revisar/Aprobar EC →
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('proyectos.elementos.index', $proyecto) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
