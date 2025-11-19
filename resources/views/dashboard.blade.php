<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Header Mejorado -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="relative">
                    <!-- Barra de color sutil arriba -->
                    <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>

                    <div class="p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                    Hola, <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ Auth::user()->nombre_completo }}</span>
                                </h1>
                                <p class="text-gray-600">Bienvenido al panel. Aquí verás tus proyectos y acciones rápidas.</p>
                            </div>
                            <div>
                                @if (Route::has('proyectos.create'))
                                    <a href="{{ route('proyectos.create') }}" class="group inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-medium">
                                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Nuevo Proyecto
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicadores con diseño moderno -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Mis Proyectos (Total) -->
                <div class="group bg-white rounded-2xl shadow-sm border border-blue-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-blue-600 text-sm font-semibold mb-1">Mis Proyectos</h3>
                        <p class="text-4xl font-bold text-blue-700 mb-1">{{ $totalMisProyectos }}</p>
                        <p class="text-xs text-blue-500">Proyectos donde pertenezco</p>
                    </div>
                </div>

                <!-- Total Liberaciones -->
                <div class="group bg-white rounded-2xl shadow-sm border border-teal-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-teal-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-teal-500 rounded-xl flex items-center justify-center shadow-md shadow-teal-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-teal-600 text-sm font-bold mb-1">Total Liberaciones</h3>
                        <p class="text-4xl font-extrabold text-teal-700 mb-1">{{ $totalLiberaciones }}</p>
                        <p class="text-xs text-teal-500">Todas mis liberaciones</p>
                    </div>
                </div>

                <!-- Liberaciones Este Mes -->
                <div class="group bg-white rounded-2xl shadow-sm border border-pink-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-pink-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-pink-500 rounded-xl flex items-center justify-center shadow-md shadow-pink-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-pink-600 text-sm font-semibold mb-1">Liberaciones del Mes</h3>
                        <p class="text-4xl font-bold text-pink-700 mb-1">{{ $liberacionesMes }}</p>
                        <p class="text-xs text-pink-500">Este mes</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
