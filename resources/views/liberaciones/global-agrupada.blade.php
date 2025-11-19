<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 mb-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div class="text-white">
                            <h1 class="text-4xl font-bold mb-1">Mis Liberaciones</h1>
                            <p class="text-indigo-100 text-lg">Organizadas por proyecto</p>
                        </div>
                    </div>

                    <!-- Toggle de vista -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-1 flex gap-1">
                        <a href="{{ route('liberaciones.index', ['vista' => 'agrupada'] + request()->except('vista')) }}"
                           class="px-4 py-2 rounded-md text-white font-medium {{ request('vista', 'agrupada') === 'agrupada' ? 'bg-white/20' : 'hover:bg-white/10' }} transition">
                            📁 Por Proyecto
                        </a>
                        <a href="{{ route('liberaciones.index', ['vista' => 'lista'] + request()->except('vista')) }}"
                           class="px-4 py-2 rounded-md text-white font-medium {{ request('vista') === 'lista' ? 'bg-white/20' : 'hover:bg-white/10' }} transition">
                            📋 Lista
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Total Liberaciones</p>
                                <p class="text-white text-3xl font-bold mt-1">{{ $estadisticas['total_liberaciones'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Proyectos Activos</p>
                                <p class="text-white text-3xl font-bold mt-1">{{ $estadisticas['total_proyectos'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Este Mes</p>
                                <p class="text-white text-3xl font-bold mt-1">{{ $estadisticas['liberaciones_mes'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Elementos Liberados</p>
                                <p class="text-white text-3xl font-bold mt-1">{{ $estadisticas['elementos_liberados'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Búsqueda -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('liberaciones.index') }}" class="flex gap-4">
                    <input type="hidden" name="vista" value="agrupada">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Buscar liberación</label>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, etiqueta o descripción..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Proyectos con Liberaciones -->
            @if($proyectosConLiberaciones->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No hay liberaciones</h3>
                    <p class="text-gray-600 mb-6">Aún no has creado ninguna liberación en tus proyectos.</p>
                    <a href="{{ route('proyectos.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        Ir a Proyectos
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($proyectosConLiberaciones as $proyecto)
                        <!-- Card del Proyecto (Carpeta) -->
                        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 overflow-hidden">
                            <!-- Header del Proyecto -->
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="text-white">
                                            <h2 class="text-lg font-bold">{{ $proyecto->nombre }}</h2>
                                            <p class="text-xs text-blue-100">
                                                {{ $proyecto->codigo }} • {{ $proyecto->liberaciones->count() }} {{ Str::plural('liberación', $proyecto->liberaciones->count()) }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('proyectos.show', $proyecto) }}"
                                       class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver Proyecto
                                    </a>
                                </div>
                            </div>

                            <!-- Liberaciones del Proyecto -->
                            <div class="p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($proyecto->liberaciones as $liberacion)
                                        <a href="{{ route('proyectos.liberaciones.show', ['proyecto' => $proyecto->id, 'liberacion' => $liberacion->id]) }}"
                                           class="bg-white rounded-lg shadow-sm border border-gray-200 hover:border-indigo-400 hover:shadow-md transition-all duration-200 overflow-hidden group">
                                            <!-- Header -->
                                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-3 py-2 border-b border-gray-200">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="inline-flex items-center px-2 py-0.5 bg-indigo-600 text-white text-xs font-bold rounded-full">
                                                        {{ $liberacion->etiqueta }}
                                                    </div>
                                                    <span class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($liberacion->fecha_liberacion)->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h3 class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-1">
                                                    {{ $liberacion->nombre }}
                                                </h3>
                                            </div>

                                            <!-- Contenido -->
                                            <div class="p-3">
                                                <p class="text-gray-600 text-xs mb-2 line-clamp-2">
                                                    {{ $liberacion->descripcion ?? 'Sin descripción' }}
                                                </p>

                                                <div class="flex items-center justify-between text-xs">
                                                    <div class="flex items-center gap-1.5 text-gray-600">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span>{{ \Carbon\Carbon::parse($liberacion->fecha_liberacion)->format('d/m/Y') }}</span>
                                                    </div>

                                                    <div class="flex items-center gap-1.5 text-indigo-600 font-semibold">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span>{{ $liberacion->items->count() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
