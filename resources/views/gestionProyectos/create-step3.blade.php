<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <div class="text-sm breadcrumbs">
                    <ul class="text-gray-600">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a></li>
                        <li><a href="{{ route('proyectos.create') }}" class="hover:text-gray-900">Nuevo Proyecto</a></li>
                        <li class="text-gray-900 font-medium">Configurar Equipo</li>
                    </ul>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Proyecto</h1>
                <p class="mt-2 text-gray-600">Paso 3 de 4: Configurar Equipo del Proyecto</p>
            </div>

            <!-- Progress Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 - Completed -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Paso 1</div>
                            <div class="text-xs text-gray-500">Datos del Proyecto</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                    <!-- Step 2 - Completed -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Paso 2</div>
                            <div class="text-xs text-gray-500">Plantillas EC</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                    <!-- Step 3 - Current -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                            <span class="text-lg font-semibold">3</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-black">Paso 3</div>
                            <div class="text-xs text-gray-500">Configurar Equipo</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

                    <!-- Step 4 - Pending -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600">
                            <span class="text-lg font-semibold">4</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-400">Paso 4</div>
                            <div class="text-xs text-gray-500">Revisión</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Summary Card -->
            <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm mb-6">
                <div class="card-body">
                    <h3 class="text-lg font-bold text-gray-900">{{ $proyectoData['nombre'] }}</h3>
                    <div class="flex gap-4 mt-2 text-sm">
                        <span class="badge badge-primary">{{ $proyectoData['metodologia'] }}</span>
                        <span class="text-gray-600">{{ $proyectoData['codigo'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card bg-white shadow-md">
                <div class="card-body">

                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Equipo del Proyecto</h2>
                        <p class="text-sm text-gray-600 mt-2">Agrega los miembros que participarán en este proyecto y asigna sus roles.</p>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info bg-blue-50 border border-blue-200 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-gray-800">
                            <p class="font-medium text-gray-900">Se creará automáticamente un equipo principal para el proyecto.</p>
                            <p class="mt-1 text-gray-700">Los roles disponibles se ajustan a la metodología seleccionada ({{ $proyectoData['metodologia'] }}).</p>
                        </div>
                    </div>

                    <form action="{{ route('proyectos.store-step3') }}" method="POST" id="equipoForm">
                        @csrf

                        <!-- Selección de Líder del Equipo -->
                        <div class="border-2 border-blue-200 bg-blue-50 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        Líder del Equipo *
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">El líder tendrá permisos completos sobre el proyecto</p>
                                </div>
                                <button
                                    type="button"
                                    onclick="autoAsignarLider()"
                                    class="btn btn-sm bg-blue-600 text-white hover:bg-blue-700 border-0"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Asignarme como líder
                                </button>
                            </div>

                            <div class="form-control">
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="buscarLider"
                                        placeholder="🔍 Buscar usuario por nombre o correo..."
                                        class="input input-bordered w-full bg-white text-gray-900 pr-10"
                                        autocomplete="off"
                                        onkeyup="filtrarUsuarios()"
                                        onfocus="mostrarListaUsuarios()"
                                    >
                                    <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <!-- Lista de usuarios filtrados -->
                                <div id="listaUsuarios" class="hidden mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg">
                                    <!-- Se llena dinámicamente -->
                                </div>

                                <!-- Usuario seleccionado -->
                                <input type="hidden" name="lider_id" id="lider_id" required>
                                <div id="liderSeleccionado" class="hidden mt-3 p-3 bg-white border-2 border-blue-400 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                            <span id="liderInicialesDisplay"></span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900" id="liderNombreDisplay"></div>
                                            <div class="text-sm text-gray-600" id="liderCorreoDisplay"></div>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        onclick="limpiarLider()"
                                        class="btn btn-sm btn-ghost text-red-600 hover:bg-red-50"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                @error('lider_id')
                                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Miembros Container -->
                        <div class="space-y-4 mb-6" id="miembrosContainer">
                            <!-- Se agregan miembros dinámicamente -->
                        </div>

                        <!-- Add Member Button -->
                        <button
                            type="button"
                            onclick="agregarMiembro()"
                            class="btn bg-gray-50 text-gray-700 border-2 border-dashed border-gray-300 hover:border-blue-500 hover:bg-blue-100 w-full"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar Miembro al Equipo
                        </button>

                        @error('miembros')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-8">
                            <a
                                href="{{ route('proyectos.create-step2') }}"
                                class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Volver
                            </a>
                            <button
                                type="submit"
                                class="btn bg-black text-white hover:bg-gray-800 border-0"
                            >
                                Continuar a Revisión
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script>
        let contadorMiembros = 0;

        // Cargar usuarios y roles desde el servidor
        const usuarios = @json($usuarios);
        const todosLosRoles = @json($roles);

        // Filtrar roles por metodología del proyecto + roles genéricos
        const metodologiaId = {{ $proyectoData['id_metodologia'] }};

        // Roles disponibles: los de la metodología seleccionada + los genéricos (null)
        const roles = todosLosRoles.filter(rol =>
            rol.metodologia_id === metodologiaId || rol.metodologia_id === null
        );

        const usuarioActual = {
            id: '{{ Auth::id() }}',
            nombre: '{{ Auth::user()->nombre_completo }}',
            correo: '{{ Auth::user()->correo }}'
        };

        // ====== FUNCIONES PARA SELECCIÓN DE LÍDER ======

        function getIniciales(nombre) {
            return nombre.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }

        function autoAsignarLider() {
            seleccionarLider(usuarioActual.id, usuarioActual.nombre, usuarioActual.correo);
        }

        function seleccionarLider(id, nombre, correo) {
            document.getElementById('lider_id').value = id;
            document.getElementById('liderNombreDisplay').textContent = nombre;
            document.getElementById('liderCorreoDisplay').textContent = correo;
            document.getElementById('liderInicialesDisplay').textContent = getIniciales(nombre);
            document.getElementById('liderSeleccionado').classList.remove('hidden');
            document.getElementById('buscarLider').value = nombre;
            document.getElementById('listaUsuarios').classList.add('hidden');
        }

        function limpiarLider() {
            document.getElementById('lider_id').value = '';
            document.getElementById('liderSeleccionado').classList.add('hidden');
            document.getElementById('buscarLider').value = '';
            document.getElementById('listaUsuarios').classList.add('hidden');
        }

        function filtrarUsuarios() {
            const busqueda = document.getElementById('buscarLider').value.toLowerCase();
            const lista = document.getElementById('listaUsuarios');

            if (busqueda.length < 2) {
                lista.classList.add('hidden');
                return;
            }

            const usuariosFiltrados = usuarios.filter(u =>
                u.nombre_completo.toLowerCase().includes(busqueda) ||
                u.correo.toLowerCase().includes(busqueda)
            );

            if (usuariosFiltrados.length === 0) {
                lista.innerHTML = '<div class="p-4 text-center text-gray-500">No se encontraron usuarios</div>';
                lista.classList.remove('hidden');
                return;
            }

            lista.innerHTML = usuariosFiltrados.map(u => `
                <div
                    class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-200 last:border-0 flex items-center"
                    onclick="seleccionarLider('${u.id}', '${u.nombre_completo}', '${u.correo}')"
                >
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                        ${getIniciales(u.nombre_completo)}
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-gray-900">${u.nombre_completo}</div>
                        <div class="text-xs text-gray-600">${u.correo}</div>
                    </div>
                </div>
            `).join('');

            lista.classList.remove('hidden');
        }

        function mostrarListaUsuarios() {
            if (document.getElementById('buscarLider').value.length >= 2) {
                filtrarUsuarios();
            }
        }

        // Ocultar lista al hacer clic fuera
        document.addEventListener('click', function(e) {
            const buscarInput = document.getElementById('buscarLider');
            const lista = document.getElementById('listaUsuarios');
            if (e.target !== buscarInput && !lista.contains(e.target)) {
                lista.classList.add('hidden');
            }
        });

        // ====== FUNCIONES PARA MIEMBROS DEL EQUIPO ======

        function agregarMiembro() {
            const container = document.getElementById('miembrosContainer');
            const miembroDiv = document.createElement('div');
            miembroDiv.className = 'border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-blue-300 transition-colors';
            miembroDiv.id = `miembro-${contadorMiembros}`;

            miembroDiv.innerHTML = `
                <div class="flex gap-4 items-start">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Usuario -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm font-medium text-gray-700">Usuario *</span>
                            </label>
                            <select
                                name="miembros[${contadorMiembros}][usuario_id]"
                                required
                                class="select select-bordered select-sm w-full bg-white text-gray-900"
                            >
                                <option value="" class="text-gray-500">Seleccionar usuario...</option>
                                ${usuarios.map(u => `
                                    <option value="${u.id}" class="text-gray-900">${u.nombre_completo} (${u.correo})</option>
                                `).join('')}
                            </select>
                        </div>

                        <!-- Rol -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm font-medium text-gray-700">Rol *</span>
                            </label>
                            <select
                                name="miembros[${contadorMiembros}][rol_id]"
                                required
                                class="select select-bordered select-sm w-full bg-white text-gray-900"
                            >
                                <option value="" class="text-gray-500">Seleccionar rol...</option>
                                ${roles.map(r => `
                                    <option value="${r.id}" title="${r.descripcion}" class="text-gray-900">${r.nombre}</option>
                                `).join('')}
                            </select>
                        </div>
                    </div>

                    <!-- Botón Eliminar -->
                    <button
                        type="button"
                        onclick="eliminarMiembro(${contadorMiembros})"
                        class="btn btn-sm btn-ghost text-red-600 hover:bg-red-50 mt-8"
                        title="Eliminar miembro"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(miembroDiv);
            contadorMiembros++;
        }

        function eliminarMiembro(id) {
            const miembroDiv = document.getElementById(`miembro-${id}`);
            if (miembroDiv) {
                miembroDiv.remove();
            }
        }

        // Validación del formulario
        document.getElementById('equipoForm').addEventListener('submit', function(e) {
            const liderId = document.getElementById('lider_id').value;
            if (!liderId) {
                e.preventDefault();
                alert('Debes seleccionar un líder para el equipo.');
                return false;
            }
        });

        // Agregar un miembro inicial al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            agregarMiembro();
        });
    </script>

</x-app-layout>
