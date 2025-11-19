<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📋 Sprint Planning
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ $proyecto->nombre }} • Planificación de Sprints
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Navegación Scrum -->
            <x-scrum.navigation :proyecto="$proyecto" active="planning" />

            <!-- Guía del Sprint Planning -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-green-900 text-lg mb-2">📋 Cómo funciona el Sprint Planning</h3>
                        <div class="grid md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-white rounded-lg p-3 border-l-4 border-green-500">
                                <p class="font-semibold text-gray-900 mb-1">1️⃣ Selecciona User Stories</p>
                                <p class="text-gray-600 text-xs">Del <strong>Product Backlog</strong> (izquierda), arrastra las stories que quieres trabajar en el sprint</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border-l-4 border-blue-500">
                                <p class="font-semibold text-gray-900 mb-1">2️⃣ Asigna al Sprint</p>
                                <p class="text-gray-600 text-xs">Suelta las stories en la zona de <strong>Sprint Planning</strong> (derecha) o haz clic en "Agregar a Sprint"</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border-l-4 border-purple-500">
                                <p class="font-semibold text-gray-900 mb-1">3️⃣ Guarda e Inicia</p>
                                <p class="text-gray-600 text-xs">Haz clic en <strong>"Guardar Planificación"</strong> y luego <strong>"Iniciar Sprint"</strong> para comenzar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Product Backlog -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">📦 Product Backlog</h3>
                            <button onclick="modalNuevaUserStory.showModal()" class="btn btn-sm bg-green-600 text-white hover:bg-green-700">
                                + Nueva User Story
                            </button>
                        </div>

                        <!-- Debug Info -->
                        @if(config('app.debug'))
                        <div class="text-xs text-gray-500 mb-2 p-2 bg-gray-100 rounded">
                            📊 Product Backlog: {{ $productBacklog->count() }} user stories
                        </div>
                        @endif

                        <div class="space-y-3" style="max-height: 600px; overflow-y: auto;">
                            @forelse($productBacklog as $userStory)
                                <div class="backlog-item bg-gray-50 rounded-lg p-4 border hover:bg-gray-100 transition-colors"
                                     draggable="true"
                                     data-story-id="{{ $userStory->id_tarea }}">

                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $userStory->nombre }}</h4>
                                        <div class="flex items-center gap-2">
                                            @if($userStory->story_points)
                                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                                    {{ $userStory->story_points }}
                                                </span>
                                            @endif

                                            @php
                                                $prioridadColor = $userStory->prioridad >= 8 ? 'bg-red-100 text-red-800' :
                                                                 ($userStory->prioridad >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                            @endphp
                                            <span class="badge badge-xs {{ $prioridadColor }}">
                                                P{{ $userStory->prioridad ?? 5 }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($userStory->descripcion)
                                        <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $userStory->descripcion }}</p>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            @if($userStory->responsableUsuario)
                                                <div class="flex items-center gap-1">
                                                    <div class="w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-700">
                                                            {{ substr($userStory->responsableUsuario->nombre, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-600">{{ $userStory->responsableUsuario->nombre }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <button onclick="asignarASprint({{ $userStory->id_tarea }})"
                                                class="btn btn-xs btn-outline">
                                            Agregar a Sprint
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <div class="text-4xl mb-2">📦</div>
                                    <p class="font-medium">No hay user stories en el Product Backlog</p>
                                    <p class="text-sm mt-2">Las user stories aparecen aquí cuando no están asignadas a ningún sprint</p>

                                    @if(config('app.debug'))
                                    <div class="mt-4 text-xs bg-yellow-50 border border-yellow-200 rounded p-3 text-left">
                                        <p class="font-semibold text-yellow-800">🔍 Debug Info:</p>
                                        <p class="text-yellow-700">Total tareas del proyecto: {{ \App\Models\TareaProyecto::where('id_proyecto', $proyecto->id)->count() }}</p>
                                        <p class="text-yellow-700">Con id_sprint NULL: {{ \App\Models\TareaProyecto::where('id_proyecto', $proyecto->id)->whereNull('id_sprint')->count() }}</p>
                                        <p class="text-yellow-700">Con story_points: {{ \App\Models\TareaProyecto::where('id_proyecto', $proyecto->id)->whereNotNull('story_points')->count() }}</p>
                                    </div>
                                    @endif

                                    <button onclick="modalNuevaUserStory.showModal()" class="btn btn-sm bg-green-600 text-white hover:bg-green-700 mt-4">
                                        Crear primera User Story
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sprint Backlog -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">🚀 Sprint Plannning</h3>
                                <p class="text-sm text-gray-600">Arrastra user stories para planificar el sprint</p>
                            </div>
                        </div>

                        <!-- Selector de Sprint -->
                        <div class="mb-4">
                            <label class="label">
                                <span class="label-text text-black font-semibold">Sprint a planificar:</span>
                            </label>

                            @php $sprintActivo = $sprints->where('estado', 'activo')->first(); @endphp
                            @if($sprintActivo)
                                <div class="alert alert-warning mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.186-.833-2.956 0L4.858 19.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                    <span><strong>{{ $sprintActivo->nombre }}</strong> está ACTIVO. Debes completarlo antes de iniciar otro.</span>
                                    <button onclick="completarSprintActivo({{ $sprintActivo->id_sprint }})" class="btn btn-sm btn-error">
                                        🏁 Completar Sprint Activo
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-info mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>¡Perfecto! No hay sprints activos. Puedes crear y planificar nuevos sprints.</span>
                                    <button onclick="modalNuevoSprint.showModal()" class="btn btn-sm btn-success">
                                        ➕ Crear Nuevo Sprint
                                    </button>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <select id="sprintSelector" class="select select-bordered select-sm bg-white text-gray-900 flex-1">
                                    @forelse($sprints as $sprint)
                                        <option value="{{ $sprint->id_sprint }}" class="text-gray-900">
                                            {{ $sprint->nombre }}
                                            @if($sprint->estado === 'activo') 🔥 ACTIVO
                                            @elseif($sprint->estado === 'completado') ✅ Completado
                                            @elseif($sprint->estado === 'planificado') 📋 Planificado
                                            @endif
                                        </option>
                                    @empty
                                        <option value="">No hay sprints</option>
                                    @endforelse
                                    <option value="nuevo">+ Nuevo Sprint</option>
                                </select>
                                <button onclick="modalNuevoSprint.showModal()" class="btn btn-sm btn-outline">
                                    + Sprint
                                </button>
                            </div>
                        </div>

                        <!-- Drop Zone para Sprint -->
                        <div id="sprintDropZone" class="bg-blue-50 border-2 border-dashed border-blue-200 rounded-lg p-6 mb-4 min-h-[200px]">
                            <div class="text-center text-blue-600">
                                <div class="text-4xl mb-2">🎯</div>
                                <p class="font-medium">Sprint Planning Area</p>
                                <p class="text-sm">Arrastra user stories aquí para incluirlas en el sprint</p>
                            </div>
                        </div>

                        <!-- Métricas del Sprint -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-4">
                                <p class="text-blue-100 text-sm">Story Points</p>
                                <p class="text-2xl font-bold" id="totalStoryPoints">0</p>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-4">
                                <p class="text-green-100 text-sm">User Stories</p>
                                <p class="text-2xl font-bold" id="totalUserStories">0</p>
                            </div>
                        </div>

                        <!-- Capacidad del Equipo -->
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="font-medium text-yellow-800 mb-2">💡 Capacidad del Equipo</h4>
                            <p class="text-sm text-yellow-700">
                                Velocidad promedio: <strong>20-25 story points</strong> por sprint de 2 semanas
                            </p>
                        </div>

                        <!-- Acciones del Sprint -->
                        <div class="mt-6 space-y-2">
                            <button onclick="guardarPlanificacion()" class="btn btn-sm bg-green-600 text-white hover:bg-green-700 w-full">
                                💾 Guardar Planificación (solo guardar)
                            </button>
                            <button onclick="iniciarSprintSeleccionado()" class="btn btn-sm bg-blue-600 text-white hover:bg-blue-700 w-full">
                                🚀 Guardar e Iniciar Sprint
                            </button>
                            <p class="text-xs text-gray-500 text-center">💡 El botón "Iniciar Sprint" guarda automáticamente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-6 flex justify-end gap-4">
                <button onclick="guardarPlanificacion()" class="btn btn-outline">💾 Guardar Planificación</button>
                <button onclick="iniciarSprintSeleccionado()" class="btn bg-blue-600 text-white hover:bg-blue-700">🚀 Guardar e Iniciar Sprint</button>
            </div>
        </div>
    </div>

    <!-- Modal Nueva User Story -->
    <dialog id="modalNuevaUserStory" class="modal">
        <div class="modal-box w-11/12 max-w-3xl bg-white border border-gray-200 shadow-2xl">
            <!-- Header del Modal -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Nueva User Story</h3>
                        <p class="text-sm text-gray-500">Crea una nueva historia de usuario para el Product Backlog</p>
                    </div>
                </div>
                <button type="button" onclick="modalNuevaUserStory.close()" class="btn btn-sm btn-circle btn-ghost text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('scrum.user-stories.store', $proyecto) }}" class="space-y-6">
                @csrf

                <!-- User Story Principal -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <label class="block text-sm font-semibold text-blue-900 mb-2">📝 Historia de Usuario</label>
                    <input type="text" name="nombre"
                           class="input input-bordered w-full bg-white border-blue-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                           placeholder="Como [tipo de usuario], quiero [funcionalidad] para [beneficio]"
                           required>
                    <p class="text-xs text-blue-600 mt-1">Ejemplo: Como cliente, quiero crear una cuenta para guardar mis productos favoritos</p>
                </div>

                <!-- Criterios de Aceptación -->
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <label class="block text-sm font-semibold text-yellow-900 mb-2">✅ Criterios de Aceptación</label>
                    <textarea name="descripcion"
                              class="textarea textarea-bordered w-full bg-white border-yellow-300 text-gray-900 placeholder-gray-500 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200"
                              rows="3"
                              placeholder="- Dado que soy un usuario nuevo&#10;- Cuando ingreso mis datos válidos&#10;- Entonces puedo crear mi cuenta exitosamente"></textarea>
                </div>

                <!-- Grid de campos principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Story Points -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">🔢 Story Points</label>
                        <select name="story_points" class="select select-bordered w-full bg-white border-gray-300 text-gray-900 focus:border-blue-500">
                            <option value="" class="text-gray-500">Sin estimar</option>
                            <option value="1" class="text-gray-900">1 - Muy pequeña (< 1 día)</option>
                            <option value="2" class="text-gray-900">2 - Pequeña (1-2 días)</option>
                            <option value="3" class="text-gray-900">3 - Pequeña-Media (2-3 días)</option>
                            <option value="5" class="text-gray-900">5 - Media (3-5 días)</option>
                            <option value="8" class="text-gray-900">8 - Grande (1 semana)</option>
                            <option value="13" class="text-gray-900">13 - Muy grande (2 semanas)</option>
                            <option value="21" class="text-gray-900">21 - Épica (requiere división)</option>
                        </select>
                    </div>

                    <!-- Prioridad -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">⚡ Prioridad</label>
                        <select name="prioridad" class="select select-bordered w-full bg-white border-gray-300 text-gray-900 focus:border-blue-500">
                            <option value="10" class="text-red-600">🔴 10 - Crítica</option>
                            <option value="8" class="text-orange-600">🟠 8 - Alta</option>
                            <option value="5" selected class="text-yellow-600">🟡 5 - Media</option>
                            <option value="3" class="text-blue-600">🔵 3 - Baja</option>
                            <option value="1" class="text-gray-600">⚪ 1 - Muy Baja</option>
                        </select>
                    </div>

                    <!-- Elemento de Configuración -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">🔗 Elemento de Configuración</label>
                        <select name="id_ec" class="select select-bordered w-full bg-white border-gray-300 text-gray-900 focus:border-blue-500">
                            <option value="" class="text-gray-500">Sin vincular</option>
                            @if(isset($elementosConfiguracion))
                                @foreach($elementosConfiguracion as $ec)
                                    <option value="{{ $ec->id }}" class="text-gray-900">{{ $ec->titulo }} ({{ $ec->tipo }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Responsable -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">👤 Responsable</label>
                        <select name="responsable" class="select select-bordered w-full bg-white border-gray-300 text-gray-900 focus:border-blue-500">
                            <option value="" class="text-gray-500">Sin asignar</option>
                            @if(isset($miembrosEquipo))
                                @foreach($miembrosEquipo as $miembro)
                                    <option value="{{ $miembro->id }}" class="text-gray-900">{{ $miembro->nombre }} {{ $miembro->apellido }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2 text-gray-600 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>La User Story se agregará al <strong>Product Backlog</strong> y podrás asignarla a un sprint más tarde.</span>
                    </div>
                </div>

                <!-- Campos ocultos -->
                <input type="hidden" name="id_fase" value="{{ $metodologia->fases->first()->id_fase ?? '' }}">
                <input type="hidden" name="estado" value="To Do">

                <!-- Botones de acción -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="modalNuevaUserStory.close()"
                            class="btn btn-ghost text-gray-600 hover:text-gray-800">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="btn bg-green-600 hover:bg-green-700 text-white border-0 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar al Product Backlog
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-black bg-opacity-50">
            <button>Cerrar</button>
        </form>
    </dialog>

    <!-- Modal Nuevo Sprint -->
    <dialog id="modalNuevoSprint" class="modal">
        <div class="modal-box bg-white max-w-md">
            <h3 class="font-bold text-lg text-black mb-6 flex items-center gap-2">
                🚀 <span>Crear Nuevo Sprint</span>
            </h3>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text text-black font-semibold">Nombre del Sprint</span></label>
                <input type="text" id="nombreNuevoSprint" class="input input-bordered w-full bg-white text-black focus:border-blue-500"
                       placeholder="Sprint {{ $sprints->count() + 1 }}" value="Sprint {{ $sprints->count() + 1 }}">
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text text-black font-semibold">Objetivo del Sprint</span></label>
                <textarea id="objetivoNuevoSprint" class="textarea textarea-bordered bg-white text-black focus:border-blue-500" rows="2"
                          placeholder="¿Qué quieres lograr en este sprint?"></textarea>
            </div>

            <div class="form-control mb-6">
                <label class="label"><span class="label-text text-black font-semibold">Duración</span></label>
                <select id="duracionNuevoSprint" class="select select-bordered bg-white text-black focus:border-blue-500">
                    <option value="7">1 semana (7 días)</option>
                    <option value="14" selected>2 semanas (14 días)</option>
                    <option value="21">3 semanas (21 días)</option>
                    <option value="28">4 semanas (28 días)</option>
                </select>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <div class="flex items-center gap-2 text-blue-800 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-sm">Tip</span>
                </div>
                <p class="text-blue-700 text-sm">El sprint se creará en estado "Planificado". Luego podrás agregar user stories y iniciarlo.</p>
            </div>

            <div class="modal-action">
                <button type="button" onclick="modalNuevoSprint.close()" class="btn btn-ghost">Cancelar</button>
                <button type="button" onclick="crearNuevoSprint()" class="btn bg-blue-600 text-white hover:bg-blue-700">
                    ✨ Crear Sprint
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>Cerrar</button>
        </form>
    </dialog>

    <script>
        // Función para notificaciones bonitas
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-md`;
            notification.innerHTML = `<div class="flex items-center gap-3"><span class="text-xl">${type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️'}</span><span>${message}</span></div>`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        let sprintPlanificado = [];

        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 Sprint Planning cargado');
            console.log('Product Backlog items:', document.querySelectorAll('.backlog-item').length);

            // Event listener para cambio de sprint
            const sprintSelector = document.getElementById('sprintSelector');
            sprintSelector.addEventListener('change', function() {
                const sprintId = this.value;
                if (sprintId && sprintId !== 'nuevo') {
                    cargarUserStoriesDelSprint(sprintId);
                } else {
                    limpiarSprintDropZone();
                }
            });

            // Cargar user stories del sprint inicialmente seleccionado
            if (sprintSelector.value && sprintSelector.value !== 'nuevo') {
                cargarUserStoriesDelSprint(sprintSelector.value);
            }

            // Drag and Drop para planificación
            document.querySelectorAll('.backlog-item').forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', this.dataset.storyId);
                    this.style.opacity = '0.5';
                });

                item.addEventListener('dragend', function(e) {
                    this.style.opacity = '1';
                });
            });

            // Drop zone del sprint
            const sprintDropZone = document.getElementById('sprintDropZone');

            sprintDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.backgroundColor = '#dbeafe';
                this.style.borderColor = '#3b82f6';
            });

            sprintDropZone.addEventListener('dragleave', function(e) {
                this.style.backgroundColor = '#eff6ff';
                this.style.borderColor = '#93c5fd';
            });

            sprintDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.backgroundColor = '#eff6ff';
                this.style.borderColor = '#93c5fd';

                const storyId = e.dataTransfer.getData('text/plain');
                agregarASprintPlanning(storyId);
            });
        });

        function agregarASprintPlanning(storyId) {
            // Encontrar el elemento de la user story
            const storyElement = document.querySelector(`[data-story-id="${storyId}"]`);
            if (!storyElement) return;

            // Agregar al array de planificación
            if (!sprintPlanificado.includes(storyId)) {
                sprintPlanificado.push(storyId);

                // Clonar el elemento y agregarlo a la zona del sprint
                const clone = storyElement.cloneNode(true);
                clone.classList.add('bg-blue-50', 'border-blue-200');

                // Agregar botón de remover
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '✕';
                removeBtn.className = 'btn btn-xs btn-circle btn-outline ml-2';
                removeBtn.onclick = () => removerDeSprintPlanning(storyId);

                clone.querySelector('.flex.items-center.justify-between').appendChild(removeBtn);

                // Reemplazar el contenido del drop zone si está vacío
                const dropZone = document.getElementById('sprintDropZone');
                if (dropZone.querySelector('.text-center')) {
                    dropZone.innerHTML = '<div class="space-y-3"></div>';
                }
                dropZone.querySelector('.space-y-3').appendChild(clone);

                // Ocultar del backlog
                storyElement.style.display = 'none';

                actualizarMetricasSprint();
            }
        }

        function removerDeSprintPlanning(storyId) {
            sprintPlanificado = sprintPlanificado.filter(id => id !== storyId);

            // Mostrar de nuevo en el backlog
            const backlogItem = document.querySelector(`[data-story-id="${storyId}"]`);
            if (backlogItem) {
                backlogItem.style.display = 'block';
            }

            // Remover del sprint planning
            const sprintItem = document.querySelector(`#sprintDropZone [data-story-id="${storyId}"]`);
            if (sprintItem) {
                sprintItem.remove();
            }

            actualizarMetricasSprint();

            // Si no hay items, mostrar mensaje
            const dropZone = document.getElementById('sprintDropZone');
            if (dropZone.querySelectorAll('[data-story-id]').length === 0) {
                dropZone.innerHTML = `
                    <div class="text-center text-blue-600">
                        <div class="text-4xl mb-2">🎯</div>
                        <p class="font-medium">Sprint Planning Area</p>
                        <p class="text-sm">Arrastra user stories aquí para incluirlas en el sprint</p>
                    </div>
                `;
            }
        }

        function asignarASprint(storyId) {
            agregarASprintPlanning(storyId);
        }

        function actualizarMetricasSprint() {
            let totalStoryPoints = 0;
            let totalUserStories = sprintPlanificado.length;

            sprintPlanificado.forEach(storyId => {
                const storyElement = document.querySelector(`[data-story-id="${storyId}"]`);
                const pointsElement = storyElement?.querySelector('.bg-blue-100');
                if (pointsElement) {
                    totalStoryPoints += parseInt(pointsElement.textContent) || 0;
                }
            });

            document.getElementById('totalStoryPoints').textContent = totalStoryPoints;
            document.getElementById('totalUserStories').textContent = totalUserStories;
        }

        function crearNuevoSprint() {
            const nombre = document.getElementById('nombreNuevoSprint').value.trim();
            const objetivo = document.getElementById('objetivoNuevoSprint').value.trim();
            const duracion = parseInt(document.getElementById('duracionNuevoSprint').value);

            if (!nombre) {
                showNotification('Por favor, ingresa el nombre del sprint', 'warning');
                return;
            }

            // Calcular fechas basadas en la duración
            const fechaInicio = new Date();
            const fechaFin = new Date(fechaInicio.getTime() + (duracion * 24 * 60 * 60 * 1000));

            // Crear sprint vía AJAX
            fetch('/proyectos/{{ $proyecto->id }}/scrum/sprints', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombre: nombre,
                    objetivo: objetivo || `Objetivos del ${nombre}`,
                    fecha_inicio: fechaInicio.toISOString().split('T')[0],
                    fecha_fin: fechaFin.toISOString().split('T')[0],
                    duracion: duracion,
                    estado: 'planificado'
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || `HTTP ${res.status}`);
                }
                return data;
            })
            .then(data => {
                showNotification(`🎉 ${nombre} creado exitosamente`, 'success');
                modalNuevoSprint.close();

                // Limpiar campos
                document.getElementById('nombreNuevoSprint').value = `Sprint {{ $sprints->count() + 2 }}`;
                document.getElementById('objetivoNuevoSprint').value = '';
                document.getElementById('duracionNuevoSprint').value = '14';

                // Recargar página para mostrar el nuevo sprint
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                console.error('Error al crear sprint:', err);
                showNotification('Error al crear sprint: ' + err.message, 'error');
            });
        }

        function guardarPlanificacion() {
            const selector = document.getElementById('sprintSelector');
            const sprintId = selector.value;

            if (sprintId === 'nuevo' || sprintId === '') {
                showNotification('Por favor, selecciona un sprint primero', 'warning');
                return;
            }

            if (sprintPlanificado.length === 0) {
                showNotification('No hay user stories para asignar al sprint', 'warning');
                return;
            }

            // Enviar las user stories al sprint seleccionado
            fetch(`/proyectos/{{ $proyecto->id }}/scrum/sprints/${sprintId}/asignar-user-stories`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_stories: sprintPlanificado
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || `HTTP ${res.status}`);
                }
                return data;
            })
            .then(data => {
                showNotification(`✅ ${sprintPlanificado.length} user stories asignadas al sprint`, 'success');

                // Limpiar la planificación temporal
                sprintPlanificado = [];

                // Recargar para mostrar las user stories asignadas
                setTimeout(() => location.reload(), 1500);
            })
            .catch(err => {
                console.error('Error al guardar planificación:', err);
                showNotification('Error al guardar planificación: ' + err.message, 'error');
            });
        }

        function iniciarSprintSeleccionado() {
            const selector = document.getElementById('sprintSelector');
            const sprintId = selector.value;

            if (sprintId === 'nuevo' || sprintId === '') {
                showNotification('Por favor, crea primero el nuevo sprint antes de iniciarlo', 'warning');
                return;
            }

            if (sprintPlanificado.length === 0) {
                showNotification('No hay user stories asignadas. Agrega al menos una antes de iniciar el sprint', 'warning');
                return;
            }

            // Confirmar inicio del sprint
            const selectedOption = selector.options[selector.selectedIndex];
            const sprintNombre = selectedOption.text.split('✓')[0].trim();

            if (confirm(`🚀 ¿Iniciar ${sprintNombre}?\n\n• ${sprintPlanificado.length} user stories\n• Se guardarán las user stories y el sprint entrará en ACTIVO`)) {
                // PRIMERO: Guardar las user stories en el sprint
                showNotification('⏳ Guardando user stories...', 'info');

                fetch(`/proyectos/{{ $proyecto->id }}/scrum/sprints/${sprintId}/asignar-user-stories`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_stories: sprintPlanificado
                    })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || `HTTP ${res.status}`);
                    }
                    return data;
                })
                .then(data => {
                    console.log('✅ User stories guardadas:', data);
                    showNotification('✅ User stories guardadas', 'success');

                    // SEGUNDO: Iniciar el sprint
                    return fetch(`/proyectos/{{ $proyecto->id }}/scrum/sprints/${sprintId}/iniciar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                })
                .then(async res => {
                    const payload = await res.json().catch(() => null);
                    if (!res.ok) {
                        const message = payload?.message || `HTTP ${res.status}: ${res.statusText}`;
                        throw new Error(message);
                    }
                    return payload;
                })
                .then(data => {
                    const mensaje = data?.message || `${sprintNombre} iniciado correctamente`;
                    showNotification(mensaje, 'success');
                    setTimeout(() => window.location.href = '/proyectos/{{ $proyecto->id }}/scrum/dashboard', 1500);
                })
                .catch(err => {
                    console.error('❌ Error al iniciar sprint:', err);
                    const mensaje = err?.message || 'Ocurrió un error inesperado al iniciar el sprint';
                    showNotification(mensaje, 'error');
                });
            }
        }

        // Función para completar el sprint activo
        function completarSprintActivo(sprintId) {
            if (confirm('🏁 ¿Completar el sprint activo?\n\nEsto marcará el sprint como terminado y permitirá iniciar uno nuevo.')) {
                fetch(`/proyectos/{{ $proyecto->id }}/scrum/sprints/${sprintId}/completar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const payload = await res.json().catch(() => null);
                    if (!res.ok) {
                        const message = payload?.message || `HTTP ${res.status}: ${res.statusText}`;
                        throw new Error(message);
                    }
                    return payload;
                })
                .then(data => {
                    showNotification('Sprint completado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(err => {
                    console.error('Error al completar sprint:', err);
                    const mensaje = err?.message || 'Error al completar el sprint';
                    showNotification(mensaje, 'error');
                });
            }
        }

        // Función para cargar user stories ya asignadas al sprint
        function cargarUserStoriesDelSprint(sprintId) {
            console.log('🔍 Cargando user stories del sprint:', sprintId);

            fetch(`/proyectos/{{ $proyecto->id }}/scrum/sprints/${sprintId}/user-stories`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log('✅ User stories recibidas:', data);

                if (data.success && data.user_stories && data.user_stories.length > 0) {
                    mostrarUserStoriesEnSprint(data.user_stories);

                    // Ocultar del product backlog las que ya están en el sprint
                    data.user_stories.forEach(story => {
                        const backlogItem = document.querySelector(`[data-story-id="${story.id_tarea}"]`);
                        if (backlogItem && backlogItem.closest('.backlog-item')) {
                            backlogItem.closest('.backlog-item').style.display = 'none';
                        }
                    });
                } else {
                    console.log('ℹ️ No hay user stories en este sprint');
                    limpiarSprintDropZone();
                }
            })
            .catch(err => {
                console.error('❌ Error al cargar user stories del sprint:', err);
                limpiarSprintDropZone();
            });
        }

        // Función para mostrar user stories en la zona del sprint
        function mostrarUserStoriesEnSprint(userStories) {
            const dropZone = document.getElementById('sprintDropZone');
            dropZone.innerHTML = '<div class="space-y-3"></div>';

            const container = dropZone.querySelector('.space-y-3');
            let totalStoryPoints = 0;

            userStories.forEach(story => {
                // Crear elemento de user story
                const storyElement = document.createElement('div');
                storyElement.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4';
                storyElement.dataset.storyId = story.id_tarea;

                totalStoryPoints += parseInt(story.story_points) || 0;

                storyElement.innerHTML = `
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-semibold text-gray-900 text-sm">${story.nombre}</h4>
                        <div class="flex items-center gap-2">
                            ${story.story_points ? `<span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">${story.story_points}</span>` : ''}
                            <button onclick="removerUserStoryDelSprint(${story.id_tarea})" class="btn btn-xs btn-circle btn-outline text-red-500 hover:bg-red-50">✕</button>
                        </div>
                    </div>
                    ${story.descripcion ? `<p class="text-sm text-gray-600 mb-2">${story.descripcion}</p>` : ''}
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Prioridad: ${story.prioridad}/10</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">✅ Asignada</span>
                    </div>
                `;

                container.appendChild(storyElement);
            });

            // Actualizar métricas
            document.getElementById('totalStoryPoints').textContent = totalStoryPoints;
            document.getElementById('totalUserStories').textContent = userStories.length;
        }

        // Función para limpiar la zona del sprint
        function limpiarSprintDropZone() {
            const dropZone = document.getElementById('sprintDropZone');
            dropZone.innerHTML = `
                <div class="text-center text-blue-600">
                    <div class="text-4xl mb-2">🎯</div>
                    <p class="font-medium">Sprint Planning Area</p>
                    <p class="text-sm">Arrastra user stories aquí para incluirlas en el sprint</p>
                </div>
            `;

            // Resetear métricas
            document.getElementById('totalStoryPoints').textContent = '0';
            document.getElementById('totalUserStories').textContent = '0';
            sprintPlanificado = [];
        }

        // Función para remover user story del sprint (guardar en BD)
        function removerUserStoryDelSprint(storyId) {
            if (confirm('¿Remover esta user story del sprint?')) {
                fetch(`/proyectos/{{ $proyecto->id }}/scrum/user-stories/${storyId}/remover-sprint`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User story removida del sprint', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Error al remover user story', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showNotification('Error al remover user story', 'error');
                });
            }
        }
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .backlog-item {
            cursor: move;
            transition: all 0.2s;
        }

        .backlog-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</x-app-layout>
