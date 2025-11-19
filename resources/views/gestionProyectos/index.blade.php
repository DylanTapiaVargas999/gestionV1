<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header dentro de un recuadro blanco (card) -->
            <div class="card bg-white text-black shadow-sm mb-6">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Proyectos</h1>
                            <p class="text-gray-600 mt-1">Gestiona todos tus proyectos desde aquí</p>
                        </div>
                        <div>
                            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 border border-black text-black bg-white px-4 py-2 rounded-md hover:bg-black hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Proyecto
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de proyectos -->
            <div class="card bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Lista de Proyectos</h2>
                        <span class="text-sm text-gray-600">Mostrando <span class="font-medium text-gray-900">{{ $todosLosProyectos->count() }}</span> proyectos</span>
                    </div>
                    @if($todosLosProyectos->isEmpty())
                        <!-- Empty State -->
                        <div class="text-center py-12 px-4">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay proyectos aún</h3>
                            <p class="text-gray-600 mb-6">Crea tu primer proyecto para comenzar</p>
                            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 border border-black text-black bg-white px-6 py-3 rounded-md hover:bg-black hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Crear Proyecto
                            </a>
                        </div>
                    @else
                        <!-- Tabla -->
                        <div class="overflow-x-auto">
                            <table class="table w-full min-w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Proyecto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Metodología</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Mi Rol</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Equipos</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Miembros</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Creado</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($todosLosProyectos as $proyecto)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    @php
                                                        // Preparar objeto proyecto para el partial avatar
                                                        $proyectoObj = (object)[
                                                            'codigo' => $proyecto['codigo'] ?? $proyecto['nombre'],
                                                            'nombre' => $proyecto['nombre'],
                                                            'metodologia' => (object)['nombre' => $proyecto['metodologia'] ?? 'Ninguna']
                                                        ];
                                                    @endphp
                                                    <div style="transform: scale(0.625); transform-origin: left center;">
                                                        @include('partials.avatar-proyecto', ['proyecto' => $proyectoObj])
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $proyecto['nombre'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $proyecto['codigo'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $metodologia = $proyecto['metodologia'] ?? 'No especificada';
                                                    $metodologiaKey = strtolower($metodologia);
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($metodologiaKey === 'agil') bg-blue-100 text-blue-800
                                                    @elseif($metodologiaKey === 'cascada') bg-purple-100 text-purple-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst($metodologia) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($proyecto['mi_rol'] === 'Creador') bg-yellow-100 text-yellow-800
                                                    @elseif($proyecto['mi_rol'] === 'Líder') bg-orange-100 text-orange-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $proyecto['mi_rol'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900">{{ $proyecto['total_equipos'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="text-sm font-medium text-gray-900">{{ $proyecto['total_miembros'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $proyecto['estado'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $proyecto['creado_en']->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('proyectos.show', $proyecto['id']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-black text-sm font-medium rounded-lg hover:bg-black hover:text-white transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer con estadísticas -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div>
                                    Mostrando <span class="font-medium text-gray-900">{{ $todosLosProyectos->count() }}</span> proyectos en total
                                </div>
                                <div class="flex items-center gap-4">
                                    <span> {{ $todosLosProyectos->where('mi_rol', 'Creador')->count() }} creados</span>
                                    <span> {{ $todosLosProyectos->where('mi_rol', '!=', 'Creador')->count() }} asignados</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
