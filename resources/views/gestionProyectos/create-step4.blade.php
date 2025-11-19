<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <div class="text-sm breadcrumbs">
                    <ul class="text-gray-600">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a></li>
                        <li><a href="{{ route('proyectos.create') }}" class="hover:text-gray-900">Nuevo Proyecto</a></li>
                        <li class="text-gray-900 font-medium">Revisión Final</li>
                    </ul>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Proyecto</h1>
                <p class="mt-2 text-gray-600">Paso 4 de 4: Revisión Final</p>
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

                    <!-- Step 3 - Completed -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-green-600">Paso 3</div>
                            <div class="text-xs text-gray-500">Configurar Equipo</div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>

                    <!-- Step 4 - Current -->
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white">
                            <span class="text-lg font-semibold">4</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-black">Paso 4</div>
                            <div class="text-xs text-gray-500">Revisión</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Alert -->
            <div class="alert alert-success bg-green-50 border border-green-200 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium">¡Todo listo!</p>
                    <p class="text-sm mt-1">Revisa la información antes de crear el proyecto. Puedes volver a editar cualquier paso.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- 1. Información del Proyecto -->
                    <div class="card bg-white shadow-md">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Información del Proyecto</h2>
                                <a href="{{ route('proyectos.create') }}" class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>

                            <dl class="grid grid-cols-1 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">Nombre del Proyecto</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $proyectoData['nombre'] }}</dd>
                                </div>

                                @if($proyectoData['descripcion'])
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $proyectoData['descripcion'] }}</dd>
                                </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Código</dt>
                                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $proyectoData['codigo'] }}</dd>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Metodología</dt>
                                        <dd class="mt-1">
                                            <span class="badge badge-primary">{{ $proyectoData['metodologia_nombre'] }}</span>
                                        </dd>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Fecha de Inicio</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($proyectoData['fecha_inicio'])->format('d/m/Y') }}
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Fecha de Fin</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($proyectoData['fecha_fin'])->format('d/m/Y') }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <dt class="text-sm font-medium text-blue-700">Duración del Proyecto</dt>
                                    <dd class="mt-1 text-2xl font-bold text-blue-900">
                                        {{ \Carbon\Carbon::parse($proyectoData['fecha_inicio'])->diffInDays(\Carbon\Carbon::parse($proyectoData['fecha_fin'])) }} días
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- 2. Elementos de Configuración -->
                    <div class="card bg-white shadow-md">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Elementos de Configuración</h2>
                                <a href="{{ route('proyectos.create-step2') }}" class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>

                            @if($plantillas->isEmpty())
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p>No se seleccionaron elementos de configuración</p>
                                    <p class="text-sm mt-1">Podrás crearlos manualmente después</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($plantillas as $plantilla)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                            <div class="flex items-start gap-3">
                                                <span class="text-2xl">{{ $plantilla->tipo_icono }}</span>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <h4 class="font-semibold text-gray-900">{{ $plantilla->nombre }}</h4>
                                                        <span class="badge {{ $plantilla->tipo_badge }} badge-sm">{{ $plantilla->tipo }}</span>
                                                    </div>
                                                    @if($plantilla->tarea_nombre)
                                                        <p class="text-sm text-blue-700 mt-2">
                                                            ✓ Tarea: "{{ $plantilla->tarea_nombre }}"
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Equipo del Proyecto -->
                    <div class="card bg-white shadow-md">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Equipo del Proyecto</h2>
                                <a href="{{ route('proyectos.create-step3') }}" class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="space-y-2">
                                @foreach($miembrosData as $miembro)
                                    @if($miembro['usuario'] && $miembro['rol'])
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="avatar placeholder">
                                                <div class="bg-blue-500 text-white rounded-full w-10 h-10 flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ strtoupper(substr($miembro['usuario']->nombre_completo, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $miembro['usuario']->nombre_completo }}</p>
                                                <p class="text-xs text-gray-500">{{ $miembro['usuario']->correo }}</p>
                                            </div>
                                            <span class="badge badge-outline">{{ $miembro['rol']->nombre }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Summary Card -->
                    <div class="card bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
                        <div class="card-body">
                            <h3 class="text-lg font-bold mb-4">Resumen</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center pb-2 border-b border-white/20">
                                    <span>Elementos de Configuración</span>
                                    <span class="font-bold text-xl">{{ count($plantillas) }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-white/20">
                                    <span>Tareas Base</span>
                                    <span class="font-bold text-xl">{{ $plantillas->where('tarea_nombre', '!=', null)->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Miembros del Equipo</span>
                                    <span class="font-bold text-xl">{{ count($miembrosData) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="card bg-white shadow-md">
                        <div class="card-body">
                            <form action="{{ route('proyectos.store') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn bg-green-600 text-white hover:bg-green-700 border-0 w-full mb-3"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Confirmar y Crear Proyecto
                                </button>
                            </form>
                            <a
                                href="{{ route('proyectos.create-step3') }}"
                                class="btn btn-outline text-gray-700 bg-gray-50 border-2 border-gray-300 hover:text-blue-600 hover:border-blue-500 hover:bg-blue-100 w-full"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Volver a Editar
                            </a>

                            <a
                                href="{{ route('proyectos.cancel') }}"
                                class="btn btn-ghost w-full text-red-600 hover:bg-red-50 mt-2"
                            >
                                Cancelar Proceso
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
