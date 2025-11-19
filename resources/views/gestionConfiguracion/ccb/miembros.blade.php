<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestión de Miembros del CCB
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $proyecto->nombre }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}" class="btn btn-ghost">
                    ← Dashboard CCB
                </a>
                <a href="{{ route('proyectos.ccb.configurar', $proyecto) }}" class="btn btn-outline">
                    Configuración Completa
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Información del CCB --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $ccb->nombre }}</h3>
                            <p class="text-sm text-gray-600">Quorum necesario: {{ $ccb->quorum }} votos</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Total miembros</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $ccb->miembros()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agregar nuevo miembro --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Nuevo Miembro</h3>

                    <form action="{{ route('proyectos.ccb.miembros.agregar', $proyecto) }}" method="POST" class="flex gap-4 items-end">
                        @csrf

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Usuario del Proyecto *
                            </label>
                            <select name="usuario_id" class="select select-bordered w-full" required>
                                <option value="">Seleccionar usuario...</option>
                                @php
                                    // Obtener usuarios que tienen acceso al proyecto (a través de usuarios_roles)
                                    $usuariosProyecto = $proyecto->usuarios()->get();
                                @endphp
                                @foreach($usuariosProyecto as $usuario)
                                    @if(!$ccb->esMiembro($usuario->id))
                                        <option value="{{ $usuario->id }}">
                                            {{ $usuario->nombre_completo }} ({{ $usuario->correo }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rol en CCB
                            </label>
                            @php
                                $roleNames = isset($roles) ? $roles->pluck('nombre')->toArray() : [];
                            @endphp
                            <select name="rol_en_ccb" class="select select-bordered w-full">
                                @if(!in_array('Miembro', $roleNames))
                                    <option value="Miembro">Miembro</option>
                                @endif
                                @foreach($roleNames as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Lista de miembros actuales --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Miembros Actuales del CCB</h3>

                    @if($miembros->isEmpty())
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay miembros en el CCB</h4>
                            <p class="text-gray-600">Agrega miembros usando el formulario de arriba.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="text-gray-900">Miembro</th>
                                        <th class="text-gray-900">Rol</th>
                                        <th class="text-gray-900">Estadísticas</th>
                                        <th class="text-gray-900">Última Actividad</th>
                                        <th class="text-gray-900">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($miembros as $miembro)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-neutral text-neutral-content rounded-full w-10 text-sm">
                                                            <span>{{ substr($miembro->name, 0, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $miembro->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $miembro->email }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <form action="{{ route('proyectos.ccb.miembros.actualizar-rol', [$proyecto, $miembro->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    @php
                                                        $roleNames = isset($roles) ? $roles->pluck('nombre')->toArray() : [];
                                                    @endphp
                                                    <select name="rol_en_ccb" onchange="this.form.submit()" class="select select-bordered select-sm">
                                                        {{-- Si el rol actual no está en la lista de roles, mostrarlo primero para no perderlo --}}
                                                        @if(!in_array($miembro->pivot->rol_en_ccb, $roleNames))
                                                            <option value="{{ $miembro->pivot->rol_en_ccb }}" selected>{{ $miembro->pivot->rol_en_ccb }}</option>
                                                        @endif
                                                        @foreach($roleNames as $r)
                                                            <option value="{{ $r }}" {{ $miembro->pivot->rol_en_ccb === $r ? 'selected' : '' }}>{{ $r }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>

                                            <td>
                                                <div class="text-sm">
                                                    <div class="flex items-center gap-4">
                                                        <div class="text-center">
                                                            <div class="text-lg font-semibold text-green-600">{{ $estadisticasMiembros[$miembro->id]['aprobar'] ?? 0 }}</div>
                                                            <div class="text-xs text-gray-500">Aprobó</div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="text-lg font-semibold text-red-600">{{ $estadisticasMiembros[$miembro->id]['rechazar'] ?? 0 }}</div>
                                                            <div class="text-xs text-gray-500">Rechazó</div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="text-lg font-semibold text-blue-600">{{ $estadisticasMiembros[$miembro->id]['total_votos'] ?? 0 }}</div>
                                                            <div class="text-xs text-gray-500">Total</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-gray-700">
                                                @php
                                                    $ultimoVoto = \App\Models\VotoCCB::where('ccb_id', $ccb->id)
                                                        ->where('usuario_id', $miembro->id)
                                                        ->orderBy('votado_en', 'desc')
                                                        ->first();
                                                @endphp
                                                @if($ultimoVoto)
                                                    <div class="text-sm">
                                                        <div>{{ $ultimoVoto->votado_en->diffForHumans() }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $ultimoVoto->solicitudCambio->titulo }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic text-sm">Sin actividad</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($miembros->count() > 1)
                                                    <form action="{{ route('proyectos.ccb.miembros.remover', [$proyecto, $miembro->id]) }}" method="POST"
                                                          onsubmit="return confirm('¿Estás seguro de remover a {{ $miembro->name }} del CCB?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-error btn-outline">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                            Remover
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400">Último miembro</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('proyectos.ccb.dashboard', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Dashboard del CCB</div>
                    <div class="text-sm text-gray-600">Ver solicitudes pendientes</div>
                </a>

                <a href="{{ route('proyectos.ccb.historial-votos', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3-3 4 4 4-4"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Historial de Votos</div>
                    <div class="text-sm text-gray-600">Ver todas las votaciones</div>
                </a>

                <a href="{{ route('proyectos.equipos.index', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Gestionar Equipos</div>
                    <div class="text-sm text-gray-600">Administrar equipos del proyecto</div>
                </a>

                <a href="{{ route('proyectos.show', $proyecto) }}"
                   class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center border border-gray-200">
                    <div class="mb-2 flex justify-center">
                        <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a2 2 0 002 2h12a2 2 0 002-2V10"/>
                        </svg>
                    </div>
                    <div class="text-gray-900 font-medium">Proyecto</div>
                    <div class="text-sm text-gray-600">Volver al proyecto</div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
