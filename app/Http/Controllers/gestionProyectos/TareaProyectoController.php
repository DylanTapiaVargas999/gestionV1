<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use App\Models\CommitRepositorio;
use App\Models\VersionEC;
use App\Models\Usuario;
use App\Services\CommitGitHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TareaProyectoController extends Controller
{
    /**
     * Verifica que el usuario sea el creador del proyecto
     */
    private function verificarCreador(Proyecto $proyecto)
    {
        $usuarioId = Auth::user()->id;

        // Permitir al LÍDER de algún equipo del proyecto
        $esLider = $proyecto->equipos()->where('lider_id', $usuarioId)->exists();
        if ($esLider) {
            return;
        }

        // Como respaldo, permitir a miembros del proyecto (si está configurado así)
        $esMiembro = $proyecto->equipos()
            ->whereHas('miembros', function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId);
            })->exists();

        if ($esMiembro) {
            return;
        }

        abort(403, 'Solo el líder o miembros del proyecto pueden gestionar el cronograma.');
    }

    /**
     * Muestra el tablero Kanban del proyecto
     */
    public function index(Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        // Obtener metodología y sus fases
        $metodologia = $proyecto->metodologia;
        $fases = FaseMetodologia::where('id_metodologia', $metodologia->id_metodologia)
            ->orderBy('orden')
            ->get();

        // Obtener todas las tareas con sus relaciones
        $tareas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('creado_en', 'desc')
            ->get()
            ->groupBy('id_fase');

        // Obtener elementos de configuración disponibles
        $elementosConfiguracion = $proyecto->elementosConfiguracion()
            ->orderBy('codigo_ec')
            ->get();

        // Obtener miembros del equipo para asignar como responsables
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.tareas.index', compact(
            'proyecto',
            'metodologia',
            'fases',
            'tareas',
            'elementosConfiguracion',
            'miembrosEquipo'
        ));
    }

    /**
     * Almacena una nueva tarea
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $this->verificarCreador($proyecto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
            'id_ec' => 'nullable|exists:elementos_configuracion,id',
            'responsable' => 'nullable|exists:usuarios,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string|max:50',
            'story_points' => 'nullable|integer|min:0',
            'horas_estimadas' => 'nullable|numeric|min:0',
            'prioridad' => 'nullable|integer',
            'sprint' => 'nullable|string|max:50',
            'criterios_aceptacion' => 'nullable|array',
            'notas' => 'nullable|string',
        ]);

        $validated['id_proyecto'] = $proyecto->id;

        // ✅ CORREGIDO: Si no se especifica estado, usar "Pendiente" como estado inicial
        // NO usar el nombre de la fase como estado (eso causaba bugs)
        if (empty($validated['estado'])) {
            $validated['estado'] = 'Pendiente'; // Estado válido del sistema
        }

        $validated['creado_por'] = Auth::user()->id;

        $tarea = TareaProyecto::create($validated);

        // Redirigir al dashboard correcto según la metodología del proyecto
        $met = strtolower($proyecto->metodologia->nombre ?? '');
        if ($met === 'cascada') {
            $routeUrl = route('cascada.dashboard', $proyecto);
            $mensaje = 'Tarea creada exitosamente.';
        } else {
            $routeUrl = route('scrum.dashboard', $proyecto);
            $mensaje = 'User Story creada exitosamente y asignada al Sprint Board.';
        }

        return redirect($routeUrl)->with('success', $mensaje);
    }

    /**
     * Muestra el formulario de edición de una tarea
     */
    public function edit(Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404);
        }

        // Obtener fases de la metodología del proyecto
        $fases = FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
            ->orderBy('orden')
            ->get();

        // Obtener elementos de configuración del proyecto
        $elementosConfiguracion = $proyecto->elementosConfiguracion()
            ->orderBy('codigo_ec')
            ->get();

        // Obtener miembros del equipo
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        return view('gestionProyectos.tareas.edit', compact(
            'proyecto',
            'tarea',
            'fases',
            'elementosConfiguracion',
            'miembrosEquipo'
        ));
    }

    /**
     * Actualiza una tarea existente
     */
    public function update(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_fase' => 'required|exists:fases_metodologia,id_fase',
            'id_ec' => 'nullable|exists:elementos_configuracion,id',
            'responsable' => 'nullable|exists:usuarios,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string|max:50',
            'story_points' => 'nullable|integer|min:0',
            'horas_estimadas' => 'nullable|numeric|min:0',
            'prioridad' => 'nullable|integer',
            'sprint' => 'nullable|string|max:50',
            'criterios_aceptacion' => 'nullable|array',
            'notas' => 'nullable|string',
            'commit_url' => 'nullable|url|max:500',
        ]);

        // Detectar cambio de estado a completado/revisión
        $estadoAnterior = $tarea->estado;
        $estadoNuevo = $validated['estado'] ?? $tarea->estado;

        DB::beginTransaction();
        try {
            // Si la tarea se marca como completada y tiene commit_url
            if ($this->esEstadoCompletado($estadoNuevo) && !$this->esEstadoCompletado($estadoAnterior)) {
                // Validar que tenga commit_url
                if (empty($validated['commit_url'])) {
                    return back()->withErrors(['commit_url' => 'Debes proporcionar la URL del commit para completar la tarea.'])->withInput();
                }

                // Procesar el commit y crear/actualizar EC
                $resultado = $this->procesarCompletarTarea($tarea, $validated['commit_url'], $proyecto);

                if (!$resultado['success']) {
                    DB::rollBack();
                    return back()->withErrors(['commit_url' => $resultado['message']])->withInput();
                }

                // Guardar el commit_id generado
                $validated['commit_id'] = $resultado['commit_id'];
            }

            // Actualizar la tarea
            $tarea->update($validated);

            // Si la tarea pasa a revisión Y tiene un EC asociado, cambiar estado del EC
            if ($this->esEstadoRevision($estadoNuevo) && !$this->esEstadoRevision($estadoAnterior)) {
                if ($tarea->id_ec) {
                    $ec = ElementoConfiguracion::find($tarea->id_ec);
                    if ($ec && $ec->estado !== 'EN_REVISION') {
                        $ec->estado = 'EN_REVISION';
                        $ec->save();
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('proyectos.tareas.index', $proyecto)
                ->with('success', 'Tarea actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la tarea: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Cambia la fase de una tarea (usado para drag & drop)
     */
    public function cambiarFase(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        // Verificar que la tarea pertenece al proyecto
        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json(['error' => 'Tarea no pertenece a este proyecto'], 404);
        }

        // Verificar que el usuario sea el creador del proyecto O el responsable de la tarea
        $usuarioAutorizado = ($proyecto->creado_por === Auth::user()->id) ||
                           ($tarea->responsable === Auth::user()->id);

        if (!$usuarioAutorizado) {
            return response()->json(['error' => 'No tienes permisos para modificar esta tarea'], 403);
        }

        $validated = $request->validate([
            'estado' => 'nullable|string',
            'id_fase' => 'nullable|exists:fases_metodologia,id_fase',
            'commit_url' => 'nullable|url|max:500',
        ]);

        $estadoAnterior = $tarea->estado;
        $faseAnterior = $tarea->id_fase;

        // Determinar el nuevo estado
        if (isset($validated['estado'])) {
            // Mapear estados del frontend a estados de la BD
            // Mantenemos los estados tal como llegan del frontend para evitar inconsistencias
            $estadosBD = [
                'Por Hacer' => 'Pendiente',
                'Pendiente' => 'Pendiente',
                'To Do' => 'Pendiente',
                'TODO' => 'Pendiente',
                'En Progreso' => 'En Progreso',
                'EN_PROGRESO' => 'En Progreso',
                'In Progress' => 'En Progreso',
                'En Revisión' => 'En Revisión',
                'En Revision' => 'En Revisión',
                'EN_REVISION' => 'En Revisión',
                'In Review' => 'En Revisión',
                'Review' => 'En Revisión',
                'Finalizado' => 'Completada',
                'Completado' => 'Completada',
                'Completada' => 'Completada',
                'Done' => 'Completada',
                'DONE' => 'Completada',
                'COMPLETADA' => 'Completada',
            ];

            $estadoFrontend = $validated['estado'];
            $estadoNuevo = $estadosBD[$estadoFrontend] ?? $estadoFrontend;
        } else {
            // Obtener el nombre de la nueva fase como estado
            $nuevaFase = FaseMetodologia::find($validated['id_fase'] ?? $tarea->id_fase);
            $estadoNuevo = $nuevaFase ? $nuevaFase->nombre_fase : $tarea->estado;
        }

        // VALIDACIÓN: Si se está marcando como completada, DEBE tener commit_url
        if ($this->esEstadoCompletado($estadoNuevo) && !$this->esEstadoCompletado($estadoAnterior)) {
            if (empty($validated['commit_url']) && empty($tarea->commit_url)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Se requiere la URL del commit para completar esta tarea',
                    'requiere_commit' => true,
                    'tarea_id' => $tarea->id_tarea,
                ], 422);
            }

            // Procesar el commit si se proporcionó
            if (!empty($validated['commit_url'])) {
                DB::beginTransaction();
                try {
                    $resultado = $this->procesarCompletarTarea($tarea, $validated['commit_url'], $proyecto);

                    if (!$resultado['success']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => $resultado['message'],
                        ], 422);
                    }

                    $tarea->commit_url = $validated['commit_url'];
                    $tarea->commit_id = $resultado['commit_id'];

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => 'Error al procesar el commit: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        // Actualizar fase si se proporcionó
        if (isset($validated['id_fase'])) {
            $tarea->id_fase = $validated['id_fase'];
        }

        // Actualizar estado
        $tarea->estado = $estadoNuevo;

        try {
            $tarea->save();

            if (isset($resultado)) {
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada exitosamente',
                'tarea' => $tarea->load(['fase', 'elementoConfiguracion', 'responsableUsuario', 'commit'])
            ]);
        } catch (\Exception $e) {
            if (isset($resultado)) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'error' => 'Error al guardar la tarea: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina una tarea
     */
    public function destroy(Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            abort(404);
        }

        $tarea->delete();

        return redirect()
            ->route('proyectos.tareas.index', $proyecto)
            ->with('success', 'Tarea eliminada exitosamente.');
    }

    /**
     * Obtiene los datos de una tarea específica (para modal)
     */
    public function show(Proyecto $proyecto, TareaProyecto $tarea)
    {
        $this->verificarCreador($proyecto);

        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }

        $tarea->load(['fase', 'elementoConfiguracion', 'responsableUsuario']);

        return response()->json($tarea);
    }

    /**
     * Verifica si un estado corresponde a "revisión"
     */
    private function esEstadoRevision($estado): bool
    {
        $estadosRevision = ['EN_REVISION', 'In Review', 'Review', 'REVISION'];
        return in_array($estado, $estadosRevision);
    }

    /**
     * Verifica si un estado corresponde a "completado"
     */
    /**
     * Verifica si un estado corresponde a "completado"
     * Considera las diferentes fases según la metodología
     */
    private function esEstadoCompletado($estado): bool
    {
        // Estados genéricos de completado (case-insensitive)
        $estadosCompletados = [
            'COMPLETADA', 'Completada', 'completada',
            'COMPLETADO', 'Completado', 'completado',
            'DONE', 'Done', 'done',
            'FINALIZADO', 'Finalizado', 'finalizado',
            'Despliegue', 'DESPLIEGUE', 'despliegue',
            'Mantenimiento', 'MANTENIMIENTO', 'mantenimiento'
        ];

        return in_array($estado, $estadosCompletados);
    }

    /**
     * Procesa el commit cuando una tarea se completa
     * Crea o actualiza el EC asociado y registra el commit
     *
     * @param TareaProyecto $tarea
     * @param string $commitUrl
     * @param Proyecto $proyecto
     * @return array ['success' => bool, 'message' => string, 'commit_id' => string|null]
     */
    private function procesarCompletarTarea(TareaProyecto $tarea, string $commitUrl, Proyecto $proyecto): array
    {
        $commitService = new CommitGitHubService();

        // Validar URL del commit
        if (!$commitService->esUrlCommitValida($commitUrl)) {
            return [
                'success' => false,
                'message' => 'La URL del commit no es válida. Formato esperado: github.com/user/repo/commit/hash',
                'commit_id' => null,
            ];
        }

        // Extraer información del commit
        $infoCommit = $commitService->extraerInfoCommit($commitUrl);

        // Crear o actualizar Elemento de Configuración
        if ($tarea->id_ec) {
            // Ya existe un EC asociado, solo actualizarlo
            $ec = ElementoConfiguracion::find($tarea->id_ec);
        } else {
            // Crear nuevo EC basado en la tarea
            $countElementos = $proyecto->elementosConfiguracion()->count();
            $codigoEc = $proyecto->codigo . '-EC-' . str_pad($countElementos + 1, 3, '0', STR_PAD_LEFT);

            $ec = new ElementoConfiguracion();
            $ec->id = (string) Str::uuid();
            $ec->proyecto_id = $proyecto->id;
            $ec->codigo_ec = $codigoEc;
            $ec->titulo = $tarea->nombre;
            $ec->descripcion = $tarea->descripcion ?? "Elemento generado desde tarea: {$tarea->nombre}";
            $ec->tipo = 'CODIGO'; // Por defecto, puede ajustarse según necesidad
            $ec->creado_por = $tarea->responsable ?? Auth::user()->id;
        }

        // Cambiar estado a EN_REVISION (esperando aprobación del CCB)
        $ec->estado = 'EN_REVISION';
        $ec->save();

        // Actualizar la tarea con el EC creado
        if (!$tarea->id_ec) {
            $tarea->id_ec = $ec->id;
        }

        // Registrar el commit en la BD vinculado al EC
        $commit = new CommitRepositorio();
        $commit->id = (string) Str::uuid();
        $commit->url_repositorio = $infoCommit['url_repositorio'];
        $commit->hash_commit = $infoCommit['hash'];
        $commit->ec_id = $ec->id;

        // Intentar obtener metadata desde GitHub (opcional, se cachea)
        $datosCommit = $commitService->obtenerDatosCommit($commitUrl);
        if ($datosCommit) {
            $commit->autor = $datosCommit['autor'];
            $commit->mensaje = $datosCommit['mensaje'];
            $commit->fecha_commit = $datosCommit['fecha_commit'];
        }

        $commit->save();

        // CREAR VERSIÓN AUTOMÁTICAMENTE (como en Scrum)
        // Obtener versión actual o crear la primera
        $versionActual = $ec->versionActual;
        $versionParts = explode('.', $versionActual?->version ?? '0.0.0');

        if ($versionParts[0] === '0') {
            // Primera versión
            $nuevaVersion = '1.0.0';
        } else {
            // Incrementar versión minor
            $versionParts[1] = (int)$versionParts[1] + 1;
            $versionParts[2] = 0;
            $nuevaVersion = implode('.', $versionParts);
        }

        // Crear nueva versión del EC
        $version = new VersionEC();
        $version->id = (string) Str::uuid();
        $version->ec_id = $ec->id;
        $version->version = $nuevaVersion;
        $version->registro_cambios = "Tarea completada: {$tarea->nombre}\n\nCommit: {$commitUrl}";
        $version->creado_por = Auth::id();
        $version->aprobado_por = Auth::id();
        $version->aprobado_en = now();
        $version->save();

        // Actualizar EC con nueva versión y estado APROBADO
        $ec->version_actual_id = $version->id;
        $ec->estado = 'APROBADO';
        $ec->save();

        return [
            'success' => true,
            'message' => "Tarea completada exitosamente. EC '{$ec->codigo_ec}' actualizado a versión {$nuevaVersion}.",
            'commit_id' => $commit->id,
            'ec_id' => $ec->id,
            'version' => $nuevaVersion,
        ];
    }

    /**
     * Actualiza el estado de una tarea (para Cascada Kanban)
     */
    public function actualizarEstado(Request $request, Proyecto $proyecto, TareaProyecto $tarea)
    {
        // Verificar que la tarea pertenece al proyecto
        if ($tarea->id_proyecto !== $proyecto->id) {
            return response()->json(['error' => 'Tarea no pertenece a este proyecto'], 404);
        }

        // Verificar permisos (creador del proyecto o responsable de la tarea)
        $usuarioAutorizado = ($proyecto->creado_por === Auth::user()->id) ||
                           ($tarea->responsable === Auth::user()->id);

        if (!$usuarioAutorizado) {
            return response()->json(['error' => 'No tienes permisos para modificar esta tarea'], 403);
        }

        $validated = $request->validate([
            'estado' => 'required|string|max:50',
        ]);

        $tarea->estado = $validated['estado'];
        $tarea->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'tarea' => $tarea,
        ]);
    }
}
