<?php

namespace App\Services\Cronograma;

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Optimizador de recursos del proyecto
 * Detecta sobrecarga y redistribuye carga de trabajo
 */
class OptimizadorRecursos
{
    /**
     * Detectar recursos sobrecargados
     */
    public function detectarSobrecarga(Proyecto $proyecto): array
    {
        $sobrecarga = [];

        $equipos = $proyecto->equipos()->with('miembros')->get();

        foreach ($equipos as $equipo) {
            foreach ($equipo->miembros as $miembro) {
                // $miembro ya ES un Usuario (relación BelongsToMany directa)
                if (!$miembro) continue;

                $horasAsignadas = $this->calcularHorasAsignadas($miembro->id, $proyecto);
                $horasDisponibles = 40; // Valor por defecto

                if ($horasAsignadas > $horasDisponibles) {
                    $sobrecarga[] = [
                        'miembro_id' => $miembro->id,
                        'usuario_id' => $miembro->id,
                        'nombre' => $miembro->nombre,
                        'email' => $miembro->email,
                        'horas_asignadas' => round($horasAsignadas, 2),
                        'horas_disponibles' => $horasDisponibles,
                        'sobrecarga_horas' => round($horasAsignadas - $horasDisponibles, 2),
                        'sobrecarga_porcentaje' => round((($horasAsignadas - $horasDisponibles) / $horasDisponibles) * 100, 2),
                        'nivel' => $this->calcularNivelSobrecarga($horasAsignadas, $horasDisponibles),
                    ];
                }
            }
        }

        // Ordenar por nivel de sobrecarga (crítico primero)
        usort($sobrecarga, function ($a, $b) {
            $niveles = ['critico' => 4, 'alto' => 3, 'medio' => 2, 'bajo' => 1];
            return ($niveles[$b['nivel']] ?? 0) - ($niveles[$a['nivel']] ?? 0);
        });

        return $sobrecarga;
    }

    /**
     * Calcular horas asignadas a un usuario en el proyecto
     */
    private function calcularHorasAsignadas(string $usuarioId, Proyecto $proyecto): float
    {
        $tareas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('responsable', $usuarioId)
            ->whereNotIn('estado', ['COMPLETADA', 'Done', 'DONE'])
            ->get();

        $horasTotales = 0;
        $hoy = Carbon::now();

        foreach ($tareas as $tarea) {
            // Solo contar tareas activas o futuras
            if ($tarea->fecha_fin && Carbon::parse($tarea->fecha_fin)->lt($hoy->copy()->subWeeks(2))) {
                continue;
            }

            $horasTotales += $tarea->horas_estimadas ?? 0;
        }

        // Convertir a horas semanales (asumiendo 4 semanas)
        return $horasTotales / 4;
    }

    /**
     * Calcular nivel de sobrecarga
     */
    private function calcularNivelSobrecarga(float $horasAsignadas, float $horasDisponibles): string
    {
        $porcentaje = (($horasAsignadas - $horasDisponibles) / $horasDisponibles) * 100;

        if ($porcentaje > 100) return 'critico'; // Más del doble
        if ($porcentaje > 50) return 'alto';     // 50-100% extra
        if ($porcentaje > 20) return 'medio';    // 20-50% extra
        return 'bajo';
    }

    /**
     * Redistribuir carga entre miembros del equipo
     */
    public function redistribuirCarga(Proyecto $proyecto): array
    {
        $sobrecargados = $this->detectarSobrecarga($proyecto);

        if (empty($sobrecargados)) {
            return [
                'redistribuciones' => [],
                'mensaje' => 'No hay recursos sobrecargados',
            ];
        }

        $redistribuciones = [];

        // Obtener todos los miembros del proyecto
        $miembros = $proyecto->equipos()
            ->with('miembros')
            ->get()
            ->pluck('miembros')
            ->flatten()
            ->filter(fn($m) => $m !== null);

        foreach ($sobrecargados as $sobrecargado) {
            // Buscar tareas que puedan reasignarse
            $tareas = TareaProyecto::where('id_proyecto', $proyecto->id)
                ->where('responsable', $sobrecargado['usuario_id'])
                ->whereNotIn('estado', ['COMPLETADA', 'Done', 'DONE'])
                ->where('es_ruta_critica', false) // Evitar tareas críticas
                ->orderBy('prioridad', 'asc') // Primero las de menor prioridad
                ->get();

            $horasALiberar = $sobrecargado['sobrecarga_horas'];
            $horasLiberadas = 0;

            foreach ($tareas as $tarea) {
                if ($horasLiberadas >= $horasALiberar) {
                    break;
                }

                // Buscar miembro con capacidad disponible
                $nuevoResponsable = $this->encontrarMiembroDisponible(
                    $miembros,
                    $proyecto,
                    $tarea->horas_estimadas ?? 0,
                    $sobrecargado['usuario_id']
                );

                if ($nuevoResponsable) {
                    $redistribuciones[] = [
                        'tarea_id' => $tarea->id_tarea,
                        'tarea_nombre' => $tarea->nombre,
                        'responsable_anterior_id' => $sobrecargado['usuario_id'],
                        'responsable_anterior_nombre' => $sobrecargado['nombre'],
                        'responsable_nuevo_id' => $nuevoResponsable->id,
                        'responsable_nuevo_nombre' => $nuevoResponsable->nombre,
                        'horas' => $tarea->horas_estimadas ?? 0,
                        'justificacion' => "Liberar carga de {$sobrecargado['nombre']} ({$sobrecargado['sobrecarga_porcentaje']}% sobrecargado)",
                    ];

                    $horasLiberadas += $tarea->horas_estimadas ?? 0;
                }
            }
        }

        return [
            'redistribuciones' => $redistribuciones,
            'total_reasignaciones' => count($redistribuciones),
            'horas_rebalanceadas' => array_sum(array_column($redistribuciones, 'horas')),
        ];
    }

    /**
     * Encontrar miembro con capacidad disponible
     */
    private function encontrarMiembroDisponible(
        Collection $miembros,
        Proyecto $proyecto,
        float $horasNecesarias,
        string $excluirId
    ): ?object {
        foreach ($miembros as $miembro) {
            // $miembro ya ES un Usuario
            if (!$miembro || $miembro->id == $excluirId) {
                continue;
            }

            $horasAsignadas = $this->calcularHorasAsignadas($miembro->id, $proyecto);
            $horasDisponibles = 40;

            $capacidadLibre = $horasDisponibles - $horasAsignadas;

            if ($capacidadLibre >= $horasNecesarias) {
                return $miembro;
            }
        }

        return null;
    }

    /**
     * Obtener estadísticas de utilización de recursos
     */
    public function obtenerEstadisticasRecursos(Proyecto $proyecto): array
    {
        $equipos = $proyecto->equipos()->with('miembros')->get();
        $estadisticas = [
            'total_miembros' => 0,
            'miembros_sobrecargados' => 0,
            'miembros_disponibles' => 0,
            'utilizacion_promedio' => 0,
            'capacidad_total' => 0,
            'horas_asignadas_total' => 0,
        ];

        $utilizaciones = [];

        foreach ($equipos as $equipo) {
            foreach ($equipo->miembros as $miembro) {
                // $miembro ya ES un Usuario
                if (!$miembro) continue;

                $estadisticas['total_miembros']++;

                $horasAsignadas = $this->calcularHorasAsignadas($miembro->id, $proyecto);
                $horasDisponibles = 40;

                $estadisticas['capacidad_total'] += $horasDisponibles;
                $estadisticas['horas_asignadas_total'] += $horasAsignadas;

                $utilizacion = ($horasAsignadas / max($horasDisponibles, 1)) * 100;
                $utilizaciones[] = $utilizacion;

                if ($horasAsignadas > $horasDisponibles) {
                    $estadisticas['miembros_sobrecargados']++;
                } elseif ($horasAsignadas < $horasDisponibles * 0.7) {
                    $estadisticas['miembros_disponibles']++;
                }
            }
        }

        $estadisticas['utilizacion_promedio'] = !empty($utilizaciones)
            ? round(array_sum($utilizaciones) / count($utilizaciones), 2)
            : 0;

        return $estadisticas;
    }
}
