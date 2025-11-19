{{-- Vista detallada de fase (Cascada) - Similar a Sprint Review pero para fases --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Encabezado de la fase --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <a href="{{ route('cascada.dashboard', $proyecto) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </a>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $fase->nombre_fase }}</h1>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
                                Fase {{ $fase->orden }}
                            </span>
                        </div>
                        <p class="text-gray-600">{{ $proyecto->nombre_proyecto }}</p>
                    </div>

                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900">{{ $porcentajeCompletado }}%</div>
                        <div class="text-sm text-gray-600">Completado</div>
                    </div>
                </div>
            </div>

            {{-- Métricas de la fase --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                {{-- Total de tareas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Tareas</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tareasFase->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tareas completadas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Completadas</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $tareasCompletadas }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- En progreso --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">En Progreso</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $tareasEnProgreso }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Horas estimadas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Horas Estimadas</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $horasEstimadas }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Progreso --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Progreso</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $porcentajeCompletado }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tablero Kanban de la fase --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Tablero de Actividades - {{ $fase->nombre_fase }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Columna: Pendiente --}}
                    <div class="bg-gray-50 rounded-lg p-4 min-h-[400px]"
                         ondrop="drop(event, 'Pendiente')"
                         ondragover="allowDrop(event)">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">Pendiente</h3>
                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">
                                {{ $tareasPendientes }}
                            </span>
                        </div>
                        <div class="space-y-3" id="column-Pendiente">
                            @foreach($tareasFase->filter(function($t) { 
                                $estado = strtolower(trim($t->estado ?? '')); 
                                return in_array($estado, ['pendiente', 'to do', 'todo', 'por hacer']);
                            }) as $tarea)
                                @include('gestionProyectos.cascada.partials.tarea-card', ['tarea' => $tarea])
                            @endforeach
                        </div>
                    </div>

                    {{-- Columna: En Progreso --}}
                    <div class="bg-blue-50 rounded-lg p-4 min-h-[400px]"
                         ondrop="drop(event, 'En Progreso')"
                         ondragover="allowDrop(event)">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">En Progreso</h3>
                            <span class="px-2 py-1 bg-blue-200 text-blue-700 rounded-full text-xs font-medium">
                                {{ $tareasEnProgreso }}
                            </span>
                        </div>
                        <div class="space-y-3" id="column-En Progreso">
                            @foreach($tareasFase->filter(function($t) { 
                                $estado = strtolower(trim($t->estado ?? '')); 
                                return in_array($estado, ['en progreso', 'en_progreso', 'in progress']);
                            }) as $tarea)
                                @include('gestionProyectos.cascada.partials.tarea-card', ['tarea' => $tarea])
                            @endforeach
                        </div>
                    </div>

                    {{-- Columna: En Revisión --}}
                    <div class="bg-yellow-50 rounded-lg p-4 min-h-[400px]"
                         ondrop="drop(event, 'En Revisión')"
                         ondragover="allowDrop(event)">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">En Revisión</h3>
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-700 rounded-full text-xs font-medium">
                                {{ $tareasEnRevision }}
                            </span>
                        </div>
                        <div class="space-y-3" id="column-En Revisión">
                            @foreach($tareasFase->filter(function($t) { 
                                $estado = strtolower(trim($t->estado ?? '')); 
                                return in_array($estado, ['en revisión', 'en revision', 'in review', 'review']);
                            }) as $tarea)
                                @include('gestionProyectos.cascada.partials.tarea-card', ['tarea' => $tarea])
                            @endforeach
                        </div>
                    </div>

                    {{-- Columna: Completada --}}
                    <div class="bg-green-50 rounded-lg p-4 min-h-[400px]"
                         ondrop="drop(event, 'Completada')"
                         ondragover="allowDrop(event)">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">Completada</h3>
                            <span class="px-2 py-1 bg-green-200 text-green-700 rounded-full text-xs font-medium">
                                {{ $tareasCompletadas }}
                            </span>
                        </div>
                        <div class="space-y-3" id="column-Completada">
                            @foreach($tareasFase->filter(function($t) { 
                                $estado = strtolower(trim($t->estado ?? '')); 
                                return in_array($estado, ['completada', 'completado', 'done', 'finalizado']);
                            }) as $tarea)
                                @include('gestionProyectos.cascada.partials.tarea-card', ['tarea' => $tarea])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista detallada de tareas con progreso --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Detalle de Actividades</h2>

                <div class="space-y-3">
                    @foreach($tareasFase as $tarea)
                        @php
                            // Calcular progreso basado en estado (case-insensitive)
                            $estadoLower = strtolower(trim($tarea->estado));
                            $progreso = 0;

                            if (in_array($estadoLower, ['pendiente', 'to do', 'todo', 'por hacer'])) {
                                $progreso = 0;
                            } elseif (in_array($estadoLower, ['en progreso', 'en_progreso', 'in progress'])) {
                                $progreso = 50;
                            } elseif (in_array($estadoLower, ['en revisión', 'en revision', 'in review', 'review'])) {
                                $progreso = 75;
                            } elseif (in_array($estadoLower, ['completada', 'completado', 'done', 'finalizado'])) {
                                $progreso = 100;
                            }

                            // Color de la barra
                            $colorBarra = 'bg-gray-300';
                            if ($progreso >= 100) {
                                $colorBarra = 'bg-green-500';
                            } elseif ($progreso >= 75) {
                                $colorBarra = 'bg-yellow-500';
                            } elseif ($progreso >= 50) {
                                $colorBarra = 'bg-blue-500';
                            } elseif ($progreso > 0) {
                                $colorBarra = 'bg-gray-400';
                            }
                        @endphp

                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-900">{{ $tarea->nombre }}</h3>
                                        @if($tarea->elementoConfiguracion)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-md flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $tarea->elementoConfiguracion->codigo_ec }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $tarea->descripcion }}</p>
                                </div>
                                <div class="ml-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $tarea->estado === 'Pendiente' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $tarea->estado === 'En Progreso' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $tarea->estado === 'En Revisión' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ in_array($tarea->estado, $estadosCompletados) ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ $tarea->estado }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-4 mb-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Responsable:</span>
                                    <span class="font-medium text-gray-900 block">{{ $tarea->responsableUsuario->name ?? 'Sin asignar' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Horas:</span>
                                    <span class="font-medium text-gray-900 block">{{ $tarea->horas_estimadas ?? 0 }}h</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Prioridad:</span>
                                    <span class="font-medium text-gray-900 block">{{ $tarea->prioridad }}</span>
                                </div>
                                @if($tarea->elementoConfiguracion)
                                    <div>
                                        <span class="text-gray-600">EC:</span>
                                        <span class="font-medium text-purple-700 block">{{ $tarea->elementoConfiguracion->nombre_ec }}</span>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-gray-600">EC:</span>
                                        <span class="font-medium text-gray-400 block">Sin EC</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Barra de progreso --}}
                            <div class="mb-2">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-600 font-medium">Progreso</span>
                                    <span class="font-semibold text-gray-900">{{ $progreso }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="{{ $colorBarra }} h-2.5 rounded-full transition-all duration-300"
                                         style="width: {{ $progreso }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para solicitar commit URL (Diseño minimalista) --}}
    <dialog id="modalCommit" class="modal">
        <div class="modal-box max-w-lg bg-white rounded-xl shadow-2xl border border-gray-100">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Commit Requerido</h3>
                    <p class="text-sm text-gray-500">Vincula el código de esta tarea</p>
                </div>
            </div>

            {{-- Contenido --}}
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Para marcar esta tarea como <span class="font-semibold text-gray-900">Completada</span>, necesitas proporcionar el enlace del commit de GitHub que implementa esta funcionalidad.
                </p>

                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">URL del Commit</span>
                    </label>
                    <input type="url"
                           id="commitUrlInput"
                           placeholder="https://github.com/usuario/repo/commit/abc123..."
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Ejemplo: https://github.com/usuario/repo/commit/7f3d9a2b...
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button onclick="document.getElementById('modalCommit').close()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmarCommit()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Confirmar
                </button>
            </div>
        </div>
    </dialog>

    {{-- Scripts para drag and drop --}}
    <script>
        let tareaEnMovimiento = null;
        let estadoDestino = null;

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("tareaId", ev.target.dataset.tareaId);
        }

        function drop(ev, nuevoEstado) {
            ev.preventDefault();
            const tareaId = ev.dataTransfer.getData("tareaId");
            const tareaElement = document.querySelector(`[data-tarea-id="${tareaId}"]`);

            if (!tareaElement) return;

            // Si se mueve a "Completada", pedir commit URL
            if (nuevoEstado === 'Completada') {
                tareaEnMovimiento = tareaId;
                estadoDestino = nuevoEstado;
                document.getElementById('commitUrlInput').value = '';
                document.getElementById('modalCommit').showModal();
                return;
            }

            // Para otros estados, actualizar directamente
            actualizarEstadoTarea(tareaId, nuevoEstado, null);
        }

        function confirmarCommit() {
            const commitUrl = document.getElementById('commitUrlInput').value.trim();

            if (!commitUrl) {
                alert('Por favor ingresa la URL del commit');
                return;
            }

            // Validar formato básico de URL de GitHub
            if (!commitUrl.includes('github.com') || !commitUrl.includes('commit/')) {
                alert('Por favor ingresa una URL válida de commit de GitHub');
                return;
            }

            document.getElementById('modalCommit').close();
            actualizarEstadoTarea(tareaEnMovimiento, estadoDestino, commitUrl);
        }

        function actualizarEstadoTarea(tareaId, nuevoEstado, commitUrl) {
            const tareaElement = document.querySelector(`[data-tarea-id="${tareaId}"]`);

            // Mover visualmente
            const column = document.getElementById(`column-${nuevoEstado}`);
            if (column && tareaElement) {
                column.appendChild(tareaElement);
            }

            // Preparar datos
            const requestData = {
                estado: nuevoEstado
            };

            if (commitUrl) {
                requestData.commit_url = commitUrl;
            }

            // Actualizar en el servidor
            fetch(`{{ route('proyectos.tareas.cambiar-fase', ['proyecto' => $proyecto->id, 'tarea' => '__TAREA_ID__']) }}`.replace('__TAREA_ID__', tareaId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito con la versión creada
                    if (commitUrl) {
                        const mensaje = data.message || 'Tarea completada exitosamente con commit registrado';
                        alert(mensaje);
                    }
                    // Recargar la página para actualizar métricas
                    window.location.reload();
                } else if (data.requiere_commit) {
                    // Si el servidor requiere commit, mostrar modal
                    tareaEnMovimiento = tareaId;
                    estadoDestino = nuevoEstado;
                    document.getElementById('modalCommit').showModal();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo actualizar el estado'));
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado');
                window.location.reload();
            });
        }
    </script>
</x-app-layout>
