<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Tarea
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Proyecto: <span class="font-semibold">{{ $proyecto->nombre }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.tareas.index', $proyecto) }}" class="btn btn-ghost btn-sm">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Tareas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="card bg-white shadow-xl border border-gray-200">
                <div class="card-body">
                    <form action="{{ route('proyectos.tareas.update', [$proyecto, $tarea]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Nombre de la Tarea -->
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Nombre de la Tarea *</span>
                                </label>
                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{ old('nombre', $tarea->nombre) }}"
                                    class="input input-bordered w-full bg-white text-gray-900"
                                    required
                                />
                                @error('nombre')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Descripción</span>
                                </label>
                                <textarea
                                    name="descripcion"
                                    rows="3"
                                    class="textarea textarea-bordered w-full bg-white text-gray-900"
                                >{{ old('descripcion', $tarea->descripcion) }}</textarea>
                                @error('descripcion')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Responsable -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Responsable</span>
                                </label>
                                <select
                                    name="responsable"
                                    class="select select-bordered w-full bg-white text-gray-900"
                                >
                                    <option value="" class="text-gray-500">Sin asignar</option>
                                    @foreach($miembrosEquipo as $miembro)
                                        <option
                                            value="{{ $miembro->id }}"
                                            {{ old('responsable', $tarea->responsable) == $miembro->id ? 'selected' : '' }}
                                            class="text-gray-900"
                                        >
                                            {{ $miembro->nombre_completo }} ({{ $miembro->correo }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsable')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Elemento de Configuración -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Elemento de Configuración</span>
                                </label>
                                <select
                                    name="id_ec"
                                    class="select select-bordered w-full bg-white text-gray-900"
                                >
                                    <option value="" class="text-gray-500">Sin EC asociado</option>
                                    @foreach($elementosConfiguracion as $ec)
                                        <option
                                            value="{{ $ec->id }}"
                                            {{ old('id_ec', $tarea->id_ec) == $ec->id ? 'selected' : '' }}
                                            class="text-gray-900"
                                        >
                                            {{ $ec->codigo_ec }} - {{ $ec->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_ec')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Fase -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Fase *</span>
                                </label>
                                <select
                                    name="id_fase"
                                    class="select select-bordered w-full bg-white text-gray-900"
                                    required
                                >
                                    @foreach($fases as $fase)
                                        <option
                                            value="{{ $fase->id_fase }}"
                                            {{ old('id_fase', $tarea->id_fase) == $fase->id_fase ? 'selected' : '' }}
                                            class="text-gray-900"
                                        >
                                            {{ $fase->nombre_fase }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_fase')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Estado</span>
                                </label>
                                <select
                                    name="estado"
                                    class="select select-bordered w-full bg-white text-gray-900"
                                >
                                    <option value="PENDIENTE" {{ old('estado', $tarea->estado) == 'PENDIENTE' ? 'selected' : '' }} class="text-gray-900">Pendiente</option>
                                    <option value="EN_PROGRESO" {{ old('estado', $tarea->estado) == 'EN_PROGRESO' ? 'selected' : '' }} class="text-gray-900">En Progreso</option>
                                    <option value="EN_REVISION" {{ old('estado', $tarea->estado) == 'EN_REVISION' ? 'selected' : '' }} class="text-gray-900">En Revisión</option>
                                    <option value="COMPLETADA" {{ old('estado', $tarea->estado) == 'COMPLETADA' ? 'selected' : '' }} class="text-gray-900">Completada</option>
                                    <option value="BLOQUEADA" {{ old('estado', $tarea->estado) == 'BLOQUEADA' ? 'selected' : '' }} class="text-gray-900">Bloqueada</option>
                                </select>
                                @error('estado')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Prioridad -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Prioridad</span>
                                </label>
                                <select
                                    name="prioridad"
                                    class="select select-bordered w-full bg-white text-gray-900"
                                >
                                    <option value="1" {{ old('prioridad', $tarea->prioridad) == 1 ? 'selected' : '' }} class="text-gray-900">Baja</option>
                                    <option value="2" {{ old('prioridad', $tarea->prioridad) == 2 ? 'selected' : '' }} class="text-gray-900">Normal</option>
                                    <option value="3" {{ old('prioridad', $tarea->prioridad) == 3 ? 'selected' : '' }} class="text-gray-900">Media</option>
                                    <option value="4" {{ old('prioridad', $tarea->prioridad) == 4 ? 'selected' : '' }} class="text-gray-900">Alta</option>
                                    <option value="5" {{ old('prioridad', $tarea->prioridad) == 5 ? 'selected' : '' }} class="text-gray-900">Crítica</option>
                                </select>
                                @error('prioridad')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Story Points (Scrum) -->
                            @if($proyecto->metodologia->nombre === 'Scrum')
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700">Story Points</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="story_points"
                                        value="{{ old('story_points', $tarea->story_points) }}"
                                        min="0"
                                        class="input input-bordered w-full bg-white text-gray-900"
                                    />
                                    @error('story_points')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Sprint -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700">Sprint</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="sprint"
                                        value="{{ old('sprint', $tarea->sprint) }}"
                                        placeholder="Sprint 1"
                                        class="input input-bordered w-full bg-white text-gray-900"
                                    />
                                    @error('sprint')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            @endif

                            <!-- Horas Estimadas (Cascada) -->
                            @if($proyecto->metodologia->nombre === 'Cascada')
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700">Horas Estimadas</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="horas_estimadas"
                                        value="{{ old('horas_estimadas', $tarea->horas_estimadas) }}"
                                        min="0"
                                        step="0.5"
                                        class="input input-bordered w-full bg-white text-gray-900"
                                    />
                                    @error('horas_estimadas')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Entregable -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700">Entregable</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="entregable"
                                        value="{{ old('entregable', $tarea->entregable) }}"
                                        placeholder="Documento, código, etc."
                                        class="input input-bordered w-full bg-white text-gray-900"
                                    />
                                    @error('entregable')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            @endif

                            <!-- Fecha Inicio -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Fecha Inicio</span>
                                </label>
                                <input
                                    type="date"
                                    name="fecha_inicio"
                                    value="{{ old('fecha_inicio', $tarea->fecha_inicio ? \Carbon\Carbon::parse($tarea->fecha_inicio)->format('Y-m-d') : '') }}"
                                    class="input input-bordered w-full bg-white text-gray-900"
                                />
                                @error('fecha_inicio')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Fecha Fin -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Fecha Fin</span>
                                </label>
                                <input
                                    type="date"
                                    name="fecha_fin"
                                    value="{{ old('fecha_fin', $tarea->fecha_fin ? \Carbon\Carbon::parse($tarea->fecha_fin)->format('Y-m-d') : '') }}"
                                    class="input input-bordered w-full bg-white text-gray-900"
                                />
                                @error('fecha_fin')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Notas -->
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">Notas Adicionales</span>
                                </label>
                                <textarea
                                    name="notas"
                                    rows="3"
                                    class="textarea textarea-bordered w-full bg-white text-gray-900"
                                >{{ old('notas', $tarea->notas) }}</textarea>
                                @error('notas')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- URL del Commit (se muestra cuando el estado es COMPLETADA) -->
                            <div class="form-control md:col-span-2" id="commitUrlContainer" style="display: none;">
                                <label class="label">
                                    <span class="label-text font-medium text-gray-700">
                                        URL del Commit *
                                        <span class="text-sm text-gray-500 font-normal">(Requerido para completar la tarea)</span>
                                    </span>
                                </label>
                                <input
                                    type="url"
                                    name="commit_url"
                                    id="commit_url"
                                    value="{{ old('commit_url', $tarea->commit_url) }}"
                                    placeholder="https://github.com/usuario/repositorio/commit/abc123..."
                                    class="input input-bordered w-full bg-white text-gray-900"
                                />
                                <label class="label">
                                    <span class="label-text-alt text-gray-600">
                                        Pega aquí la URL del commit de GitHub que completa esta tarea
                                    </span>
                                </label>
                                @error('commit_url')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            @if($tarea->commit)
                                <!-- Información del commit existente -->
                                <div class="md:col-span-2 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold text-green-800">Commit Asociado</span>
                                    </div>
                                    <div class="space-y-1 text-sm text-gray-700">
                                        <p><strong>Hash:</strong> <code class="bg-white px-2 py-1 rounded">{{ Str::limit($tarea->commit->hash_commit, 12, '') }}</code></p>
                                        @if($tarea->commit->mensaje)
                                            <p><strong>Mensaje:</strong> {{ $tarea->commit->mensaje }}</p>
                                        @endif
                                        @if($tarea->commit->autor)
                                            <p><strong>Autor:</strong> {{ $tarea->commit->autor }}</p>
                                        @endif
                                        <p>
                                            <a href="{{ $tarea->commit->url_repositorio }}/commit/{{ $tarea->commit->hash_commit }}"
                                               target="_blank"
                                               class="text-blue-600 hover:underline inline-flex items-center gap-1">
                                                Ver en GitHub
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <script>
                            // Mostrar/ocultar campo commit_url según el estado seleccionado
                            document.addEventListener('DOMContentLoaded', function() {
                                const estadoSelect = document.querySelector('select[name="estado"]');
                                const commitContainer = document.getElementById('commitUrlContainer');
                                const commitInput = document.getElementById('commit_url');

                                function toggleCommitField() {
                                    const estadoSeleccionado = estadoSelect.value;
                                    const estadosCompletados = ['COMPLETADA', 'COMPLETADO', 'Done', 'DONE', 'Completado', 'Finalizado', 'FINALIZADO'];

                                    if (estadosCompletados.includes(estadoSeleccionado)) {
                                        commitContainer.style.display = 'block';
                                        commitInput.required = true;
                                    } else {
                                        commitContainer.style.display = 'none';
                                        commitInput.required = false;
                                    }
                                }

                                estadoSelect.addEventListener('change', toggleCommitField);
                                toggleCommitField(); // Ejecutar al cargar la página
                            });
                        </script>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                            <button
                                type="button"
                                onclick="if(confirm('¿Estás seguro de eliminar esta tarea?')) { document.getElementById('formEliminar').submit(); }"
                                class="btn btn-error btn-outline"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Tarea
                            </button>

                            <div class="flex gap-3">
                                <a
                                    href="{{ route('proyectos.tareas.index', $proyecto) }}"
                                    class="btn btn-ghost text-gray-700"
                                >
                                    Cancelar
                                </a>
                                <button
                                    type="submit"
                                    class="btn bg-black text-white hover:bg-gray-800 border-0"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>

                    </form>

                    <!-- Formulario Eliminar (oculto) -->
                    <form
                        id="formEliminar"
                        action="{{ route('proyectos.tareas.destroy', [$proyecto, $tarea]) }}"
                        method="POST"
                        style="display: none;"
                    >
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
