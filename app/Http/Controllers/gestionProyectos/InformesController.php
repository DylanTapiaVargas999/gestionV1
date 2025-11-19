<?php

namespace App\Http\Controllers\GestionProyectos;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\ElementoConfiguracion;
use App\Services\Cronograma\DetectorDesviaciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InformesController extends Controller
{
    protected $detectorDesviaciones;

    public function __construct(DetectorDesviaciones $detectorDesviaciones)
    {
        $this->detectorDesviaciones = $detectorDesviaciones;
    }

    /**
     * Dashboard principal de informes
     */
    public function dashboard(Proyecto $proyecto)
    {
        // INFORME 01: Estado General del Proyecto
        $informeGeneral = $this->obtenerInformeGeneral($proyecto);

        // INFORME 02: Estado de Tareas/Requerimientos
        $informeTareas = $this->obtenerInformeTareas($proyecto);

        // INFORME 03: Carga de Trabajo del Equipo
        $informeEquipo = $this->obtenerInformeEquipo($proyecto);

        return view('informes.dashboard', compact(
            'proyecto',
            'informeGeneral',
            'informeTareas',
            'informeEquipo'
        ));
    }

    /**
     * Informe 01: Estado General del Proyecto
     */
    private function obtenerInformeGeneral(Proyecto $proyecto)
    {
        $tareas = $proyecto->tareas()->with(['fase', 'responsableUsuario'])->get();
        $elementosConfig = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->get();

        // Métricas generales - ✅ CORREGIDO: filtrado case-insensitive para estados
        $totalTareas = $tareas->count();
        $tareasCompletadas = $tareas->filter(function($tarea) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
        })->count();
        $avanceGeneral = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100, 1) : 0;

        // Estado de ECs por tipo - ✅ CORREGIDO: usar tipos reales de la BD
        $ecsPorTipo = [
            'codigo_fuente' => $elementosConfig->where('tipo', 'CODIGO')->count(),
            'documentacion' => $elementosConfig->where('tipo', 'DOCUMENTO')->count(),
            'scripts_bd' => $elementosConfig->where('tipo', 'SCRIPT_BD')->count(),
            'casos_prueba' => $elementosConfig->where('tipo', 'CASO_PRUEBA')->count(),
            'configuracion' => $elementosConfig->where('tipo', 'CONFIGURACION')->count(),
        ];

        // Estado de ECs por estado - ✅ CORREGIDO: estados en mayúsculas como en la BD
        $ecsPorEstado = [
            'aprobados' => $elementosConfig->where('estado', 'APROBADO')->count(),
            'liberados' => $elementosConfig->where('estado', 'LIBERADO')->count(),
            'en_revision' => $elementosConfig->where('estado', 'EN_REVISION')->count(),
            'obsoletos' => $elementosConfig->where('estado', 'OBSOLETO')->count(),
        ];

        $totalECs = $elementosConfig->count();
        $ecsPorEstadoPorcentaje = $totalECs > 0 ? [
            'aprobados' => round(($ecsPorEstado['aprobados'] / $totalECs) * 100, 1),
            'liberados' => round(($ecsPorEstado['liberados'] / $totalECs) * 100, 1),
            'en_revision' => round(($ecsPorEstado['en_revision'] / $totalECs) * 100, 1),
            'obsoletos' => round(($ecsPorEstado['obsoletos'] / $totalECs) * 100, 1),
        ] : ['aprobados' => 0, 'liberados' => 0, 'en_revision' => 0, 'obsoletos' => 0];

        // Cumplimiento de hitos - ✅ CORREGIDO: usar fecha_fin como hito, es_hito no existe
        $hoy = Carbon::now();
        $hitosCompletados = $tareas->filter(function($tarea) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            return $tarea->fecha_fin_estimada && in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
        })->count();
        $hitosTotales = $tareas->where('fecha_fin_estimada', '!=', null)->count();
        $cumplimientoHitos = $hitosTotales > 0 ? round(($hitosCompletados / $hitosTotales) * 100, 1) : 0;

        // Riesgos identificados
        $desviaciones = $this->detectorDesviaciones->detectarDesviaciones($proyecto);
        $riesgosAltos = $desviaciones->where('severidad', 'critica')->count() +
                        $desviaciones->where('severidad', 'alta')->count();

        return [
            'proyecto' => [
                'codigo' => $proyecto->codigo,
                'nombre' => $proyecto->nombre,
                'metodologia' => $proyecto->metodologia->nombre ?? 'N/A',
                'jefe_proyecto' => $proyecto->creador->name ?? 'N/A', // ✅ CORREGIDO: usar 'name' no 'nombre'
                'fecha_inicio' => $proyecto->fecha_inicio,
                'fecha_fin' => $proyecto->fecha_fin,
                'avance_general' => $avanceGeneral,
            ],
            'tareas' => [
                'total' => $totalTareas,
                'completadas' => $tareasCompletadas,
                'en_progreso' => $tareas->filter(function($tarea) {
                    $estado = strtolower(trim($tarea->estado ?? ''));
                    return in_array($estado, ['en_progreso', 'en progreso', 'in progress', 'working']);
                })->count(),
                'pendientes' => $tareas->filter(function($tarea) {
                    $estado = strtolower(trim($tarea->estado ?? ''));
                    return in_array($estado, ['pendiente', 'to do', 'todo', 'backlog']);
                })->count(),
            ],
            'elementos_configuracion' => [
                'total' => $totalECs,
                'por_tipo' => $ecsPorTipo,
                'por_estado' => $ecsPorEstado,
                'por_estado_porcentaje' => $ecsPorEstadoPorcentaje,
                'nivel_madurez' => $totalECs > 0 ?
                    round((($ecsPorEstado['aprobados'] + $ecsPorEstado['liberados']) / $totalECs) * 100, 1) : 0,
            ],
            'hitos' => [
                'completados' => $hitosCompletados,
                'totales' => $hitosTotales,
                'cumplimiento' => $cumplimientoHitos,
            ],
            'riesgos' => [
                'total' => $desviaciones->count(),
                'criticos' => $desviaciones->where('severidad', 'critica')->count(),
                'altos' => $desviaciones->where('severidad', 'alta')->count(),
                'medios' => $desviaciones->where('severidad', 'media')->count(),
            ],
        ];
    }

    /**
     * Informe 02: Estado de Tareas/Requerimientos
     */
    private function obtenerInformeTareas(Proyecto $proyecto)
    {
        $tareas = $proyecto->tareas()->with(['fase', 'responsableUsuario'])->get();
        $hoy = Carbon::now();

        // Clasificar tareas por prioridad y estado
        $tareasPorPrioridad = [
            'alta' => $tareas->where('prioridad', '>=', 8),
            'media' => $tareas->whereBetween('prioridad', [5, 7]),
            'baja' => $tareas->where('prioridad', '<', 5),
        ];

        // Alertas de tareas - ✅ CORREGIDO: usar método dinámico para completadas y días como entero
        $tareasProximasVencer = $tareas->filter(function ($tarea) use ($hoy) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            $estaCompletada = in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
            if (!$tarea->fecha_fin || $estaCompletada) return false;
            // diffInDays puede retornar float en Carbon v3; redondeamos para evitar decimales en UI
            $diff = $hoy->diffInDays(Carbon::parse($tarea->fecha_fin), false);
            $diasRestantes = (int) round($diff);
            return $diasRestantes >= 0 && $diasRestantes <= 7;
        });

        $tareasBloqueadas = $tareas->filter(function($tarea) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            return in_array($estado, ['bloqueada', 'blocked', 'bloqueado']);
        });

        $tareasCompletadasRecientes = $tareas->filter(function ($tarea) use ($hoy) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            $estaCompletada = in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
            if (!$estaCompletada || !$tarea->updated_at) return false;
            return $hoy->diffInDays(Carbon::parse($tarea->updated_at)) <= 10;
        });

        // Métricas de implementación - ✅ CORREGIDO: filtrado case-insensitive
        $totalTareas = $tareas->count();
        $completadas = $tareas->filter(function($tarea) {
            $estado = strtolower(trim($tarea->estado ?? ''));
            return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
        })->count();
        $tasaCompletitud = $totalTareas > 0 ? round(($completadas / $totalTareas) * 100, 1) : 0;

        return [
            'tareas_detalle' => $tareas->map(function ($tarea) use ($hoy) {
                $diasRestantes = null;
                $estado = strtolower(trim($tarea->estado ?? ''));
                $estaCompletada = in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                
                if ($tarea->fecha_fin && !$estaCompletada) {
                    // Garantizar valor entero (Carbon v3 retorna float)
                    $diff = $hoy->diffInDays(Carbon::parse($tarea->fecha_fin), false);
                    $diasRestantes = (int) round($diff);
                }

                return [
                    'id' => $tarea->id_tarea,
                    'nombre' => $tarea->nombre,
                    'fase' => $tarea->fase->nombre_fase ?? 'Sin fase',
                    'prioridad' => $tarea->prioridad,
                    'prioridad_texto' => $tarea->prioridad >= 8 ? 'Alta' : ($tarea->prioridad >= 5 ? 'Media' : 'Baja'),
                    'estado' => $tarea->estado,
                    'responsable' => $tarea->responsableUsuario->name ?? 'Sin asignar', // ✅ CORREGIDO: usar 'name'
                    'fecha_vencimiento' => $tarea->fecha_fin,
                    'dias_restantes' => $diasRestantes,
                ];
            })->sortBy('dias_restantes')->values(),
            'alertas' => [
                'proximas_vencer' => $tareasProximasVencer->count(),
                'bloqueadas' => $tareasBloqueadas->count(),
                'completadas_recientes' => $tareasCompletadasRecientes->count(),
            ],
            'metricas' => [
                'total' => $totalTareas,
                'completadas' => $completadas,
                'tasa_completitud' => $tasaCompletitud,
                'por_prioridad' => [
                    'alta' => [
                        'total' => $tareasPorPrioridad['alta']->count(),
                        'completadas' => $tareasPorPrioridad['alta']->filter(function($t) {
                            $estado = strtolower(trim($t->estado ?? ''));
                            return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                        })->count(),
                    ],
                    'media' => [
                        'total' => $tareasPorPrioridad['media']->count(),
                        'completadas' => $tareasPorPrioridad['media']->filter(function($t) {
                            $estado = strtolower(trim($t->estado ?? ''));
                            return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                        })->count(),
                    ],
                    'baja' => [
                        'total' => $tareasPorPrioridad['baja']->count(),
                        'completadas' => $tareasPorPrioridad['baja']->filter(function($t) {
                            $estado = strtolower(trim($t->estado ?? ''));
                            return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                        })->count(),
                    ],
                ],
            ],
        ];
    }

    /**
     * Informe 03: Carga de Trabajo del Equipo
     */
    private function obtenerInformeEquipo(Proyecto $proyecto)
    {
        $equipos = $proyecto->equipos()->with('miembros')->get();
        $miembrosData = [];

        foreach ($equipos as $equipo) {
            foreach ($equipo->miembros as $miembro) {
                if (!$miembro) continue;

                // Tareas asignadas - ✅ CORREGIDO: filtrado case-insensitive
                $tareasAsignadas = TareaProyecto::where('id_proyecto', $proyecto->id)
                    ->where('responsable', $miembro->id)
                    ->get();

                $tareasActivas = $tareasAsignadas->filter(function($t) {
                    $estado = strtolower(trim($t->estado ?? ''));
                    return !in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                });
                
                $tareasCompletadas = $tareasAsignadas->filter(function($t) {
                    $estado = strtolower(trim($t->estado ?? ''));
                    return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
                });

                // Horas asignadas - calcular de forma inteligente
                $horasAsignadas = 0;
                foreach ($tareasActivas as $tarea) {
                    if ($tarea->horas_estimadas && $tarea->horas_estimadas > 0) {
                        // Usar horas_estimadas si existe
                        $horasAsignadas += $tarea->horas_estimadas;
                    } elseif ($tarea->fecha_inicio && $tarea->fecha_fin) {
                        // Calcular por duración: días laborables * 8 horas
                        $dias = \Carbon\Carbon::parse($tarea->fecha_inicio)
                            ->diffInDays(\Carbon\Carbon::parse($tarea->fecha_fin)) + 1;
                        $horasAsignadas += $dias * 8;
                    } else {
                        // Fallback: asumir 8 horas por tarea
                        $horasAsignadas += 8;
                    }
                }

                $horasDisponibles = 40; // Semana laboral estándar
                $utilizacion = $horasDisponibles > 0
                    ? min(100, round(($horasAsignadas / $horasDisponibles) * 100, 1))
                    : 0;

                // Determinar nivel de carga
                $nivelCarga = 'normal';
                if ($utilizacion > 100) $nivelCarga = 'sobrecarga';
                elseif ($utilizacion < 50) $nivelCarga = 'subutilizado';

                $miembrosData[] = [
                    'id' => $miembro->id,
                    'nombre' => $miembro->name, // ✅ CORREGIDO: usar 'name' no 'nombre'
                    'email' => $miembro->email,
                    'equipo' => $equipo->nombre,
                    'tareas_totales' => $tareasAsignadas->count(),
                    'tareas_activas' => $tareasActivas->count(),
                    'tareas_completadas' => $tareasCompletadas->count(),
                    'horas_asignadas' => round($horasAsignadas, 1),
                    'horas_disponibles' => $horasDisponibles,
                    'utilizacion' => $utilizacion,
                    'nivel_carga' => $nivelCarga,
                ];
            }
        }

        // Alertas de recursos
        $sobrecargados = collect($miembrosData)->where('nivel_carga', 'sobrecarga')->count();
        $subutilizados = collect($miembrosData)->where('nivel_carga', 'subutilizado')->count();

        return [
            'miembros' => collect($miembrosData)->sortByDesc('utilizacion')->values(),
            'resumen' => [
                'total_miembros' => count($miembrosData),
                'utilizacion_promedio' => count($miembrosData) > 0 ?
                    round(collect($miembrosData)->avg('utilizacion'), 1) : 0,
                'sobrecargados' => $sobrecargados,
                'subutilizados' => $subutilizados,
                'normales' => count($miembrosData) - $sobrecargados - $subutilizados,
            ],
        ];
    }
}
