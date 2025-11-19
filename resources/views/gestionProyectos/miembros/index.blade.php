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
                <span class="text-gray-900 font-medium">Miembros del Proyecto</span>
            </div>

            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Miembros del Proyecto</h1>
                    <p class="text-gray-600 mt-1">{{ $proyecto->nombre }}</p>
                </div>
            </div>

            <!-- Mensajes de éxito y error -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Lista de Miembros Actuales -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Miembros Actuales ({{ $miembrosProyecto->count() }})</h3>

                            @if($miembrosProyecto->isEmpty())
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">No hay miembros en el proyecto</h4>
                                    <p class="text-gray-600">Agrega miembros usando el formulario de la derecha.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($miembrosProyecto as $miembro)
                                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition">
                                            <div class="flex items-center gap-4">
                                                <div class="avatar placeholder">
                                                    <div class="bg-neutral text-neutral-content rounded-full w-12 text-sm">
                                                        <span>{{ substr($miembro->name, 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $miembro->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $miembro->correo }}</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <!-- Selector de rol -->
                                                <form action="{{ route('proyectos.miembros.update', [$proyecto, $miembro->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="rol_id" onchange="this.form.submit()" class="select select-bordered select-sm">
                                                        @foreach($roles as $rol)
                                                            <option value="{{ $rol->id }}" {{ $miembro->pivot->rol_id == $rol->id ? 'selected' : '' }}>
                                                                {{ $rol->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>

                                                <!-- Botón de remover (solo si no es líder del equipo) -->
                                                @php
                                                    $esLiderEquipo = $proyecto->equipos()->where('lider_id', $miembro->id)->exists();
                                                @endphp
                                                @if(!$esLiderEquipo && $miembrosProyecto->count() > 1)
                                                    <form action="{{ route('proyectos.miembros.destroy', [$proyecto, $miembro->id]) }}" method="POST"
                                                          onsubmit="return confirm('¿Estás seguro de remover a {{ $miembro->name }} del proyecto?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400 px-2 py-1 bg-gray-100 rounded">
                                                        @if($esLiderEquipo)
                                                            Líder
                                                        @else
                                                            Último miembro
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Agregar Nuevo Miembro -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Nuevo Miembro</h3>

                            <form action="{{ route('proyectos.miembros.store', $proyecto) }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Usuario *
                                    </label>
                                    <select name="usuario_id" class="select select-bordered w-full" required>
                                        <option value="">Seleccionar usuario...</option>
                                        @foreach($usuariosDisponibles as $usuario)
                                            <option value="{{ $usuario->id }}">
                                                {{ $usuario->nombre_completo }} ({{ $usuario->correo }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Rol en el Proyecto *
                                    </label>
                                    <select name="rol_id" class="select select-bordered w-full" required>
                                        <option value="">Seleccionar rol...</option>
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Campo oculto: asignar automáticamente al único equipo del proyecto --}}
                                @if($equipos->isNotEmpty())
                                    <input type="hidden" name="equipo_id" value="{{ $equipos->first()->id }}">
                                @endif

                                <button type="submit" class="btn btn-primary w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Agregar Miembro
                                </button>
                            </form>

                            @if($usuariosDisponibles->isEmpty())
                                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">No hay usuarios disponibles</p>
                                            <p class="text-xs text-yellow-700">Todos los usuarios ya son miembros del proyecto.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total de miembros</span>
                                    <span class="font-semibold text-gray-900">{{ $miembrosProyecto->count() }}</span>
                                </div>

                                @php
                                    $rolesCount = [];
                                    foreach($miembrosProyecto as $miembro) {
                                        $rolNombre = $miembro->pivot->rol->nombre ?? 'Sin rol';
                                        $rolesCount[$rolNombre] = ($rolesCount[$rolNombre] ?? 0) + 1;
                                    }
                                @endphp

                                @foreach($rolesCount as $rolNombre => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">{{ $rolNombre }}</span>
                                        <span class="font-semibold text-gray-900">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Quick Action 3: CCB -->
                    <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}"
                       class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-purple-300 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900">Comité de Cambios (CCB)</h3>
                        </div>
                        <p class="text-sm text-gray-600">Gestionar miembros y votaciones del CCB</p>
                    </a>                <a href="{{ route('proyectos.show', $proyecto) }}"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Elementos Configuración</div>
                    <div class="text-sm text-gray-600">Gestionar EC del proyecto</div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
