<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                    <a href="{{ route('proyectos.show', $proyecto) }}" class="hover:text-gray-900">{{ $proyecto->nombre }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('proyectos.liberaciones.index', $proyecto) }}" class="hover:text-gray-900">Liberaciones</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900 font-medium">Nueva Liberación</span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900">🚀 Nueva Liberación</h1>
            </div>

            <form action="{{ route('proyectos.liberaciones.store', $proyecto) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Información Básica -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Información Básica</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Etiqueta de Liberación <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   name="etiqueta"
                                   required
                                   placeholder="v1.0.0, RELEASE-2024-01, etc."
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                   value="{{ old('etiqueta') }}">
                            @error('etiqueta')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Fecha de Liberación
                            </label>
                            <input type="date"
                                   name="fecha_liberacion"
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                   value="{{ old('fecha_liberacion', now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre Descriptivo
                        </label>
                        <input type="text"
                               name="nombre"
                               placeholder="Nombre corto y descriptivo de la liberación"
                               class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                               value="{{ old('nombre') }}">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea name="descripcion"
                                  rows="3"
                                  placeholder="Describe los cambios y mejoras incluidas en esta liberación"
                                  class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                <!-- Elementos de Configuración -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Elementos de Configuración</h3>
                        <span class="text-sm text-gray-600">{{ $elementosDisponibles->count() }} elementos disponibles</span>
                    </div>

                    @if($elementosDisponibles->isNotEmpty())
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($elementosDisponibles as $elemento)
                            <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="elementos[]"
                                       value="{{ $elemento->id }}"
                                       class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-900">{{ $elemento->nombre }}</span>
                                        <span class="px-2 py-0.5 bg-{{ $elemento->estado == 'aprobado' ? 'green' : 'blue' }}-100 text-{{ $elemento->estado == 'aprobado' ? 'green' : 'blue' }}-700 text-xs font-semibold rounded-full uppercase">
                                            {{ $elemento->estado }}
                                        </span>
                                        @if($elemento->tipo)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                            {{ $elemento->tipo }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($elemento->descripcion)
                                    <p class="text-sm text-gray-600">{{ Str::limit($elemento->descripcion, 100) }}</p>
                                    @endif
                                    @if($elemento->versiones && $elemento->versiones->isNotEmpty())
                                    <p class="text-xs text-gray-500 mt-1">
                                        Última versión: <span class="font-semibold">{{ $elemento->versiones->first()->version }}</span>
                                    </p>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-900">
                                💡 <strong>Tip:</strong> Selecciona los elementos de configuración que formarán parte de esta liberación. Se incluirá automáticamente la última versión de cada elemento.
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600">No hay elementos de configuración disponibles para liberar</p>
                            <p class="text-sm text-gray-500 mt-1">Los elementos deben estar en estado "Aprobado" o "Liberado"</p>
                        </div>
                    @endif
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('proyectos.liberaciones.index', $proyecto) }}"
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Crear Liberación
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
