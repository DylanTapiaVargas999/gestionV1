<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    GESTIÓN CASCADA
                </h2>
                <p class="text-sm text-gray-600 mt-1 font-medium">
                    {{ $proyecto->nombre }} • Metodología Cascada (Waterfall)
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-ghost btn-sm">
                    ← Volver al proyecto
                </a>
                <button onclick="modalNuevaTarea.showModal()" class="btn btn-sm bg-gray-600 text-white hover:bg-gray-700 rounded-lg font-semibold">
                    + Nueva Actividad
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Métricas del Proyecto (siempre visibles) --}}
            @include('gestionProyectos.cascada.partials.metricas')

            {{-- Navegación por Tabs --}}
            <div class="mb-6">
                <div class="bg-white rounded-t-lg border-x border-t border-gray-200">
                    <nav class="flex border-b border-gray-200">
                        <button onclick="mostrarTab('progreso')" id="tab-progreso"
                                class="tab-btn py-4 px-6 border-b-2 border-blue-600 text-blue-600 font-semibold text-sm whitespace-nowrap">
                            PROGRESO POR FASES
                        </button>
                        <button onclick="mostrarTab('cronograma')" id="tab-cronograma"
                                class="tab-btn py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold text-sm whitespace-nowrap">
                            CRONOGRAMA MAESTRO
                        </button>
                        <button onclick="mostrarTab('gantt')" id="tab-gantt"
                                class="tab-btn py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold text-sm whitespace-nowrap">
                            DIAGRAMA DE GANTT
                        </button>
                    </nav>
                </div>

                {{-- Contenido de Tabs --}}
                {{-- Tab: Progreso por Fases --}}
                <div id="content-progreso" class="tab-content" style="display: block;">
                    @include('gestionProyectos.cascada.partials.cronologia')
                    @include('gestionProyectos.cascada.partials.progreso-fases')
                </div>

                {{-- Tab: Cronograma Maestro --}}
                <div id="content-cronograma" class="tab-content" style="display: none;">
                    @include('gestionProyectos.cascada.partials.cronograma-maestro')
                </div>

                {{-- Tab: Diagrama de Gantt --}}
                <div id="content-gantt" class="tab-content" style="display: none;">
                    @include('gestionProyectos.cascada.partials.diagrama-gantt')
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Nueva Actividad --}}
    @include('gestionProyectos.cascada.partials.modal-nueva-tarea')

    {{-- Script para manejo de tabs --}}
    <script>
        function mostrarTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            // Desactivar todos los botones de tab
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Mostrar el contenido seleccionado
            document.getElementById('content-' + tabName).style.display = 'block';

            // Activar el botón de tab seleccionado
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-blue-600', 'text-blue-600');
        }
    </script>
</x-app-layout>
