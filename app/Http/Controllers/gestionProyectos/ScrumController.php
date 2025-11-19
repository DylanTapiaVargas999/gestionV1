<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use App\Models\Usuario;
use App\Models\Sprint;
use App\Models\DailyScrum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScrumController extends Controller
{
    /**
     * Verifica que el proyecto use metodología Scrum
     */
    private function verificarMetodologiaScrum(Proyecto $proyecto)
    {
        if (strtolower($proyecto->metodologia->nombre) !== 'scrum') {
            abort(403, 'Este controlador solo funciona con proyectos Scrum.');
        }
    }

    /**
     * Verifica permisos del usuario en el proyecto
     */
    private function verificarPermisos(Proyecto $proyecto)
    {
        $usuario = Auth::user();

        // El creador siempre tiene permisos
        if ($proyecto->creado_por === $usuario->id) {
            return;
        }

        // Verificar si es miembro del equipo
        $esMiembro = $proyecto->equipos()
            ->whereHas('miembros', function($query) use ($usuario) {
                $query->where('usuario_id', $usuario->id);
            })->exists();

        if (!$esMiembro) {
            abort(403, 'No tienes permisos para acceder a este proyecto Scrum.');
        }
    }

    /**
     * Dashboard principal de Scrum - Vista del tablero con sprints
     */
    public function dashboard(Request $request, Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Obtener fases de Scrum
        $metodologia = $proyecto->metodologia;
        $fases = FaseMetodologia::where('id_metodologia', $metodologia->id_metodologia)
            ->orderBy('orden')
            ->get();

        // Obtener sprints del proyecto
        $sprints = $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->get();

        // Determinar qué sprint mostrar
        $sprintNombreSolicitado = $request->query('sprint');

        if ($sprintNombreSolicitado) {
            // Buscar el sprint solicitado por nombre
            $sprintActivo = $sprints->firstWhere('nombre', $sprintNombreSolicitado);

            // Si no existe, usar el sprint activo por defecto
            if (!$sprintActivo) {
                $sprintActivo = $proyecto->sprintActivo ?? $sprints->first();
            }
        } else {
            // No se especificó sprint, usar el activo
            $sprintActivo = $proyecto->sprintActivo ?? $sprints->first();
        }

        // Para compatibilidad con vistas: mantener $sprintActual como string
        $sprintActual = $sprintActivo ? $sprintActivo->nombre : 'Sprint 1';

        // Obtener TODAS las tareas del proyecto
        $todasTareas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->get();

        // Filtrar tareas del sprint activo por id_sprint
        $tareas = $sprintActivo
            ? $todasTareas->where('id_sprint', $sprintActivo->id_sprint)
            : collect();

        $tareasPorFase = $tareas->groupBy('id_fase');

        // Agrupar TODAS las tareas por sprint usando id_sprint directamente
        $tareasPorSprint = $todasTareas->groupBy(function($tarea) use ($sprints) {
            if (!$tarea->id_sprint) {
                return 'Sin Sprint';
            }

            // Buscar el sprint por ID
            $sprint = $sprints->firstWhere('id_sprint', $tarea->id_sprint);
            return $sprint ? $sprint->nombre : "Sprint ID {$tarea->id_sprint}";
        });

        // Métricas del sprint actual
        if ($sprintActivo) {
            $tareasSprintActual = $tareas->where('id_sprint', $sprintActivo->id_sprint);
            $totalStoryPoints = $tareasSprintActual->sum('story_points');

            // Contar story points completados con case-insensitive matching
            $storyPointsCompletados = $tareasSprintActual->filter(function($tarea) {
                $estadoLower = strtolower(trim($tarea->estado));
                return in_array($estadoLower, [
                    'done', 'completado', 'completada',
                    'hecho', 'finished', 'finalizado', 'finalizada'
                ]);
            })->sum('story_points');

            // Actualizar velocidad estimada del sprint si cambió
            if ($sprintActivo->velocidad_estimada != $totalStoryPoints) {
                $sprintActivo->update(['velocidad_estimada' => $totalStoryPoints]);
            }
        } else {
            $tareasSprintActual = collect();
            $totalStoryPoints = 0;
            $storyPointsCompletados = 0;
        }

        // Burndown chart data
        $burndownData = $sprintActivo ? $this->calcularBurndown($sprintActivo, $tareasSprintActual) : [];

        // Obtener miembros del equipo
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.scrum.dashboard', compact(
            'proyecto',
            'metodologia',
            'fases',
            'tareas',
            'tareasPorSprint',  // ← Para compatibilidad con vistas
            'tareasPorFase',
            'sprints',
            'sprintActual',     // ← String del nombre
            'sprintActivo',     // ← Objeto Sprint completo
            'totalStoryPoints',
            'storyPointsCompletados',
            'burndownData',
            'miembrosEquipo'
        ));
    }

    /**
     * Vista de planificación de sprint
     */
    public function sprintPlanning(Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        $metodologia = $proyecto->metodologia;

        // Product Backlog (tareas sin sprint asignado)
        $productBacklog = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->whereNull('id_sprint')
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->get();

        // Sprints existentes del proyecto
        $sprints = $proyecto->sprints()->orderBy('fecha_inicio', 'asc')->get();

        // Miembros del equipo para asignar responsables
        $miembrosEquipo = $proyecto->equipos()
            ->with('miembros')
            ->get()
            ->flatMap(function ($equipo) {
                return $equipo->miembros;
            })
            ->unique('id');

        // Elementos de Configuración del proyecto
        $elementosConfiguracion = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->orderBy('titulo')
            ->get();

        return view('gestionProyectos.scrum.sprint-planning', compact(
            'proyecto',
            'metodologia',
            'productBacklog',
            'sprints',
            'miembrosEquipo',
            'elementosConfiguracion'
        ));
    }

    /**
     * Vista de Daily Scrum
     */
    public function dailyScrum(Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Obtener sprint (por nombre desde request o sprint activo)
        $nombreSprint = request('sprint');
        $sprintActivo = null;

        if ($nombreSprint) {
            $sprintActivo = $proyecto->sprints()->where('nombre', $nombreSprint)->first();
        }

        if (!$sprintActivo) {
            $sprintActivo = $proyecto->sprintActivo ?? $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->first();
        }

        $sprintActual = $sprintActivo ? $sprintActivo->nombre : 'Sprint 1';

        // Tareas del sprint actual agrupadas por miembro
        $tareasDelSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->when($sprintActivo, function($query) use ($sprintActivo) {
                return $query->where('id_sprint', $sprintActivo->id_sprint);
            })
            ->with(['responsableUsuario', 'fase'])
            ->get()
            ->groupBy('responsable');

        // Miembros del equipo
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.scrum.daily-scrum', compact(
            'proyecto',
            'sprintActual',
            'tareasDelSprint',
            'miembrosEquipo'
        ));
    }

    /**
     * Vista de Sprint Review
     */
    public function sprintReview(Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Obtener sprint (por nombre desde request o sprint activo)
        $nombreSprint = request('sprint');
        $sprintActivo = null;

        if ($nombreSprint) {
            $sprintActivo = $proyecto->sprints()->where('nombre', $nombreSprint)->first();
        }

        if (!$sprintActivo) {
            $sprintActivo = $proyecto->sprintActivo ?? $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->first();
        }

        $sprintActual = $sprintActivo ? $sprintActivo->nombre : 'Sprint 1';

        $tareasDelSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->when($sprintActivo, function($query) use ($sprintActivo) {
                return $query->where('id_sprint', $sprintActivo->id_sprint);
            })
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->get();

        // Métricas del sprint
        $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
        $totalTareas = $tareasDelSprint->count();
        $tareasCompletadas = $tareasDelSprint->whereIn('estado', $estadosCompletados)->count();
        $totalStoryPoints = $tareasDelSprint->sum('story_points') ?? 0;
        $storyPointsCompletados = $tareasDelSprint->whereIn('estado', $estadosCompletados)->sum('story_points') ?? 0;

        return view('gestionProyectos.scrum.sprint-review', compact(
            'proyecto',
            'sprintActual',
            'sprintActivo',
            'tareasDelSprint',
            'totalTareas',
            'tareasCompletadas',
            'totalStoryPoints',
            'storyPointsCompletados'
        ));
    }

    /**
     * Vista de Sprint Retrospective
     */
    public function sprintRetrospective(Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Obtener sprint (por nombre desde request o sprint activo)
        $nombreSprint = request('sprint');
        $sprintActivo = null;

        if ($nombreSprint) {
            $sprintActivo = $proyecto->sprints()->where('nombre', $nombreSprint)->first();
        }

        if (!$sprintActivo) {
            $sprintActivo = $proyecto->sprintActivo ?? $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->first();
        }

        $sprintActual = $sprintActivo ? $sprintActivo->nombre : 'Sprint 1';

        return view('gestionProyectos.scrum.sprint-retrospective', compact(
            'proyecto',
            'sprintActual',
            'sprintActivo'
        ));
    }

    /**
     * Obtener el sprint actual del proyecto
     */
    private function obtenerSprintActual(Proyecto $proyecto)
    {
        $ultimoSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->whereNotNull('sprint')
            ->orderBy('sprint', 'desc')
            ->value('sprint');

        return $ultimoSprint ?? 'Sprint 1';
    }

    /**
     * Calcular datos para burndown chart
     */
    private function calcularBurndown($sprint, $tareasSprintActual)
    {
        if (!$sprint || !$sprint->fecha_inicio || !$sprint->fecha_fin) {
            return [];
        }

        $totalStoryPoints = $tareasSprintActual->sum('story_points');
        $fechaInicio = $sprint->fecha_inicio;
        $fechaFin = $sprint->fecha_fin;
        $dias = $fechaInicio->diffInDays($fechaFin);

        // Datos para el burndown chart
        $burndownData = [];
        for ($dia = 0; $dia <= $dias; $dia++) {
            $progreso = $dias > 0 ? ($dia / $dias) * 0.8 : 0; // 80% de avance ideal
            $storyPointsRestantes = max(0, $totalStoryPoints * (1 - $progreso));
            $burndownData[] = [
                'dia' => $dia,
                'ideal' => max(0, $totalStoryPoints - ($totalStoryPoints / max($dias, 1) * $dia)),
                'actual' => $storyPointsRestantes
            ];
        }

        return $burndownData;
    }

    /**
     * Crear un nuevo sprint
     */
    public function storeSprint(Request $request, Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'objetivo' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'velocidad_estimada' => 'nullable|integer|min:0',
        ]);

        $sprint = Sprint::create([
            'id_proyecto' => $proyecto->id,
            'nombre' => $validated['nombre'],
            'objetivo' => $validated['objetivo'] ?? "Objetivos del {$validated['nombre']}",
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'velocidad_estimada' => $validated['velocidad_estimada'] ?? 0,
            'estado' => 'planificado',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sprint creado exitosamente',
            'sprint' => $sprint->load(['userStories']),
        ]);
    }

    /**
     * Iniciar un sprint
     */
    public function iniciarSprint(Request $request, Proyecto $proyecto, Sprint $sprint)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        if ($sprint->id_proyecto !== $proyecto->id) {
            abort(404, 'Sprint no encontrado en este proyecto');
        }

        if ($sprint->estado !== 'planificado') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden iniciar sprints en estado planificado',
            ], 400);
        }

        // Verificar que no haya otro sprint activo
        $sprintActivo = Sprint::where('id_proyecto', $proyecto->id)
            ->where('estado', 'activo')
            ->first();

        if ($sprintActivo) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un sprint activo. Completa el sprint actual antes de iniciar uno nuevo.',
            ], 400);
        }

        $sprint->update(['estado' => 'activo']);

        return response()->json([
            'success' => true,
            'message' => 'Sprint iniciado exitosamente',
            'sprint' => $sprint,
        ]);
    }

    /**
     * Completar un sprint
     */
    public function completarSprint(Request $request, Proyecto $proyecto, Sprint $sprint)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        if ($sprint->id_proyecto !== $proyecto->id) {
            abort(404, 'Sprint no encontrado en este proyecto');
        }

        if ($sprint->estado !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden completar sprints activos',
            ], 400);
        }

        // Calcular velocidad real
        $tareasCompletadas = $sprint->userStories()->where('estado', 'Completado')->get();
        $velocidadReal = $tareasCompletadas->sum('story_points');

        $sprint->update([
            'estado' => 'completado',
            'velocidad_real' => $velocidadReal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sprint completado exitosamente',
            'sprint' => $sprint,
            'velocidad_real' => $velocidadReal,
        ]);
    }

    /**
     * Asignar user stories a un sprint
     */
    public function asignarUserStories(Request $request, Proyecto $proyecto, Sprint $sprint)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Verificar que el sprint pertenece al proyecto
        if ($sprint->id_proyecto !== $proyecto->id) {
            return response()->json([
                'success' => false,
                'message' => 'El sprint no pertenece a este proyecto'
            ], 400);
        }

        $validated = $request->validate([
            'user_stories' => 'required|array',
            'user_stories.*' => 'exists:tareas_proyecto,id_tarea'
        ]);

        $userStoriesIds = $validated['user_stories'];

        // Actualizar las user stories para asignarlas al sprint
        $updatedCount = TareaProyecto::whereIn('id_tarea', $userStoriesIds)
            ->where('id_proyecto', $proyecto->id)
            ->whereNull('id_sprint') // Solo las que no están asignadas a otro sprint
            ->update(['id_sprint' => $sprint->id_sprint]);

        return response()->json([
            'success' => true,
            'message' => "Se asignaron {$updatedCount} user stories al sprint",
            'sprint' => $sprint,
            'assigned_count' => $updatedCount
        ]);
    }

    /**
     * Obtener user stories de un sprint
     */
    public function obtenerUserStoriesSprint(Proyecto $proyecto, Sprint $sprint)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Verificar que el sprint pertenece al proyecto
        if ($sprint->id_proyecto !== $proyecto->id) {
            return response()->json([
                'success' => false,
                'message' => 'El sprint no pertenece a este proyecto'
            ], 400);
        }

        $userStories = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('id_sprint', $sprint->id_sprint)
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'user_stories' => $userStories,
            'sprint' => $sprint
        ]);
    }

    /**
     * Remover user story de un sprint
     */
    public function removerUserStorySprint(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        // Verificar que la tarea pertenece al proyecto
        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json([
                'success' => false,
                'message' => 'La user story no pertenece a este proyecto'
            ], 400);
        }

        // Remover del sprint (asignar null)
        $tarea->update(['id_sprint' => null]);

        return response()->json([
            'success' => true,
            'message' => 'User story removida del sprint',
            'user_story' => $tarea
        ]);
    }



    /**
     * Crear una nueva user story
     */
    public function storeUserStory(Request $request, Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_sprint' => 'nullable|exists:sprints,id_sprint',
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
            'id_ec' => 'nullable|exists:elementos_configuracion,id',
            'story_points' => 'nullable|integer|min:1|max:100',
            'prioridad' => 'nullable|integer|min:1|max:10',
            'responsable' => 'nullable|exists:usuarios,id',
            'criterios_aceptacion' => 'nullable|string',
        ]);

        $userStory = TareaProyecto::create([
            'id_proyecto' => $proyecto->id,
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'id_sprint' => $validated['id_sprint'] ?? null,
            'id_fase' => $validated['id_fase'],
            'id_ec' => $validated['id_ec'] ?? null,
            'story_points' => $validated['story_points'] ?? null,
            'prioridad' => $validated['prioridad'] ?? 5,
            'responsable' => $validated['responsable'] ?? null,
            'estado' => 'To Do',
            'creado_por' => Auth::id(),
        ]);

        // Actualizar velocidad estimada del sprint si fue asignada
        if ($userStory->id_sprint && $userStory->story_points) {
            $sprint = Sprint::find($userStory->id_sprint);
            if ($sprint) {
                $totalStoryPoints = TareaProyecto::where('id_sprint', $sprint->id_sprint)->sum('story_points');
                $sprint->update(['velocidad_estimada' => $totalStoryPoints]);
            }
        }

        // Si es petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User Story creada exitosamente',
                'userStory' => $userStory->load(['fase', 'responsableUsuario', 'sprint']),
            ]);
        }

        // Si es petición normal, redirigir a sprint planning si venimos de ahí
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'sprint-planning')) {
            return redirect()->route('scrum.sprint-planning', $proyecto)
                ->with('success', 'User Story creada y agregada al Product Backlog');
        }

        // Por defecto redirigir al dashboard
        return redirect()->route('scrum.dashboard', $proyecto)
            ->with('success', '✅ User Story creada exitosamente');
    }

    /**
     * Actualizar una user story
     */
    public function updateUserStory(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404, 'User Story no encontrada en este proyecto');
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'id_sprint' => 'nullable|exists:sprints,id_sprint',
            'id_fase' => 'sometimes|exists:fases_metodologia,id_fase',
            'story_points' => 'nullable|integer|min:1|max:100',
            'prioridad' => 'nullable|integer|min:1|max:5',
            'responsable' => 'nullable|exists:usuarios,id',
            'estado' => 'nullable|in:To Do,In Progress,In Review,Done,Completado',
            'criterios_aceptacion' => 'nullable|array',
        ]);

        $tarea->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User Story actualizada exitosamente',
            'userStory' => $tarea->load(['fase', 'responsableUsuario', 'sprint']),
        ]);
    }

    /**
     * Guardar registro de Daily Scrum
     */
    public function storeDailyScrum(Request $request, Proyecto $proyecto)
    {
        $this->verificarMetodologiaScrum($proyecto);
        $this->verificarPermisos($proyecto);

        $validated = $request->validate([
            'id_sprint' => 'required|exists:sprints,id_sprint',
            'id_miembro' => 'required|exists:usuarios,id',
            'que_hice_ayer' => 'required|string',
            'que_hare_hoy' => 'required|string',
            'impedimentos' => 'nullable|string',
        ]);

        $dailyScrum = DailyScrum::create([
            'id_sprint' => $validated['id_sprint'],
            'id_miembro' => $validated['id_miembro'],
            'fecha' => now()->format('Y-m-d'),
            'que_hice_ayer' => $validated['que_hice_ayer'],
            'que_hare_hoy' => $validated['que_hare_hoy'],
            'impedimentos' => $validated['impedimentos'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Daily Scrum registrado exitosamente',
            'dailyScrum' => $dailyScrum,
        ]);
    }

}
