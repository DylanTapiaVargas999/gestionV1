<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ComiteCambio;
use App\Models\Equipo;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Metodologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Sprint;
use Illuminate\Support\Facades\Schema;
use App\Notifications\Proyecto\UsuarioAsignadoAProyecto;
use App\Notifications\Proyecto\UsuarioAsignadoComoLider;
use App\Notifications\Proyecto\MiembroAgregadoACCB;

class ProyectoController extends Controller
{
    /**
     * Mostrar lista de TODOS los proyectos (donde soy líder + asignados).
     */
    public function index()
    {
        $usuarioId = Auth::user()->id;

        // Proyectos donde soy LÍDER de algún equipo
        $proyectosLider = Proyecto::with(['equipos', 'creador'])
            ->whereHas('equipos', function($q) use ($usuarioId) {
                $q->where('lider_id', $usuarioId);
            })
            ->get();

        // Resolver metodologías para proyectos donde soy líder
        $metIdsLider = $proyectosLider->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metMapLider = Metodologia::whereIn('id_metodologia', $metIdsLider)->get()->keyBy('id_metodologia')->map(fn($m) => $m->nombre);

        $proyectosLider = $proyectosLider->map(function($proyecto) use ($metMapLider) {
            $metNombre = $metMapLider[$proyecto->id_metodologia] ?? 'No especificada';

            return [
                'id' => $proyecto->id,
                'codigo' => $proyecto->codigo,
                'nombre' => $proyecto->nombre,
                'id_metodologia' => $proyecto->id_metodologia,
                'metodologia' => $metNombre,
                'total_equipos' => $proyecto->equipos->count(),
                'total_miembros' => $proyecto->equipos->sum(fn($e) => $e->miembros()->count()),
                'mi_rol' => 'Líder',
                'creado_en' => $proyecto->creado_en,
                'estado' => 'Activo', // temporal
            ];
        });

        // Proyectos donde soy MIEMBRO (pero no líder)
        $proyectosAsignados = Proyecto::with(['equipos' => function($query) use ($usuarioId) {
                $query->whereHas('miembros', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })->with(['miembros' => function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                }]);
            }])
            ->whereHas('equipos.miembros', function ($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })
            ->whereDoesntHave('equipos', function($q) use ($usuarioId) {
                $q->where('lider_id', $usuarioId);
            })
            ->get();

        // Resolver metodologías para proyectos en los que participo
        $metIdsAsignados = $proyectosAsignados->pluck('id_metodologia')->filter()->unique()->values()->all();
        $metMapAsignados = Metodologia::whereIn('id_metodologia', $metIdsAsignados)->get()->keyBy('id_metodologia')->map(fn($m) => $m->nombre);

        $proyectosAsignados = $proyectosAsignados->map(function($proyecto) use ($metMapAsignados) {
                $miEquipo = $proyecto->equipos->first();
                $miembro = $miEquipo->miembros->first();

                // Obtener el rol desde el pivot
                $rolId = $miembro->pivot->rol_id;
                $miRol = \App\Models\Rol::find($rolId)->nombre ?? 'Miembro';

                $metNombre = $metMapAsignados[$proyecto->id_metodologia] ?? 'No especificada';

                return [
                    'id' => $proyecto->id,
                    'codigo' => $proyecto->codigo,
                    'nombre' => $proyecto->nombre,
                    'id_metodologia' => $proyecto->id_metodologia,
                    'metodologia' => $metNombre,
                    'total_equipos' => $proyecto->equipos->count(),
                    'total_miembros' => $proyecto->equipos->sum(fn($e) => $e->miembros()->count()),
                    'mi_rol' => $miRol,
                    'nombre_equipo' => $miEquipo->nombre ?? 'Sin equipo',
                    'creado_en' => $proyecto->creado_en,
                    'estado' => 'Activo', // temporal
                    ];
                });

        // Combinar ambas colecciones y ordenar por fecha
        $todosLosProyectos = $proyectosLider->concat($proyectosAsignados)
            ->sortByDesc('creado_en')
            ->values();

        return view('gestionProyectos.index', compact('todosLosProyectos'));
    }

    /**
     * Mostrar dashboard interno de un proyecto específico.
     */
    public function show(Proyecto $proyecto)
    {
        // Verificar que el usuario tenga acceso (es líder o miembro)
        $usuarioId = Auth::user()->id;

        // Verificar si es líder de algún equipo del proyecto
        $esLider = $proyecto->equipos()->where('lider_id', $usuarioId)->exists();

        $esMiembro = $proyecto->equipos()->whereHas('miembros', function($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId);
        })->exists();

        if (!$esLider && !$esMiembro) {
            abort(403, 'No tienes acceso a este proyecto.');
        }

        // Cargar relaciones con eager loading del rol desde el pivot
        $proyecto->load([
            'equipos' => function($query) {
                $query->with(['miembros' => function($q) {
                    // Aquí los miembros vienen con el pivot automáticamente
                }, 'lider']);
            },
            'creador',
            'metodologia',
            'elementosConfiguracion',
            'solicitudesCambio'
        ]);

        // Detectar el ROL del usuario en este proyecto
        $miRol = null;
        $miEquipo = null;

        if ($esLider) {
            $miRol = 'líder';
        } else {
            // Buscar el rol del usuario en algún equipo
            foreach ($proyecto->equipos as $equipo) {
                $miembro = $equipo->miembros->firstWhere('id', $usuarioId);
                if ($miembro) {
                    $rolId = $miembro->pivot->rol_id;
                    $rolObj = \App\Models\Rol::find($rolId);
                    $miRol = strtolower($rolObj->nombre); // 'desarrollador', etc.
                    $miEquipo = $equipo;
                    break;
                }
            }
        }

        // Cargar los roles manualmente para cada miembro
        foreach ($proyecto->equipos as $equipo) {
            foreach ($equipo->miembros as $miembro) {
                $miembro->rol_proyecto = \App\Models\Rol::find($miembro->pivot->rol_id);
            }
        }

        // Obtener el rol específico del usuario para pasarlo a la vista
        $rolEnProyecto = null;
        if (!$esLider && $miEquipo) {
            $miembro = $miEquipo->miembros->firstWhere('id', $usuarioId);
            if ($miembro) {
                $rolEnProyecto = \App\Models\Rol::find($miembro->pivot->rol_id);
            }
        }

        // **CARGAR DATOS SGCS INTEGRADOS** usando el servicio
        $sgcsService = new \App\Services\SGCSMetodologiaService();
        $datosIntegrados = null;

        // Cargar datos integrados para todos los miembros del equipo (incluyendo líderes)
        if (!$esLider && $rolEnProyecto) {
            $datosIntegrados = $sgcsService->getDatosColaborador(
                $proyecto,
                $rolEnProyecto->nombre,
                $usuarioId
            );
        } elseif ($esLider) {
            // Para líderes, también cargar sus tareas si las tienen
            $datosIntegrados = $sgcsService->getDatosColaborador(
                $proyecto,
                'líder',
                $usuarioId
            );
        }

        // Para líderes, cargar datos SGCS completos (como antes)
        $elementosConfiguracion = null;
        $solicitudesCambio = null;

    // Sprint activo y story points (para Scrum)
    $sprintActivo = null;
    $storyPointsTotal = 0;
    $storyPointsCompletados = 0;

        // **CARGAR DATOS ADICIONALES PARA METODOLOGÍA CASCADA**
        $fases = null;
        $tareas = null;
        $progresoPorFase = null;
        $faseActual = null;
        $fechaInicioProyecto = null;
        $fechaFinProyecto = null;
        $duracionTotal = null;
        $hitos = null;
        $miembrosEquipo = null;

        // Cargar solicitudes de cambio para TODOS los miembros (incluyendo miembros generales)
        $solicitudesCambio = \App\Models\SolicitudCambio::where('proyecto_id', $proyecto->id)
            ->with(['solicitante', 'items.elementoConfiguracion', 'votos.usuario'])
            ->orderBy('creado_en', 'desc')
            ->get();

        // Si es metodología Scrum, intentar cargar el sprint activo y calcular story points
        if (strtolower($proyecto->metodologia->nombre ?? '') === 'scrum') {
            // Evitar consultas si la tabla no existe (ej. entorno sin migraciones)
            if (Schema::hasTable('sprints')) {
                $sprintActivo = Sprint::sprintActivo($proyecto->id);
                if ($sprintActivo) {
                    $sprintActivo->load('userStories');
                    $storyPointsTotal = $sprintActivo->userStories->sum('story_points');
                    $storyPointsCompletados = $sprintActivo->userStories->where('estado', 'Completado')->sum('story_points');
                }
            } else {
                // Mantener valores por defecto (0 / null) si no existe la tabla
                $sprintActivo = null;
                $storyPointsTotal = 0;
                $storyPointsCompletados = 0;
            }
        }

        // Verificar si el usuario es miembro del CCB
        $ccb = $proyecto->hasOne(\App\Models\ComiteCambio::class, 'proyecto_id')->first();
        $esMiembroCCB = $ccb && $ccb->esMiembro($usuarioId);

        if ($esLider || $miRol === 'líder') {
            $elementosConfiguracion = \App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)
                ->with(['versionActual', 'tareas'])
                ->get();

            // Si es metodología cascada, cargar datos adicionales para la vista unificada
            if (strtolower($proyecto->metodologia->nombre ?? '') === 'cascada') {
                // Obtener fases en orden secuencial
                $fases = \App\Models\FaseMetodologia::where('id_metodologia', $proyecto->metodologia->id_metodologia)
                    ->orderBy('orden')
                    ->get();

                // Obtener tareas con fechas para el cronograma
                $tareas = \App\Models\TareaProyecto::where('id_proyecto', $proyecto->id)
                    ->whereNotNull('fecha_inicio')
                    ->whereNotNull('fecha_fin')
                    ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
                    ->orderBy('fecha_inicio')
                    ->get();

                // Agrupar tareas por fase
                $tareasPorFase = \App\Models\TareaProyecto::where('id_proyecto', $proyecto->id)
                    ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
                    ->get()
                    ->groupBy('id_fase');

                // Calcular progreso por fase
                $progresoPorFase = [];
                foreach ($fases as $fase) {
                    $tareasDelaFase = $tareasPorFase->get($fase->id_fase, collect());
                    $totalTareas = $tareasDelaFase->count();

                    // Contar tareas completadas con case-insensitive matching
                    $tareasCompletadas = $tareasDelaFase->filter(function($tarea) {
                        $estadoLower = strtolower(trim($tarea->estado ?? ''));
                        return in_array($estadoLower, [
                            'done', 'completado', 'completada',
                            'hecho', 'finished', 'finalizado', 'finalizada'
                        ]);
                    })->count();

                    $progresoPorFase[$fase->id_fase] = [
                        'total' => $totalTareas,
                        'completadas' => $tareasCompletadas,
                        'porcentaje' => $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0,
                        'fase_completada' => $totalTareas > 0 && $tareasCompletadas === $totalTareas
                    ];
                }

                // Identificar fase actual (primera incompleta)
                $faseActual = null;
                foreach ($fases as $fase) {
                    if (!$progresoPorFase[$fase->id_fase]['fase_completada']) {
                        $faseActual = $fase;
                        break;
                    }
                }

                // Calcular fechas del proyecto
                $fechaInicioProyecto = $tareas->min('fecha_inicio');
                $fechaFinProyecto = $tareas->max('fecha_fin');
                $duracionTotal = $fechaInicioProyecto && $fechaFinProyecto
                    ? \Carbon\Carbon::parse($fechaInicioProyecto)->diffInDays(\Carbon\Carbon::parse($fechaFinProyecto))
                    : 0;

                // Hitos importantes
                $hitos = $this->identificarHitosCascada($tareas, $fases);

                // Obtener miembros del equipo
                $miembrosEquipo = collect();
                foreach ($proyecto->equipos as $equipo) {
                    $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
                }
                $miembrosEquipo = $miembrosEquipo->unique('id');
            }
        }

        // Decidir qué vista mostrar según el rol
        $vista = 'gestionProyectos.show'; // vista por defecto

        if ($esLider || $miRol === 'líder') {
            $vista = 'gestionProyectos.show-lider';
        } else {
            // Todos los demás roles usan la vista miembro-general (unificada)
            $vista = 'gestionProyectos.miembro-general';
        }

        return view($vista, compact(
            'proyecto',
            'esLider',
            'sprintActivo',
            'storyPointsTotal',
            'storyPointsCompletados',
            'miRol',
            'miEquipo',
            'rolEnProyecto',
            'elementosConfiguracion',
            'solicitudesCambio',
            'esMiembroCCB',
            'datosIntegrados',
            'fases',
            'tareas',
            'progresoPorFase',
            'faseActual',
            'fechaInicioProyecto',
            'fechaFinProyecto',
            'duracionTotal',
            'hitos',
            'miembrosEquipo'
        ));
    }

    /**
     * Mostrar el formulario para crear un nuevo proyecto (Paso 1).
     */
    public function create()
    {
        return view('gestionProyectos.create');
    }

    /**
     * Guardar el proyecto en sesión y mostrar el formulario de plantillas EC (Paso 2).
     */
    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'id_metodologia' => ['required', 'exists:metodologias,id_metodologia'],
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ]);

        // Generar código automáticamente
        $validated['codigo'] = Proyecto::generarCodigo();

        // Guardar datos del proyecto en sesión
        session(['proyecto_temp' => $validated]);

        // Redirigir al paso 2: Seleccionar plantillas EC
        return redirect()->route('proyectos.create-step2');
    }

    /**
     * Mostrar el formulario del paso 2 (seleccionar plantillas EC).
     */
    public function createStep2()
    {
        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Debes completar el Paso 1 primero.');
        }

        // Obtener plantillas según la metodología seleccionada
        $plantillas = \App\Models\PlantillaEC::where('metodologia_id', $proyectoData['id_metodologia'])
            ->orderBy('orden')
            ->get();

        // Calcular fechas para cada plantilla
        foreach ($plantillas as $plantilla) {
            $plantilla->fecha_inicio_calculada = $plantilla->calcularFecha(
                $proyectoData['fecha_inicio'],
                $proyectoData['fecha_fin'],
                $plantilla->porcentaje_inicio
            );
            $plantilla->fecha_fin_calculada = $plantilla->calcularFecha(
                $proyectoData['fecha_inicio'],
                $proyectoData['fecha_fin'],
                $plantilla->porcentaje_fin
            );
        }

        // Obtener nombre de metodología
        $metodologia = \App\Models\Metodologia::find($proyectoData['id_metodologia']);
        $proyectoData['metodologia_nombre'] = $metodologia->nombre;

        return view('gestionProyectos.create-step2', compact('proyectoData', 'plantillas'));
    }

    /**
     * Guardar plantillas EC seleccionadas en sesión y redirigir al paso 3 (equipo).
     */
    public function storeStep2(Request $request)
    {
        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Sesión expirada. Por favor, inicia el proceso nuevamente.');
        }

        // Validar que se haya seleccionado al menos una plantilla (opcional)
        $request->validate([
            'plantillas' => ['nullable', 'array'],
            'plantillas.*' => ['exists:plantillas_ec,id'],
        ]);

        // Guardar plantillas seleccionadas en sesión
        session(['plantillas_seleccionadas' => $request->plantillas ?? []]);

        // Redirigir al paso 3: Configurar equipo
        return redirect()->route('proyectos.create-step3');
    }

    /**
     * Mostrar el formulario del paso 3 (configurar equipo).
     */
    public function createTeams()
    {
        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Debes completar los pasos anteriores primero.');
        }

        // Si la sesión tiene id_metodologia, resolver el nombre legible y añadirlo
        if (isset($proyectoData['id_metodologia'])) {
            $met = \App\Models\Metodologia::find($proyectoData['id_metodologia']);
            $proyectoData['metodologia'] = $met ? $met->nombre : null;
        } else {
            $proyectoData['metodologia'] = $proyectoData['metodologia'] ?? null;
        }

        // Cargar usuarios disponibles
        $usuarios = Usuario::orderBy('nombre_completo')->get(['id', 'nombre_completo', 'correo']);

        // Cargar roles filtrados por metodología
        $roles = Rol::where(function($query) use ($proyectoData) {
            $query->where('metodologia_id', $proyectoData['id_metodologia'])
                  ->orWhereNull('metodologia_id');
        })->orderBy('nombre')->get(['id', 'nombre', 'descripcion', 'metodologia_id']);

        return view('gestionProyectos.create-step3', compact('proyectoData', 'usuarios', 'roles'));
    }

    /**
     * Guardar miembros del equipo y redirigir al paso 4 (revisión final).
     */
    public function storeStep3(Request $request)
    {
        $proyectoData = session('proyecto_temp');

        if (!$proyectoData) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Sesión expirada. Por favor, inicia el proceso nuevamente.');
        }

        // Validar líder y miembros
        $request->validate([
            'lider_id' => ['required', 'exists:usuarios,id'],
            'miembros' => ['nullable', 'array'],
            'miembros.*.usuario_id' => ['required', 'exists:usuarios,id'],
            'miembros.*.rol_id' => ['required', 'exists:roles,id'],
        ]);

        // Guardar líder en sesión
        session(['lider_id' => $request->lider_id]);

        // Guardar miembros en sesión
        session(['miembros_temp' => $request->miembros ?? []]);

        // Redirigir al paso 4: Revisión final
        return redirect()->route('proyectos.create-step4');
    }

    /**
     * Mostrar el formulario del paso 4 (revisión final).
     */
    public function createStep4()
    {
        $proyectoData = session('proyecto_temp');
        $plantillasSeleccionadas = session('plantillas_seleccionadas', []);
        $miembrosData = session('miembros_temp', []);
        $liderId = session('lider_id');

        if (!$proyectoData || !$liderId) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Debes completar los pasos anteriores primero.');
        }

        // Cargar plantillas seleccionadas
        $plantillas = [];
        if (!empty($plantillasSeleccionadas)) {
            $plantillas = \App\Models\PlantillaEC::whereIn('id', $plantillasSeleccionadas)
                ->orderBy('orden')
                ->get();
        }

        // Cargar información del líder
        $lider = Usuario::find($liderId);

        // Cargar información de usuarios y roles de miembros
        $usuariosIds = array_column($miembrosData, 'usuario_id');
        $rolesIds = array_column($miembrosData, 'rol_id');

        $usuarios = Usuario::whereIn('id', $usuariosIds)->get()->keyBy('id');
        $roles = Rol::whereIn('id', $rolesIds)->get()->keyBy('id');

        // Enriquecer miembros con nombres
        foreach ($miembrosData as &$miembro) {
            $miembro['usuario'] = $usuarios[$miembro['usuario_id']] ?? null;
            $miembro['rol'] = $roles[$miembro['rol_id']] ?? null;
        }

        // Obtener metodología
        $metodologia = \App\Models\Metodologia::find($proyectoData['id_metodologia']);
        $proyectoData['metodologia_nombre'] = $metodologia->nombre;

        return view('gestionProyectos.create-step4', compact('proyectoData', 'plantillas', 'miembrosData', 'lider'));
    }

    /**
     * Guardar el proyecto completo con EC, tareas y miembros (transacción).
     */
    public function store(Request $request)
    {
        // Recuperar datos desde la sesión
        $proyectoData = session('proyecto_temp');
        $plantillasSeleccionadas = session('plantillas_seleccionadas', []);
        $miembrosData = session('miembros_temp', []);
        $liderId = session('lider_id');

        if (!$proyectoData || !$liderId) {
            return redirect()->route('proyectos.create')
                ->with('error', 'Sesión expirada. Por favor, inicia el proceso nuevamente.');
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Crear el proyecto
            $proyecto = Proyecto::create([
                'id' => Str::uuid()->toString(),
                'codigo' => $proyectoData['codigo'],
                'nombre' => $proyectoData['nombre'],
                'descripcion' => $proyectoData['descripcion'],
                'id_metodologia' => $proyectoData['id_metodologia'],
                'fecha_inicio' => $proyectoData['fecha_inicio'],
                'fecha_fin' => $proyectoData['fecha_fin'],
                'creado_por' => Auth::id(), // Solo para auditoría
            ]);

            // 2️⃣ Crear 1 equipo automático (Equipo Principal)
            $equipoId = Str::uuid()->toString();
            $equipo = Equipo::create([
                'id' => $equipoId,
                'proyecto_id' => $proyecto->id,
                'nombre' => 'Equipo Principal - ' . $proyecto->nombre,
                'lider_id' => $liderId, // ✅ Líder seleccionado explícitamente
            ]);

            // 3️⃣ Asignar líder al equipo con rol de Líder (buscar rol líder)
            $rolLider = Rol::where('nombre', 'LIKE', '%líder%')->orWhere('nombre', 'LIKE', '%lider%')->first();
            if (!$rolLider) {
                $rolLider = Rol::find(2); // Fallback al rol con id 2 si existe
            }

            // Añadir el líder también como miembro del proyecto (usuarios_roles)
            // para que aparezca en la vista de miembros que utiliza la relación usuarios() (usuarios_roles).
            if ($rolLider) {
                // Insertar en usuarios_roles si no existe
                $existeUsuarioRol = DB::table('usuarios_roles')
                    ->where('proyecto_id', $proyecto->id)
                    ->where('usuario_id', $liderId)
                    ->exists();

                if (!$existeUsuarioRol) {
                    DB::table('usuarios_roles')->insert([
                        'usuario_id' => $liderId,
                        'proyecto_id' => $proyecto->id,
                        'rol_id' => $rolLider->id,
                    ]);
                }

                // Insertar en miembros_equipo (también)
                DB::table('miembros_equipo')->insert([
                    'equipo_id' => $equipoId,
                    'usuario_id' => $liderId,
                    'rol_id' => $rolLider->id,
                ]);
            }

            // 4️⃣ Crear automáticamente un CCB mínimo para el proyecto y asignar al líder como miembro
            // solo si no existe ya un CCB para este proyecto.
            $existingCcb = ComiteCambio::where('proyecto_id', $proyecto->id)->first();
            if (!$existingCcb) {
                $ccb = ComiteCambio::create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'proyecto_id' => $proyecto->id,
                    'nombre' => 'CCB - ' . $proyecto->nombre,
                    'quorum' => 1,
                ]);

                // Attach leader as Presidente/Líder del CCB
                $ccb->miembros()->attach($liderId, [
                    'rol_en_ccb' => 'Líder del Proyecto (Gestor de Configuración)'
                ]);

                // Recalculate quorum
                $ccb->calcularQuorum();
            }

            // 4️⃣ Asignar miembros al equipo
            foreach ($miembrosData as $miembro) {
                // Evitar duplicar al líder si ya está en los miembros
                if ($miembro['usuario_id'] === $liderId) {
                    continue;
                }

                // Insertar en miembros_equipo
                DB::table('miembros_equipo')->insert([
                    'equipo_id' => $equipoId,
                    'usuario_id' => $miembro['usuario_id'],
                    'rol_id' => $miembro['rol_id'],
                ]);

                // También asegurar que el usuario esté registrado en usuarios_roles (miembro del proyecto)
                $existeUsuarioRol = DB::table('usuarios_roles')
                    ->where('proyecto_id', $proyecto->id)
                    ->where('usuario_id', $miembro['usuario_id'])
                    ->exists();

                if (!$existeUsuarioRol) {
                    DB::table('usuarios_roles')->insert([
                        'usuario_id' => $miembro['usuario_id'],
                        'proyecto_id' => $proyecto->id,
                        'rol_id' => $miembro['rol_id'],
                    ]);
                }
            }

            // 5️⃣ Crear EC desde plantillas seleccionadas
            $contadorEC = 0;
            $contadorTareas = 0;
            $contadorRelaciones = 0;
            $mapaPlantillasEC = []; // Mapeo: nombre_plantilla => id_ec_creado

            if (!empty($plantillasSeleccionadas)) {
                $plantillas = \App\Models\PlantillaEC::whereIn('id', $plantillasSeleccionadas)
                    ->orderBy('orden')
                    ->get();

                // Obtener primera fase de la metodología
                $primeraFase = \App\Models\FaseMetodologia::where('id_metodologia', $proyectoData['id_metodologia'])
                    ->orderBy('orden')
                    ->first();

                // PASO 1: Crear todos los EC
                foreach ($plantillas as $plantilla) {
                    $ec = \App\Models\ElementoConfiguracion::create([
                        'id' => Str::uuid()->toString(),
                        'codigo_ec' => $this->generarCodigoEC($proyecto),
                        'titulo' => $plantilla->nombre,
                        'descripcion' => $plantilla->descripcion,
                        'proyecto_id' => $proyecto->id,
                        'tipo' => $plantilla->tipo,
                        'estado' => 'PENDIENTE',
                        'creado_por' => Auth::id(),
                    ]);
                    $contadorEC++;
                    $mapaPlantillasEC[$plantilla->nombre] = $ec->id; // Guardar mapeo

                    // Crear tarea base si existe
                    if ($plantilla->tarea_nombre) {
                        $fechaInicio = $plantilla->calcularFecha(
                            $proyectoData['fecha_inicio'],
                            $proyectoData['fecha_fin'],
                            $plantilla->porcentaje_inicio
                        );
                        $fechaFin = $plantilla->calcularFecha(
                            $proyectoData['fecha_inicio'],
                            $proyectoData['fecha_fin'],
                            $plantilla->porcentaje_fin
                        );

                        \App\Models\TareaProyecto::create([
                            'id_proyecto' => $proyecto->id,
                            'id_ec' => $ec->id,
                            'id_fase' => $primeraFase->id_fase ?? null,
                            'nombre' => $plantilla->tarea_nombre,
                            'descripcion' => $plantilla->tarea_descripcion,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'estado' => 'PENDIENTE',
                            'prioridad' => 3, // Media por defecto
                            'creado_por' => Auth::id(),
                        ]);
                        $contadorTareas++;
                    }
                }

                // PASO 2: Crear relaciones entre EC basadas en las plantillas
                foreach ($plantillas as $plantilla) {
                    if (!empty($plantilla->relaciones) && is_array($plantilla->relaciones)) {
                        $ecOrigen = $mapaPlantillasEC[$plantilla->nombre] ?? null;

                        if ($ecOrigen) {
                            foreach ($plantilla->relaciones as $relacion) {
                                $nombreDestino = $relacion['nombre'] ?? null;
                                $tipoRelacion = $relacion['tipo'] ?? 'REFERENCIA';
                                $ecDestino = $mapaPlantillasEC[$nombreDestino] ?? null;

                                if ($ecDestino && $ecOrigen !== $ecDestino) {
                                    \App\Models\RelacionEC::create([
                                        'id' => Str::uuid()->toString(),
                                        'desde_ec' => $ecOrigen,
                                        'hacia_ec' => $ecDestino,
                                        'tipo_relacion' => $tipoRelacion,
                                        'nota' => "Relación automática desde plantilla: {$plantilla->nombre}",
                                    ]);
                                    $contadorRelaciones++;
                                }
                            }
                        }
                    }
                }
            }

            // Limpiar sesión
            session()->forget(['proyecto_temp', 'plantillas_seleccionadas', 'miembros_temp', 'lider_id']);

            // 🔔 ENVIAR NOTIFICACIONES
            try {
                // 1. Notificar al líder
                $lider = Usuario::find($liderId);
                if ($lider) {
                    $lider->notify(new UsuarioAsignadoComoLider($proyecto));
                }

                // 2. Notificar a los miembros del equipo
                foreach ($miembrosData as $miembro) {
                    if ($miembro['usuario_id'] === $liderId) continue;

                    $usuario = Usuario::find($miembro['usuario_id']);
                    if ($usuario) {
                        $rol = Rol::find($miembro['rol_id']);
                        $usuario->notify(new UsuarioAsignadoAProyecto($proyecto, $rol->nombre ?? 'Miembro'));
                    }
                }

                // 3. Notificar al líder sobre su rol en CCB
                if ($lider) {
                    $lider->notify(new MiembroAgregadoACCB($proyecto));
                }
            } catch (\Exception $e) {
                // No interrumpir el proceso si falla el envío de notificaciones
                Log::warning('Error al enviar notificaciones de proyecto: ' . $e->getMessage());
            }

            DB::commit();

            $mensaje = "¡Proyecto '{$proyecto->nombre}' creado exitosamente!";
            if ($contadorEC > 0) {
                $mensaje .= " Se crearon {$contadorEC} elemento(s) de configuración, {$contadorTareas} tarea(s) base y {$contadorRelaciones} relación(es) de trazabilidad.";
            }

            return redirect()->route('proyectos.show', $proyecto)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('proyectos.create')
                ->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Generar código único para EC
     */
    private function generarCodigoEC($proyecto)
    {
        $año = date('Y');
        // Buscar el último código EC del año en toda la tabla (único globalmente)
        // porque la columna `codigo_ec` tiene un índice UNIQUE a nivel global.
        $ultimoEC = \App\Models\ElementoConfiguracion::where('codigo_ec', 'like', "EC-{$año}-%")
            ->orderBy('codigo_ec', 'desc')
            ->first();

        if (!$ultimoEC) {
            $contador = 1;
        } else {
            preg_match('/EC-\d{4}-(\d{3})/', $ultimoEC->codigo_ec, $matches);
            $contador = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        }

        // Asegurar unicidad en caso extremo de condiciones de carrera: si el código ya
        // existe (por ejemplo otro proceso creó el mismo código entre la consulta y la inserción),
        // incrementar hasta encontrar uno libre.
        do {
            $candidate = sprintf("EC-%s-%03d", $año, $contador);
            $exists = \App\Models\ElementoConfiguracion::where('codigo_ec', $candidate)->exists();
            if ($exists) {
                $contador++;
            }
        } while ($exists);

        return $candidate;
    }

    /**
     * Cancelar el proceso y limpiar la sesión.
     */
    public function cancel()
    {
        session()->forget(['proyecto_temp', 'plantillas_seleccionadas', 'miembros_temp', 'lider_id']);

        return redirect()->route('dashboard')
            ->with('info', 'Proceso de creación de proyecto cancelado.');
    }

    /**
     * Vista de trazabilidad jerárquica del proyecto
     */
    public function trazabilidad(Proyecto $proyecto)
    {
        // Cargar todos los EC con sus relaciones y versiones
        $elementos = \App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->with([
                'relacionesDesde.elementoHacia',
                'relacionesHacia.elementoDesde',
                'versiones.commit',
                'versiones.creador',
                'versiones' => function($query) {
                    $query->orderBy('creado_en', 'desc');
                }
            ])
            ->orderBy('tipo')
            ->orderBy('codigo_ec')
            ->get();

        // Construir matriz de trazabilidad
        $matriz = [];
        foreach ($elementos as $desde) {
            $matriz[$desde->id] = [];
            foreach ($elementos as $hacia) {
                $matriz[$desde->id][$hacia->id] = null;
            }
        }

        // Llenar matriz con relaciones
        foreach ($elementos as $elemento) {
            foreach ($elemento->relacionesDesde as $relacion) {
                $matriz[$elemento->id][$relacion->hacia_ec] = $relacion;
            }
        }

        // Preparar datos para JavaScript (evita problemas de sintaxis en Blade)
        $elementosJS = $elementos->map(function($e) {
            return [
                'id' => $e->id,
                'codigo' => $e->codigo_ec,
                'titulo' => $e->titulo,
                'tipo' => $e->tipo,
                'estado' => $e->estado,
                'versiones' => $e->versiones->map(function($v) {
                    return [
                        'version' => $v->version,
                        'estado' => $v->estado,
                        'commit_id' => $v->commit_id,
                        'creado_en' => $v->creado_en->format('d/m/Y H:i'),
                    ];
                })
            ];
        });

        return view('gestionProyectos.trazabilidad', compact('proyecto', 'elementos', 'matriz', 'elementosJS'));
    }

    /**
     * Organizar elementos en niveles jerárquicos
     */
    private function organizarEnNiveles($elementos, $elementosRaiz)
    {
        $niveles = [];
        $visitados = [];
        $nivel = 0;

        // Nivel 0: elementos raíz
        $niveles[0] = $elementosRaiz->pluck('id')->toArray();
        foreach ($niveles[0] as $id) {
            $visitados[$id] = true;
        }

        // Calcular niveles siguientes
        $nivelActual = $elementosRaiz->pluck('id')->toArray();

        while (!empty($nivelActual)) {
            $nivel++;
            $siguienteNivel = [];

            foreach ($nivelActual as $elementoId) {
                $elemento = $elementos->firstWhere('id', $elementoId);
                if (!$elemento) continue;

                // Encontrar todos los EC que dependen de este
                foreach ($elemento->relacionesDesde as $relacion) {
                    $hijoId = $relacion->hacia_ec;
                    if (!isset($visitados[$hijoId])) {
                        $siguienteNivel[] = $hijoId;
                        $visitados[$hijoId] = true;
                    }
                }
            }

            if (!empty($siguienteNivel)) {
                $niveles[$nivel] = array_unique($siguienteNivel);
            }

            $nivelActual = $siguienteNivel;
        }

        return $niveles;
    }

    /**
     * Mostrar gestión de equipos del proyecto
     */
    public function gestionarEquipos(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $equipos = $proyecto->equipos()->with(['lider', 'miembros'])->get();

        return view('gestionProyectos.equipos.index', compact('proyecto', 'equipos'));
    }

    /**
     * Mostrar formulario para crear equipo
     */
    public function crearEquipo(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $usuarios = Usuario::all();

        // Filtrar roles por metodología del proyecto
        $roles = Rol::where(function($query) use ($proyecto) {
            $query->where('metodologia_id', $proyecto->id_metodologia)
                  ->orWhereNull('metodologia_id');
        })->orderBy('nombre')->get();

        return view('gestionProyectos.equipos.create', compact('proyecto', 'usuarios', 'roles'));
    }

    /**
     * Guardar nuevo equipo
     */
    public function guardarEquipo(Request $request, Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'lider_id' => 'required|exists:usuarios,id',
            'miembros' => 'nullable|array',
            'miembros.*.usuario_id' => 'required|exists:usuarios,id',
            'miembros.*.rol_id' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $equipo = Equipo::create([
                'id' => Str::uuid()->toString(),
                'proyecto_id' => $proyecto->id,
                'nombre' => $validated['nombre'],
                'lider_id' => $validated['lider_id'],
            ]);

            // Agregar miembros si se proporcionaron
            if (!empty($validated['miembros'])) {
                foreach ($validated['miembros'] as $miembro) {
                    // Insertar en miembros_equipo
                    DB::table('miembros_equipo')->insert([
                        'equipo_id' => $equipo->id,
                        'usuario_id' => $miembro['usuario_id'],
                        'rol_id' => $miembro['rol_id'],
                    ]);

                    // Asegurar que el usuario también esté en usuarios_roles (miembro del proyecto)
                    $existeUsuarioRol = DB::table('usuarios_roles')
                        ->where('proyecto_id', $proyecto->id)
                        ->where('usuario_id', $miembro['usuario_id'])
                        ->exists();

                    if (!$existeUsuarioRol) {
                        DB::table('usuarios_roles')->insert([
                            'usuario_id' => $miembro['usuario_id'],
                            'proyecto_id' => $proyecto->id,
                            'rol_id' => $miembro['rol_id'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('proyectos.equipos.index', $proyecto)
                ->with('success', 'Equipo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear equipo: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para editar equipo
     */
    public function editarEquipo(Proyecto $proyecto, Equipo $equipo)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($equipo->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $usuarios = Usuario::all();

        // Filtrar roles por metodología del proyecto
        $roles = Rol::where(function($query) use ($proyecto) {
            $query->where('metodologia_id', $proyecto->id_metodologia)
                  ->orWhereNull('metodologia_id');
        })->orderBy('nombre')->get();

        return view('gestionProyectos.equipos.edit', compact('proyecto', 'equipo', 'usuarios', 'roles'));
    }

    /**
     * Actualizar equipo
     */
    public function actualizarEquipo(Request $request, Proyecto $proyecto, Equipo $equipo)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($equipo->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'lider_id' => 'required|exists:usuarios,id',
        ]);

        $equipo->update($validated);

        return redirect()->route('proyectos.equipos.index', $proyecto)
            ->with('success', 'Equipo actualizado exitosamente');
    }

    /**
     * Eliminar equipo
     */
    public function eliminarEquipo(Proyecto $proyecto, Equipo $equipo)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($equipo->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // Verificar que no sea el último equipo
        if ($proyecto->equipos()->count() <= 1) {
            return back()->withErrors(['error' => 'El proyecto debe tener al menos un equipo']);
        }

        $equipo->delete();

        return redirect()->route('proyectos.equipos.index', $proyecto)
            ->with('success', 'Equipo eliminado exitosamente');
    }

    /**
     * Agregar miembro a equipo
     */
    public function agregarMiembroEquipo(Request $request, Proyecto $proyecto, Equipo $equipo)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($equipo->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'rol_id' => 'required|exists:roles,id',
        ]);

        // Verificar que no esté ya en el equipo
        $existe = DB::table('miembros_equipo')
            ->where('equipo_id', $equipo->id)
            ->where('usuario_id', $validated['usuario_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Este usuario ya es miembro del equipo']);
        }

        DB::table('miembros_equipo')->insert([
            'equipo_id' => $equipo->id,
            'usuario_id' => $validated['usuario_id'],
            'rol_id' => $validated['rol_id'],
        ]);

        return back()->with('success', 'Miembro agregado al equipo exitosamente');
    }

    /**
     * Remover miembro de equipo
     */
    public function removerMiembroEquipo(Proyecto $proyecto, Equipo $equipo, $usuarioId)
    {
        $this->verificarAccesoProyecto($proyecto);

        if ($equipo->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        DB::table('miembros_equipo')
            ->where('equipo_id', $equipo->id)
            ->where('usuario_id', $usuarioId)
            ->delete();

        return back()->with('success', 'Miembro removido del equipo exitosamente');
    }

    /**
     * Verificar acceso al proyecto
     */
    private function verificarAccesoProyecto(Proyecto $proyecto)
    {
        $usuarioId = Auth::user()->id;
        $esLider = $proyecto->equipos()->where('lider_id', $usuarioId)->exists();
        $esMiembro = $proyecto->equipos()->whereHas('miembros', function($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId);
        })->exists();

        if (!$esLider && !$esMiembro) {
            abort(403, 'No tienes acceso a este proyecto');
        }
    }

    /**
     * Mostrar gestión de miembros del proyecto
     */
    public function gestionarMiembrosProyecto(Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        // Obtener todos los usuarios que tienen acceso al proyecto
        $miembrosProyecto = $proyecto->usuarios()->withPivot('rol_id')->get();

        // Obtener usuarios disponibles para agregar
        $usuariosDisponibles = Usuario::whereNotIn('id', $miembrosProyecto->pluck('id'))->get();

        // Obtener equipos del proyecto
        $equipos = $proyecto->equipos;

        // Filtrar roles por metodología del proyecto
        $roles = Rol::where(function($query) use ($proyecto) {
            $query->where('metodologia_id', $proyecto->id_metodologia)
                  ->orWhereNull('metodologia_id');
        })->orderBy('nombre')->get();

        return view('gestionProyectos.miembros.index', compact('proyecto', 'miembrosProyecto', 'usuariosDisponibles', 'roles', 'equipos'));
    }

    /**
     * Agregar miembro al proyecto
     */
    public function agregarMiembroProyecto(Request $request, Proyecto $proyecto)
    {
        $this->verificarAccesoProyecto($proyecto);

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'rol_id' => 'required|exists:roles,id',
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        // Verificar que no esté ya en el proyecto
        $existe = DB::table('usuarios_roles')
            ->where('proyecto_id', $proyecto->id)
            ->where('usuario_id', $validated['usuario_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Este usuario ya es miembro del proyecto']);
        }

        // Insertar en usuarios_roles (rol en el proyecto)
        DB::table('usuarios_roles')->insert([
            'usuario_id' => $validated['usuario_id'],
            'proyecto_id' => $proyecto->id,
            'rol_id' => $validated['rol_id'],
        ]);

        // Insertar en miembros_equipo (agregarlo al equipo)
        DB::table('miembros_equipo')->insert([
            'equipo_id' => $validated['equipo_id'],
            'usuario_id' => $validated['usuario_id'],
            'rol_id' => $validated['rol_id'],
        ]);

        return redirect()->route('proyectos.miembros.index', $proyecto)->with('success', 'Miembro agregado al proyecto y equipo exitosamente');
    }

    /**
     * Actualizar rol de miembro del proyecto
     */
    public function actualizarRolMiembroProyecto(Request $request, Proyecto $proyecto, $usuarioId)
    {
        $this->verificarAccesoProyecto($proyecto);

        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
        ]);

        DB::table('usuarios_roles')
            ->where('proyecto_id', $proyecto->id)
            ->where('usuario_id', $usuarioId)
            ->update(['rol_id' => $validated['rol_id']]);

        return back()->with('success', 'Rol del miembro actualizado exitosamente');
    }

    /**
     * Remover miembro del proyecto
     */
    public function removerMiembroProyecto(Proyecto $proyecto, $usuarioId)
    {
        $this->verificarAccesoProyecto($proyecto);

        // No permitir que se quede sin miembros
        if ($proyecto->usuarios()->count() <= 1) {
            return back()->withErrors(['error' => 'El proyecto debe tener al menos un miembro']);
        }

        // Verificar que no sea el líder del equipo
        $esLider = $proyecto->equipos()->where('lider_id', $usuarioId)->exists();
        if ($esLider) {
            return back()->withErrors(['error' => 'No se puede remover al líder del equipo. Primero asigna otro líder.']);
        }

        DB::table('usuarios_roles')
            ->where('proyecto_id', $proyecto->id)
            ->where('usuario_id', $usuarioId)
            ->delete();

        return back()->with('success', 'Miembro removido del proyecto exitosamente');
    }

    /**
     * Identificar hitos importantes del proyecto cascada
     */
    private function identificarHitosCascada($tareas, $fases)
    {
        $hitos = [];

        foreach ($fases as $fase) {
            $tareasDelaFase = $tareas->where('id_fase', $fase->id_fase);

            if ($tareasDelaFase->count() > 0) {
                $fechaInicioFase = $tareasDelaFase->min('fecha_inicio');
                $fechaFinFase = $tareasDelaFase->max('fecha_fin');
                $totalTareas = $tareasDelaFase->count();
                $tareasCompletadas = $tareasDelaFase->where('estado', 'Completado')->count();

                // Hito de inicio de fase
                if ($fechaInicioFase) {
                    $hitos[] = [
                        'tipo' => 'inicio_fase',
                        'titulo' => "Inicio de {$fase->nombre_fase}",
                        'fecha' => $fechaInicioFase,
                        'descripcion' => "Comenzar trabajos de {$fase->nombre_fase}",
                        'completado' => $tareasCompletadas > 0,
                        'fase' => $fase->nombre_fase
                    ];
                }

                // Hito de fin de fase
                if ($fechaFinFase) {
                    $hitos[] = [
                        'tipo' => 'fin_fase',
                        'titulo' => "Finalización de {$fase->nombre_fase}",
                        'fecha' => $fechaFinFase,
                        'descripcion' => "Completar todas las actividades de {$fase->nombre_fase}",
                        'completado' => $tareasCompletadas === $totalTareas,
                        'fase' => $fase->nombre_fase
                    ];
                }
            }
        }

        // Ordenar hitos por fecha
        usort($hitos, function($a, $b) {
            return strcmp($a['fecha'], $b['fecha']);
        });

        return $hitos;
    }
}
