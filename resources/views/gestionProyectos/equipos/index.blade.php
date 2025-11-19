<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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
                <span class="text-gray-900 font-medium">Equipos</span>
            </div>

            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Equipo del Proyecto</h1>
                    <p class="text-gray-600 mt-1">{{ $proyecto->nombre }}</p>
                    <p class="text-sm text-gray-500 mt-1">Cada proyecto tiene un único equipo principal</p>
                </div>
                @if($equipos->isEmpty())
                <a href="{{ route('proyectos.equipos.create', $proyecto) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Equipo
                </a>
                @endif
            </div>

            @if($equipos->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay equipo configurado</h3>
                        <p class="text-gray-600 mb-6">Crea el equipo principal para organizar a los miembros del proyecto.</p>
                        <a href="{{ route('proyectos.equipos.create', $proyecto) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Crear Equipo Principal
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($equipos as $equipo)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $equipo->nombre }}</h3>
                                        <p class="text-sm text-gray-600">
                                            Líder: <span class="font-medium">{{ $equipo->lider->nombre_completo ?? 'No asignado' }}</span>
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('proyectos.equipos.edit', [$proyecto, $equipo]) }}"
                                           class="text-gray-400 hover:text-blue-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($equipos->count() > 1)
                                            <form action="{{ route('proyectos.equipos.destroy', [$proyecto, $equipo]) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este equipo?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Miembros ({{ $equipo->miembros->count() }})</h4>
                                    @if($equipo->miembros->isEmpty())
                                        <p class="text-sm text-gray-500 italic">Sin miembros</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($equipo->miembros as $miembro)
                                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                                    <div class="flex items-center gap-2">
                                                        <div class="avatar placeholder">
                                                            <div class="bg-neutral text-neutral-content rounded-full w-6 text-xs">
                                                                <span>{{ substr($miembro->name, 0, 2) }}</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $miembro->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $miembro->pivot->rol->nombre ?? 'Sin rol' }}</div>
                                                        </div>
                                                    </div>
                                                    <form action="{{ route('proyectos.equipos.miembros.destroy', [$proyecto, $equipo, $miembro->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <form action="{{ route('proyectos.equipos.miembros.store', [$proyecto, $equipo]) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <select name="usuario_id" class="select select-bordered select-sm flex-1" required>
                                            <option value="">Agregar miembro...</option>
                                            @php
                                                $usuariosDisponibles = \App\Models\Usuario::whereNotIn('id', $equipo->miembros->pluck('id'))->get();
                                            @endphp
                                            @foreach($usuariosDisponibles as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->nombre_completo }} ({{ $usuario->correo }})</option>
                                            @endforeach
                                        </select>
                                        <select name="rol_id" class="select select-bordered select-sm" required>
                                            @foreach(\App\Models\Rol::all() as $rol)
                                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Acciones rápidas -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('proyectos.ccb.miembros', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Miembros CCB</div>
                    <div class="text-sm text-gray-600">Gestionar comité de cambios</div>
                </a>

                <a href="{{ route('proyectos.show', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Dashboard Proyecto</div>
                    <div class="text-sm text-gray-600">Vista general del proyecto</div>
                </a>

                <a href="{{ route('proyectos.elementos.index', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Elementos Configuración</div>
                    <div class="text-sm text-gray-600">Gestionar EC del proyecto</div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
