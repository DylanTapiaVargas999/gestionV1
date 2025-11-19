{{-- Modal para crear nueva actividad en Cascada --}}
<dialog id="modalNuevaTarea" class="modal">
    <div class="modal-box w-11/12 max-w-3xl bg-white">
        <h3 class="font-bold text-2xl text-gray-900 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            Nueva Actividad
        </h3>

        <form method="POST" action="{{ route('proyectos.tareas.store', $proyecto) }}">
            @csrf

            {{-- SECCIÓN 1: Información Básica --}}
            <div class="bg-blue-50 rounded-xl p-5 mb-5 border-2 border-blue-200">
                <h4 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    1. Información Básica
                </h4>

                {{-- Nombre --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg> Nombre de la Actividad
                        </span>
                    </label>
                    <input type="text" name="nombre"
                           class="input input-bordered w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                           placeholder="Ej: Análisis de requisitos funcionales" required>
                </div>

                {{-- Descripción --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg> Descripción
                        </span>
                    </label>
                    <textarea name="descripcion"
                              class="textarea textarea-bordered bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                              rows="3"
                              placeholder="Detalla el alcance y objetivos de esta actividad..."></textarea>
                </div>
            </div>

            {{-- SECCIÓN 2: Planificación --}}
            <div class="bg-green-50 rounded-xl p-5 mb-5 border-2 border-green-200">
                <h4 class="text-sm font-bold text-green-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    2. Planificación
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Fase --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg> Fase
                            </span>
                        </label>
                        <select name="id_fase" class="select select-bordered bg-white text-gray-900 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200" required>
                            @foreach($fases as $fase)
                                <option value="{{ $fase->id_fase }}" {{ $faseActual && $faseActual->id_fase === $fase->id_fase ? 'selected' : '' }}>
                                    {{ $fase->nombre_fase }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Horas estimadas --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg> Horas Estimadas
                            </span>
                        </label>
                        <input type="number" name="horas_estimadas"
                               class="input input-bordered bg-white text-gray-900 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200"
                               placeholder="40" min="0" step="0.5">
                    </div>

                    {{-- Fecha inicio --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg> Fecha de Inicio
                            </span>
                        </label>
                        <input type="date" name="fecha_inicio"
                               class="input input-bordered bg-white text-gray-900 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>

                    {{-- Fecha fin --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg> Fecha de Fin
                            </span>
                        </label>
                        <input type="date" name="fecha_fin"
                               class="input input-bordered bg-white text-gray-900 border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: Asignación y Configuración --}}
            <div class="bg-purple-50 rounded-xl p-5 mb-6 border-2 border-purple-200">
                <h4 class="text-sm font-bold text-purple-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    3. Asignación y Configuración
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Elemento de Configuración --}}
                    @if(isset($elementosConfiguracion) && $elementosConfiguracion->count() > 0)
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg> Elemento de Configuración
                                </span>
                            </label>
                            <select name="id_ec" class="select select-bordered bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                                <option value="">Sin elemento de configuración</option>
                                @foreach($elementosConfiguracion as $ec)
                                    <option value="{{ $ec->id }}">
                                        {{ $ec->codigo_ec }} - {{ $ec->titulo }} ({{ $ec->tipo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Responsable --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg> Responsable
                            </span>
                        </label>
                        <select name="responsable" class="select select-bordered bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                            <option value="">Sin asignar</option>
                            @foreach($miembrosEquipo as $miembro)
                                <option value="{{ $miembro->id }}">{{ $miembro->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Prioridad --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-gray-900 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg> Prioridad
                            </span>
                        </label>
                        <select name="prioridad" class="select select-bordered bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                            <option value="1">1 - Muy Baja</option>
                            <option value="3">3 - Baja</option>
                            <option value="5" selected>5 - Media</option>
                            <option value="8">8 - Alta</option>
                            <option value="10">10 - Crítica</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="modalNuevaTarea.close()"
                        class="px-6 py-2.5 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Actividad
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>Cerrar</button>
    </form>
</dialog>

{{-- Script de validación de fechas --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalNuevaTarea = document.getElementById('modalNuevaTarea');
        if (modalNuevaTarea) {
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
            const fechaFin = document.querySelector('input[name="fecha_fin"]');

            if (fechaInicio && fechaFin) {
                fechaInicio.addEventListener('change', function() {
                    fechaFin.min = this.value;
                });

                fechaFin.addEventListener('change', function() {
                    if (this.value < fechaInicio.value) {
                        alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                        this.value = '';
                    }
                });
            }
        }
    });
</script>
