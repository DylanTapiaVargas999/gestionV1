<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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
                    <span class="text-gray-900 font-medium">{{ $liberacion->etiqueta }}</span>
                </div>

                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-4 py-2 bg-indigo-100 text-indigo-700 text-lg font-bold rounded-lg">
                                {{ $liberacion->etiqueta }}
                            </span>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $liberacion->nombre ?? 'Liberación' }}</h1>
                        </div>
                        @if($liberacion->descripcion)
                        <p class="text-gray-700 mt-2">{{ $liberacion->descripcion }}</p>
                        @endif
                    </div>
                    <a href="{{ route('proyectos.liberaciones.index', $proyecto) }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300">
                        Volver
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-lg p-4">
                    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Información de la Liberación -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 uppercase">Fecha Liberación</span>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ $liberacion->fecha_liberacion->format('d/m/Y') }}</p>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 uppercase">Elementos</span>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ $liberacion->items->count() }}</p>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 uppercase">Creada</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">{{ $liberacion->creado_en->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-600">{{ $liberacion->creado_en->format('H:i') }}</p>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 uppercase">Estado</span>
                    </div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full">LIBERADA</span>
                </div>
            </div>

            <!-- Elementos de la Liberación -->
            <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b-2 border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Elementos de Configuración</h3>
                    <span class="text-sm font-semibold text-gray-600">{{ $liberacion->items->count() }} elementos</span>
                </div>

                @if($liberacion->items->isNotEmpty())
                    <div class="divide-y divide-gray-200">
                        @foreach($liberacion->items as $item)
                        <div class="p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-bold text-gray-900">{{ $item->elementoConfiguracion->nombre }}</h4>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                            {{ $item->elementoConfiguracion->tipo ?? 'N/A' }}
                                        </span>
                                        @if($item->versionEc)
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                                            v{{ $item->versionEc->version }}
                                        </span>
                                        @endif
                                    </div>

                                    @if($item->elementoConfiguracion->descripcion)
                                    <p class="text-sm text-gray-700 mb-2">{{ $item->elementoConfiguracion->descripcion }}</p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
                                        @if($item->elementoConfiguracion->ruta)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                            </svg>
                                            <span>{{ Str::limit($item->elementoConfiguracion->ruta, 50) }}</span>
                                        </div>
                                        @endif
                                        @if($item->versionEc && $item->versionEc->fecha_version)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $item->versionEc->fecha_version->format('d/m/Y') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <form action="{{ route('proyectos.liberaciones.quitar-elemento', ['proyecto' => $proyecto, 'liberacion' => $liberacion, 'item' => $item]) }}"
                                      method="POST"
                                      onsubmit="return confirm('¿Remover este elemento de la liberación?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded hover:bg-red-200 transition-colors">
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                        </svg>
                        <p class="text-gray-600">No hay elementos en esta liberación</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
