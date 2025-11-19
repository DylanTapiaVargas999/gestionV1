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
                <span class="text-gray-900 font-medium">Configurar CCB</span>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Configurar Comité de Control de Cambios</h1>
                <p class="text-gray-600 mt-1">Configura el CCB para aprobar o rechazar solicitudes de cambio</p>
            </div>

            @if($ccb)
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-blue-900">CCB ya configurado</h3>
                            <p class="text-sm text-blue-700">Ya existe un CCB configurado para este proyecto. Puedes modificar su configuración.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('proyectos.ccb.guardar-configuracion', $proyecto) }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-8">

                        <!-- Sección 1 -->
                        <div class="mb-8 bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    1
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Información del CCB</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                El Comité de Control de Cambios es responsable de revisar y aprobar/rechazar todas las solicitudes de cambio del proyecto.
                            </p>
                        </div>

                        {{-- Nombre del CCB --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Comité (opcional)
                            </label>
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $ccb?->nombre) }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                                   placeholder="CCB - {{ $proyecto->nombre }}">
                            <p class="mt-1 text-sm text-gray-500">Si lo dejas vacío, se usará el nombre del proyecto</p>
                        </div>

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Miembros del CCB --}}
                        <div class="mb-6 bg-orange-50 p-5 rounded-lg border border-orange-100">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    2
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Miembros del CCB</h3>
                            </div>
                            <p class="text-sm text-gray-600 ml-13">
                                Selecciona los miembros que formarán parte del comité
                            </p>
                        </div>
                        @if($proyecto->usuarios()->count() === 0)
                            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-100 rounded-lg text-sm text-yellow-800">
                                Este proyecto no tiene usuarios asignados aún. Se incluye el líder/creador del
                                proyecto como opción para que puedas crear el CCB; si quieres agregar más usuarios
                                primero asígnalos al proyecto desde la sección de miembros del proyecto.
                            </div>
                        @endif
                        <div id="miembros-container" class="space-y-3 mb-6">
                            {{-- Se llenará dinámicamente --}}
                        </div>

                        <button type="button" onclick="agregarMiembro()" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar Miembro al CCB
                        </button>

                        @error('miembros')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <div class="border-t border-gray-200 my-8"></div>

                        {{-- Información del quorum --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h4 class="font-semibold text-blue-900">Sobre el Quorum</h4>
                            </div>
                            <p class="text-sm text-blue-700">
                                El quorum se calcula automáticamente como el 50% de los miembros del CCB (redondeado hacia arriba).
                                Por ejemplo, si tienes 5 miembros, el quorum será 3.
                                Una solicitud de cambio se aprueba cuando alcanza el quorum de votos favorables.
                            </p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-between items-center">
                            <a href="{{ route('proyectos.show', $proyecto) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition">
                                {{ $ccb ? 'Actualizar CCB' : 'Crear CCB' }}
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

    <script>
    const miembrosDisponibles = @json($usuariosProyecto);
    const miembrosActuales = @json($ccb ? $ccb->miembros->pluck('id')->toArray() : []);
    const rolesActuales = @json($ccb ? $ccb->miembros->mapWithKeys(fn($m) => [$m->id => $m->pivot->rol_en_ccb])->toArray() : []);

    // Roles disponibles cargados desde la tabla `roles` (incluye los
    // creados por el seeder `CCBRolesSeeder` y cualquier rol creado
    // manualmente). Se pasaron desde el controlador.
    const rolesDisponibles = @json(isset($roles) ? $roles->pluck('nombre') : []);
        let contadorMiembros = 0;

        function agregarMiembro() {
            const container = document.getElementById('miembros-container');
            const index = contadorMiembros++;

            const div = document.createElement('div');
            div.className = 'flex gap-3 items-start bg-gray-50 border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition';
            div.id = `miembro-${index}`;

            div.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Usuario *
                    </label>
                    <select name="miembros[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-sm" required>
                        <option value="">Seleccionar usuario...</option>
                        ${miembrosDisponibles.map(u => `
                            <option value="${u.id}">
                                ${u.nombre_completo} (${u.correo})
                            </option>
                        `).join('')}
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rol en CCB
                    </label>
                    <select name="roles_ccb[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-sm">
                        ${(() => {
                            // Asegurarnos de tener 'Miembro' como opción por defecto
                            const opciones = Array.isArray(rolesDisponibles) ? rolesDisponibles.slice() : [];
                            if (!opciones.includes('Miembro')) opciones.unshift('Miembro');
                            return opciones.map(r => `\n                                <option value="${r}">${r}</option>`).join('');
                        })()}
                    </select>
                </div>

                <div class="pt-6">
                    <button type="button" onclick="eliminarMiembro(${index})"
                            class="text-gray-400 hover:text-red-500 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(div);
        }

        function eliminarMiembro(index) {
            const elemento = document.getElementById(`miembro-${index}`);
            if (elemento) {
                elemento.remove();
            }
        }

        // Cargar miembros existentes si hay CCB configurado
        window.addEventListener('DOMContentLoaded', () => {
            @if($ccb && $ccb->miembros->isNotEmpty())
                @foreach($ccb->miembros as $miembro)
                    agregarMiembro();
                    const ultimoIndex = contadorMiembros - 1;
                    const ultimoDiv = document.getElementById(`miembro-${ultimoIndex}`);
                    const selectUsuario = ultimoDiv.querySelector('select[name="miembros[]"]');
                    const selectRol = ultimoDiv.querySelector('select[name="roles_ccb[]"]');

                    selectUsuario.value = '{{ $miembro->id }}';
                    selectRol.value = '{{ $miembro->pivot->rol_en_ccb }}';
                @endforeach
            @else
                // Agregar un campo vacío inicial
                agregarMiembro();
                // Si solo hay un usuario disponible (p. ej. el creador), preseleccionarlo
                if (miembrosDisponibles.length === 1) {
                    const ultimoIndex = contadorMiembros - 1;
                    const ultimoDiv = document.getElementById(`miembro-${ultimoIndex}`);
                    const selectUsuario = ultimoDiv.querySelector('select[name="miembros[]"]');
                    if (selectUsuario) {
                        selectUsuario.value = miembrosDisponibles[0].id;
                    }
                }
            @endif
        });
    </script>
</x-app-layout>
