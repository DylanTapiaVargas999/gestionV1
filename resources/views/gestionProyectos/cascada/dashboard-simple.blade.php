<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GESTIÓN CASCADA - {{ $proyecto->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Métricas básicas --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-900">Fase Actual</h3>
                            <p class="text-2xl font-bold text-blue-800">{{ $faseActual->nombre_fase ?? 'Ninguna' }}</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-900">Progreso</h3>
                            @php
                                $totalFases = $fases->count();
                                $fasesCompletadas = collect($progresoPorFase)->where('fase_completada', true)->count();
                                $progreso = $totalFases > 0 ? round(($fasesCompletadas / $totalFases) * 100) : 0;
                            @endphp
                            <p class="text-2xl font-bold text-green-800">{{ $progreso }}%</p>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-yellow-900">Duración</h3>
                            <p class="text-2xl font-bold text-yellow-800">{{ $duracionTotal }} días</p>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-purple-900">Tareas</h3>
                            <p class="text-2xl font-bold text-purple-800">{{ $tareas->count() }}</p>
                        </div>
                    </div>

                    {{-- Navegación de pestañas --}}
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showTab('fases')" id="tab-fases" class="tab-button border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                                Progreso por Fases
                            </button>
                            <button onclick="showTab('cronograma')" id="tab-cronograma" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Cronograma Maestro
                            </button>
                            <button onclick="showTab('gantt')" id="tab-gantt" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Diagrama de Gantt
                            </button>
                        </nav>
                    </div>

                    {{-- Contenido de pestañas --}}
                    <div id="content-fases" class="tab-content" style="display: block;">
                        <div style="background-color: #fef3c7; border: 1px solid #d97706; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #92400e; margin-bottom: 8px;">🔍 PROGRESO POR FASES</h3>
                            <p style="color: #b45309;">Fases totales: {{ $fases->count() }}</p>
                            <p style="color: #b45309;">Fase actual: {{ $faseActual->nombre_fase ?? 'Ninguna' }}</p>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($fases as $index => $fase)
                                <div style="background-color: white; border: 1px solid #d1d5db; padding: 16px; border-radius: 4px;">
                                    <strong>{{ $index + 1 }}. {{ $fase->nombre_fase }}</strong>
                                    <br>
                                    <small style="color: #6b7280;">ID: {{ $fase->id_fase }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="content-cronograma" class="tab-content" style="display: none;">
                        <div style="background-color: #dcfce7; border: 1px solid #16a34a; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #166534; margin-bottom: 8px;">📋 CRONOGRAMA MAESTRO</h3>
                            <p style="color: #15803d;">Total de tareas: {{ $tareas->count() }}</p>
                        </div>

                        @if($tareas->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach($tareas->take(10) as $index => $tarea)
                                    <div style="background-color: white; border: 1px solid #d1d5db; padding: 12px; border-radius: 4px;">
                                        <strong>{{ $index + 1 }}. {{ $tarea->nombre }}</strong>
                                        <br>
                                        <small style="color: #6b7280;">Estado: {{ $tarea->estado }}</small>
                                        <br>
                                        <small style="color: #6b7280;">Fase: {{ $tarea->fase->nombre_fase ?? 'Sin fase' }}</small>
                                    </div>
                                @endforeach

                                @if($tareas->count() > 10)
                                    <p style="color: #6b7280; text-align: center;">... y {{ $tareas->count() - 10 }} tareas más</p>
                                @endif
                            </div>
                        @else
                            <p style="color: #6b7280; text-align: center; padding: 32px 0;">No hay tareas en este proyecto.</p>
                        @endif
                    </div>

                    <div id="content-gantt" class="tab-content" style="display: none;">
                        <div style="background-color: #f3e8ff; border: 1px solid #9333ea; padding: 16px; border-radius: 8px;">
                            <h3 style="font-size: 18px; font-weight: 600; color: #6b21a8; margin-bottom: 8px;">📊 DIAGRAMA DE GANTT</h3>
                            <p style="color: #7c3aed;">Vista de diagrama de Gantt (en desarrollo)</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('Script cargado');

        function showTab(tabName) {
            console.log('Cambiando a tab:', tabName);

            // Ocultar todos los contenidos
            const allContents = document.querySelectorAll('.tab-content');
            console.log('Contenidos encontrados:', allContents.length);
            allContents.forEach(el => {
                el.classList.add('hidden');
                el.style.display = 'none'; // Forzar ocultación
            });

            // Desactivar todos los botones
            const allButtons = document.querySelectorAll('.tab-button');
            console.log('Botones encontrados:', allButtons.length);
            allButtons.forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            // Mostrar el contenido seleccionado
            const activeContent = document.getElementById('content-' + tabName);
            console.log('Contenido a mostrar:', activeContent);
            if (activeContent) {
                activeContent.classList.remove('hidden');
                activeContent.style.display = 'block'; // Forzar mostrar
            }

            // Activar el botón seleccionado
            const activeTab = document.getElementById('tab-' + tabName);
            console.log('Tab a activar:', activeTab);
            if (activeTab) {
                activeTab.classList.remove('border-transparent', 'text-gray-500');
                activeTab.classList.add('border-blue-500', 'text-blue-600');
            }
        }

        // Mostrar el primer tab por defecto cuando carga la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado, mostrando tab por defecto');
            showTab('fases');
        });
    </script>
</x-app-layout>
