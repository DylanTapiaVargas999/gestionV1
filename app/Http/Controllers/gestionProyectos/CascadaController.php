<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CascadaController extends Controller
{
    /**
     * Verifica que el proyecto use metodología Cascada
     */
    private function verificarMetodologiaCascada(Proyecto $proyecto)
    {
        if (strtolower($proyecto->metodologia->nombre) !== 'cascada') {
            abort(403, 'Este controlador solo funciona con proyectos Cascada.');
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
            abort(403, 'No tienes permisos para acceder a este proyecto Cascada.');
        }
    }

    /**
     * Dashboard principal de Cascada - Vista unificada con pestañas
     */
    public function dashboard(Proyecto $proyecto, Request $request)
    {
        $this->verificarMetodologiaCascada($proyecto);
        $this->verificarPermisos($proyecto);

        // Obtener vista activa desde parámetro o default
        $vistaActiva = $request->get('vista', 'dashboard');

        // Obtener fases en orden secuencial
        $metodologia = $proyecto->metodologia;
        $fases = FaseMetodologia::where('id_metodologia', $metodologia->id_metodologia)
            ->orderBy('orden')
            ->get();

        // Obtener tareas con fechas para el cronograma
        $tareas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->whereNotNull('fecha_inicio')
            ->whereNotNull('fecha_fin')
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('fecha_inicio')
            ->get();

        // Agrupar tareas por fase
        $tareasPorFase = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->with(['fase', 'elementoConfiguracion', 'responsableUsuario'])
            ->get()
            ->groupBy('id_fase');

        // Calcular progreso por fase
        $progresoPorFase = [];
        foreach ($fases as $fase) {
            $tareasDelaFase = $tareasPorFase->get($fase->id_fase, collect());
            $totalTareas = $tareasDelaFase->count();

            // Contar tareas completadas usando case-insensitive matching
            $tareasCompletadas = $tareasDelaFase->filter(function($tarea) {
                $estadoLower = strtolower(trim($tarea->estado));
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
            ? Carbon::parse($fechaInicioProyecto)->diffInDays(Carbon::parse($fechaFinProyecto))
            : 0;

        // Hitos importantes
        $hitos = $this->identificarHitos($tareas, $fases);

        // Obtener miembros del equipo
        $miembrosEquipo = collect();
        foreach ($proyecto->equipos as $equipo) {
            $miembrosEquipo = $miembrosEquipo->merge($equipo->miembros);
        }
        $miembrosEquipo = $miembrosEquipo->unique('id');

        // Obtener elementos de configuración del proyecto
        $elementosConfiguracion = $proyecto->elementosConfiguracion()
            ->orderBy('codigo_ec')
            ->get();

        return view('gestionProyectos.cascada.dashboard', compact(
            'proyecto',
            'metodologia',
            'fases',
            'tareas',
            'tareasPorFase',
            'progresoPorFase',
            'faseActual',
            'fechaInicioProyecto',
            'fechaFinProyecto',
            'duracionTotal',
            'hitos',
            'miembrosEquipo',
            'elementosConfiguracion',
            'vistaActiva'
        ));
    }

    /**
     * Vista detallada de una fase específica
     */
    public function verFase(Proyecto $proyecto, FaseMetodologia $fase)
    {
        $this->verificarMetodologiaCascada($proyecto);
        $this->verificarPermisos($proyecto);

        // Verificar que la fase pertenece a la metodología del proyecto
        if ($fase->id_metodologia !== $proyecto->metodologia->id_metodologia) {
            abort(404, 'Fase no encontrada en este proyecto.');
        }

        // Obtener tareas de la fase
        $tareasDelaFase = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('id_fase', $fase->id_fase)
            ->with(['elementoConfiguracion', 'responsableUsuario'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_inicio')
            ->get();

        // Calcular métricas de la fase
        $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
        $totalTareas = $tareasDelaFase->count();
        $tareasCompletadas = $tareasDelaFase->whereIn('estado', $estadosCompletados)->count();
        $horasEstimadas = $tareasDelaFase->sum('horas_estimadas');
        $progreso = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

        // Contar tareas en cada estado (case-insensitive)
        $tareasEnProgreso = $tareasDelaFase->filter(function($tarea) {
            return in_array(strtolower(trim($tarea->estado)), ['en progreso', 'en_progreso', 'in progress']);
        })->count();

        $tareasPendientes = $tareasDelaFase->filter(function($tarea) {
            return in_array(strtolower(trim($tarea->estado)), ['pendiente', 'to do', 'todo', 'por hacer']);
        })->count();

        $tareasEnRevision = $tareasDelaFase->filter(function($tarea) {
            return in_array(strtolower(trim($tarea->estado)), ['en revisión', 'en revision', 'in review', 'review']);
        })->count();

        // Obtener fase anterior y siguiente
        $faseAnterior = FaseMetodologia::where('id_metodologia', $proyecto->metodologia->id_metodologia)
            ->where('orden', '<', $fase->orden)
            ->orderBy('orden', 'desc')
            ->first();

        $faseSiguiente = FaseMetodologia::where('id_metodologia', $proyecto->metodologia->id_metodologia)
            ->where('orden', '>', $fase->orden)
            ->orderBy('orden')
            ->first();

        // Entregables de la fase
        $entregables = $tareasDelaFase->where('id_ec', '!=', null);

        $porcentajeCompletado = $progreso;
        $tareasFase = $tareasDelaFase; // Alias para la vista

        return view('gestionProyectos.cascada.fase-detalle', compact(
            'proyecto',
            'fase',
            'tareasDelaFase',
            'tareasFase',
            'totalTareas',
            'tareasCompletadas',
            'tareasPendientes',
            'tareasEnProgreso',
            'tareasEnRevision',
            'horasEstimadas',
            'progreso',
            'faseAnterior',
            'faseSiguiente',
            'entregables',
            'estadosCompletados',
            'porcentajeCompletado'
        ));
    }

    /**
     * Vista de cronograma maestro - Redirige al dashboard unificado
     */
    public function cronogramaMaestro(Proyecto $proyecto)
    {
        $this->verificarMetodologiaCascada($proyecto);
        $this->verificarPermisos($proyecto);

        return redirect()->route('cascada.dashboard', [$proyecto, 'vista' => 'cronograma']);
    }

    /**
     * Vista de hitos del proyecto - Redirige al dashboard unificado
     */
    public function hitos(Proyecto $proyecto)
    {
        $this->verificarMetodologiaCascada($proyecto);
        $this->verificarPermisos($proyecto);

        return redirect()->route('cascada.dashboard', [$proyecto, 'vista' => 'dashboard']);
    }

    /**
     * Redirección para diagrama gantt - Redirige al dashboard unificado
     */
    public function redirigirGantt(Proyecto $proyecto)
    {
        $this->verificarMetodologiaCascada($proyecto);
        $this->verificarPermisos($proyecto);

        return redirect()->route('cascada.dashboard', [$proyecto, 'vista' => 'gantt']);
    }

    /**
     * Identificar hitos importantes del proyecto
     */
    private function identificarHitos($tareas, $fases)
    {
        $hitos = [];

        foreach ($fases as $fase) {
            $tareasDelaFase = $tareas->where('id_fase', $fase->id_fase);

            if ($tareasDelaFase->count() > 0) {
                $fechaInicioFase = $tareasDelaFase->min('fecha_inicio');
                $fechaFinFase = $tareasDelaFase->max('fecha_fin');
                $totalTareas = $tareasDelaFase->count();
                $estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE', 'COMPLETADA', 'done', 'completado', 'completada'];
                $tareasCompletadas = $tareasDelaFase->whereIn('estado', $estadosCompletados)->count();

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

    /**
     * Generar datos para el gráfico Gantt
     */
    private function generarDatosGantt($tareas, $fechaInicio, $rangoFechas)
    {
        $datosGantt = [];
        $fechaInicioCarbon = Carbon::parse($fechaInicio);

        foreach ($tareas as $tarea) {
            $inicioTarea = Carbon::parse($tarea->fecha_inicio);
            $finTarea = Carbon::parse($tarea->fecha_fin);

            $diasDesdeInicio = $fechaInicioCarbon->diffInDays($inicioTarea);
            $duracionTarea = $inicioTarea->diffInDays($finTarea) + 1;

            $porcentajeInicio = ($diasDesdeInicio / $rangoFechas) * 100;
            $porcentajeDuracion = ($duracionTarea / $rangoFechas) * 100;

            $datosGantt[] = [
                'tarea' => $tarea,
                'porcentaje_inicio' => $porcentajeInicio,
                'porcentaje_duracion' => $porcentajeDuracion,
                'dias_desde_inicio' => $diasDesdeInicio,
                'duracion_dias' => $duracionTarea
            ];
        }

        return $datosGantt;
    }
}
