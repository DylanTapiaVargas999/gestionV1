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
                <span class="text-gray-900 font-medium">Nuevo Elemento de Configuración</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Crear Elemento de Configuración</h1>
                <p class="text-gray-600 mt-1">Proyecto: <span class="font-semibold">{{ $proyecto->nombre }}</span></p>
            </div>

            <form action="{{ route('proyectos.elementos.store', $proyecto) }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                                Define la información principal del elemento de configuración
                            </p>
                        </div>

                        {{-- Título --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Título del Elemento *
                            </label>
                            <input
                                type="text"
                                name="titulo"
                                value="{{ old('titulo') }}"
                                placeholder="Ej: Documento de Requisitos, Script de Migración, etc."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('titulo') border-red-500 @enderror"
                                required
                            />
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">El código EC se generará automáticamente</p>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción *
                            </label>
                            <textarea name="descripcion" rows="3"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('descripcion') border-red-500 @enderror"
                                      placeholder="Describe el propósito y contenido de este elemento..."
                                      required>{{ old('descripcion') }}</textarea>
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
                                <option value="">Seleccionar tipo...</option>
                                <option value="DOCUMENTO" {{ old('tipo') === 'DOCUMENTO' ? 'selected' : '' }}>📄 Documento</option>
                                <option value="CODIGO" {{ old('tipo') === 'CODIGO' ? 'selected' : '' }}>💻 Código</option>
                                <option value="SCRIPT_BD" {{ old('tipo') === 'SCRIPT_BD' ? 'selected' : '' }}>🗃️ Script de Base de Datos</option>
                                <option value="CONFIGURACION" {{ old('tipo') === 'CONFIGURACION' ? 'selected' : '' }}>⚙️ Configuración</option>
                                <option value="OTRO" {{ old('tipo') === 'OTRO' ? 'selected' : '' }}>📦 Otro</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Información sobre el flujo de trabajo -->
                        <div class="mb-8 bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-2">💡 ¿Cómo trabajar con este EC?</h4>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li><strong>Paso 1:</strong> Crea el elemento (se guardará en estado PENDIENTE)</li>
                                        <li><strong>Paso 2:</strong> Trabaja en tu repositorio GitHub y haz commits</li>
                                        <li><strong>Paso 3:</strong> Al completar tareas, vincula los commits</li>
                                        <li><strong>Paso 4:</strong> Los commits vinculados crean versiones automáticamente</li>
                                        <li>📌 No necesitas subir archivos aquí - todo viene de GitHub</li>
                                    </ul>
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
                                Crear Elemento
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
