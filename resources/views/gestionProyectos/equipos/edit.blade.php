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
                <a href="{{ route('proyectos.equipos.index', $proyecto) }}" class="hover:text-gray-900">Equipos</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">Editar Equipo</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Editar Equipo</h1>
                <p class="text-gray-600 mt-1">Proyecto: {{ $proyecto->nombre }}</p>
            </div>

            <form action="{{ route('proyectos.equipos.update', [$proyecto, $equipo]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-8">

                        <!-- Información del Equipo -->
                        <div class="mb-8 bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    1
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Información del Equipo</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                Modifica el nombre del equipo y cambia su líder si es necesario.
                            </p>
                        </div>

                        {{-- Nombre del Equipo --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Equipo *
                            </label>
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $equipo->nombre) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                   placeholder="Ej: Equipo de Desarrollo Frontend"
                                   required>
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Líder del Equipo --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Líder del Equipo *
                            </label>
                            <select name="lider_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" required>
                                <option value="">Seleccionar líder...</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('lider_id', $equipo->lider_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nombre_completo }} ({{ $usuario->correo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('lider_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Miembros Actuales --}}
                        <div class="mb-6 bg-green-50 p-5 rounded-lg border border-green-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    2
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Miembros Actuales</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                Miembros actuales del equipo. Puedes removerlos desde la lista de equipos.
                            </p>
                        </div>

                        @if($equipo->miembros->isEmpty())
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg text-center">
                                <p class="text-gray-600">Este equipo no tiene miembros asignados.</p>
                            </div>
                        @else
                            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($equipo->miembros as $miembro)
                                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8 text-sm">
                                                    <span>{{ substr($miembro->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $miembro->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $miembro->pivot->rol->nombre ?? 'Sin rol' }}</div>
                                            </div>
                                        </div>
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                            Miembro actual
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Botones --}}
                        <div class="flex justify-between items-center">
                            <a href="{{ route('proyectos.equipos.index', $proyecto) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                                Actualizar Equipo
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
