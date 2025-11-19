<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ☀️ Daily Scrum
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ $proyecto->nombre }} • {{ $sprintActual }} • Stand-up diario
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Navegación Scrum -->
            <x-scrum.navigation :proyecto="$proyecto" active="daily" />

            <!-- Información del Daily Scrum -->
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white rounded-lg shadow-sm mb-6 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Daily Scrum - {{ $sprintActual }}</h3>
                        <p class="text-yellow-100 mb-4">
                            Reunión diaria de 15 minutos • {{ now()->format('d/m/Y') }}
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    ⏰
                                </div>
                                <span class="text-sm">Duración: 15 min máximo</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    👥
                                </div>
                                <span class="text-sm">{{ $miembrosEquipo->count() }} miembros del equipo</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-6xl opacity-80">☀️</div>
                </div>
            </div>

            <!-- Las 3 preguntas del Daily Scrum -->
            <div class="bg-white rounded-lg shadow-sm border mb-6 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Las 3 Preguntas Clave</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-2xl mb-2">✅</div>
                        <h4 class="font-semibold text-blue-900 mb-1">¿Qué hice ayer?</h4>
                        <p class="text-sm text-blue-700">Trabajo completado desde el último Daily Scrum</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl mb-2">🎯</div>
                        <h4 class="font-semibold text-green-900 mb-1">¿Qué haré hoy?</h4>
                        <p class="text-sm text-green-700">Plan de trabajo para el día actual</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="text-2xl mb-2">🚧</div>
                        <h4 class="font-semibold text-red-900 mb-1">¿Hay impedimentos?</h4>
                        <p class="text-sm text-red-700">Obstáculos que bloquean el progreso</p>
                    </div>
                </div>
            </div>

            <!-- Estado por Miembro del Equipo -->
            <div class="space-y-4">
                @forelse($miembrosEquipo as $miembro)
                    @php
                        $tareasDelMiembro = $tareasDelSprint->get($miembro->id, collect());
                        // Estados completados: Done, Completado, Completada, DONE, COMPLETADA
                        $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
                        $tareasCompletadas = $tareasDelMiembro->whereIn('estado', $estadosCompletados)->count();
                        $tareasEnProgreso = $tareasDelMiembro->whereNotIn('estado', $estadosCompletados)->count();
                    @endphp

                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="p-6">
                            <!-- Header del miembro -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($miembro->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $miembro->nombre }}</h3>
                                        <p class="text-sm text-gray-600">{{ $miembro->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-sm bg-green-100 text-green-800">
                                        {{ $tareasCompletadas }} completadas
                                    </span>
                                    <span class="badge badge-sm bg-blue-100 text-blue-800">
                                        {{ $tareasEnProgreso }} en progreso
                                    </span>
                                </div>
                            </div>

                            <!-- Tareas del miembro -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <!-- ¿Qué hice ayer? -->
                                <div class="space-y-2">
                                    <h4 class="font-medium text-blue-900 flex items-center gap-2">
                                        <span class="text-lg">✅</span>
                                        ¿Qué hice ayer?
                                    </h4>
                                    <div class="space-y-2">
                                        @forelse($tareasDelMiembro->whereIn('estado', $estadosCompletados) as $tarea)
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                                <p class="text-sm font-medium text-green-900">{{ $tarea->nombre }}</p>
                                                @if($tarea->story_points)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 bg-green-100 text-green-800 text-xs font-bold rounded-full mt-1">
                                                        {{ $tarea->story_points }}
                                                    </span>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-gray-500">
                                                <p class="text-sm">No hay tareas completadas ayer</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- ¿Qué haré hoy? -->
                                <div class="space-y-2">
                                    <h4 class="font-medium text-green-900 flex items-center gap-2">
                                        <span class="text-lg">🎯</span>
                                        ¿Qué haré hoy?
                                    </h4>
                                    <div class="space-y-2">
                                        @forelse($tareasDelMiembro->whereNotIn('estado', $estadosCompletados) as $tarea)
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                <p class="text-sm font-medium text-blue-900">{{ $tarea->nombre }}</p>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-xs text-blue-700">{{ $tarea->fase->nombre_fase ?? 'Sin fase' }}</span>
                                                    @if($tarea->story_points)
                                                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">
                                                            {{ $tarea->story_points }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-gray-500">
                                                <p class="text-sm">No hay tareas planificadas para hoy</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Impedimentos -->
                                <div class="space-y-2">
                                    <h4 class="font-medium text-red-900 flex items-center gap-2">
                                        <span class="text-lg">🚧</span>
                                        Impedimentos
                                    </h4>
                                    <div class="space-y-2">
                                        <!-- Impedimentos simulados - en una implementación real vendrían de la base de datos -->
                                        @if($tareasEnProgreso > 0 && $tareasCompletadas === 0)
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                                <p class="text-sm font-medium text-red-900">Tareas bloqueadas</p>
                                                <p class="text-xs text-red-700">Revisar dependencias</p>
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-gray-500">
                                                <p class="text-sm">Sin impedimentos reportados</p>
                                                <button onclick="reportarImpedimento('{{ $miembro->id }}')" class="btn btn-xs btn-outline btn-error mt-1">
                                                    Reportar impedimento
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Área de notas -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-700">💬 Notas adicionales:</span>
                                    <input type="text" placeholder="Comentarios del daily scrum..."
                                           class="input input-sm input-bordered bg-white flex-1">
                                    <button class="btn btn-sm btn-outline">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
                        <div class="text-4xl mb-4">👥</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay miembros en el equipo</h3>
                        <p class="text-gray-600">Agrega miembros al equipo para realizar el Daily Scrum</p>
                    </div>
                @endforelse
            </div>

            <!-- Acciones del Daily Scrum -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">Acciones del Daily Scrum</h3>
                        <p class="text-sm text-gray-600">Registra el resultado de la reunión diaria</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="guardarDailyScrum()" class="btn bg-yellow-500 text-white hover:bg-yellow-600">
                            💾 Guardar Daily Scrum
                        </button>
                        <button onclick="modalImpedimentos.showModal()" class="btn btn-outline btn-error">
                            🚧 Gestionar Impedimentos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reportar Impedimento -->
    <dialog id="modalImpedimentos" class="modal">
        <div class="modal-box bg-white">
            <h3 class="font-bold text-lg text-black mb-4">🚧 Reportar Impedimento</h3>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text text-black font-semibold">Miembro del equipo</span></label>
                <select class="select select-bordered bg-white text-black">
                    @foreach($miembrosEquipo as $miembro)
                        <option value="{{ $miembro->id }}">{{ $miembro->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text text-black font-semibold">Descripción del impedimento</span></label>
                <textarea class="textarea textarea-bordered bg-white text-black" rows="3"
                          placeholder="Describe el obstáculo que está bloqueando el progreso..."></textarea>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text text-black font-semibold">Prioridad</span></label>
                <select class="select select-bordered bg-white text-black">
                    <option value="alta">🔴 Alta - Bloquea completamente</option>
                    <option value="media" selected>🟡 Media - Ralentiza el trabajo</option>
                    <option value="baja">🟢 Baja - Puede esperar</option>
                </select>
            </div>

            <div class="modal-action">
                <button type="button" onclick="modalImpedimentos.close()" class="btn btn-ghost">Cancelar</button>
                <button type="button" onclick="crearImpedimento()" class="btn btn-error">Reportar Impedimento</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>Cerrar</button>
        </form>
    </dialog>

    <script>
        function reportarImpedimento(miembroId) {
            modalImpedimentos.showModal();
        }

        function crearImpedimento() {
            // Implementar creación de impedimento
            console.log('Crear impedimento');
            modalImpedimentos.close();

            // Mostrar mensaje de éxito
            alert('Impedimento reportado. El Scrum Master será notificado.');
        }

        function guardarDailyScrum() {
            // Recopilar todas las notas y actualizaciones
            const notas = document.querySelectorAll('input[placeholder="Comentarios del daily scrum..."]');
            const dailyData = {
                fecha: new Date().toISOString().split('T')[0],
                sprint: '{{ $sprintActual }}',
                participantes: {{ $miembrosEquipo->pluck('id') }},
                notas: Array.from(notas).map(nota => nota.value).filter(n => n.trim())
            };

            console.log('Guardar Daily Scrum:', dailyData);

            // Mostrar confirmación
            alert('Daily Scrum guardado exitosamente');
        }

        // Auto-focus en el primer input cuando se abre un modal
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('dialog').forEach(modal => {
                modal.addEventListener('open', function() {
                    const firstInput = this.querySelector('input, textarea, select');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 100);
                    }
                });
            });
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
