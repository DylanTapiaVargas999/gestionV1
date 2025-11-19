<?php

namespace App\Services\Cronograma;

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Servicio para detectar desviaciones y calcular ruta crítica
 */
class DetectorDesviaciones
{
    /**
     * Detecta todas las desviaciones del cronograma
     */
    public function detectarDesviaciones(Proyecto $proyecto): Collection
    {
        $hoy = Carbon::now();
        $desviaciones = collect([]);

        $tareas = $proyecto->tareas()->with(['fase', 'sprint', 'responsableUsuario'])->get();

        foreach ($tareas as $tarea) {
            // Saltar tareas completadas (usa método del modelo que es case-insensitive)
            if ($tarea->estaCompletada()) {
                continue;
            }

            // Detectar atraso real
            if ($tarea->fecha_fin && Carbon::parse($tarea->fecha_fin)->lt($hoy)) {
                $diasAtraso = Carbon::parse($tarea->fecha_fin)->diffInDays($hoy);

                $desviaciones->push([
                    'tipo' => 'atraso',
                    'severidad' => $this->calcularSeveridad($tarea, $diasAtraso),
                    'tarea' => $tarea,
                    'dias_atraso' => round($diasAtraso, 0), // Redondear a entero
                    'en_ruta_critica' => $tarea->es_ruta_critica ?? false,
                    'impacto' => $this->calcularImpactoAtraso($tarea, $diasAtraso),
                ]);
            }

            // Detectar riesgo de atraso (fecha cercana con poco progreso)
            if ($tarea->fecha_fin && !Carbon::parse($tarea->fecha_fin)->lt($hoy)) {
                $probabilidadAtraso = $this->calcularProbabilidadAtraso($tarea);

                if ($probabilidadAtraso >= 50) {
                    $desviaciones->push([
                        'tipo' => 'riesgo',
                        'severidad' => $probabilidadAtraso >= 75 ? 'alta' : 'media',
                        'tarea' => $tarea,
                        'probabilidad_atraso' => $probabilidadAtraso,
                        'dias_restantes' => Carbon::parse($tarea->fecha_fin)->diffInDays($hoy),
                        'progreso_actual' => $tarea->progreso_real ?? 0,
                        'en_ruta_critica' => $tarea->es_ruta_critica ?? false,
                    ]);
                }
            }
        }

        return $desviaciones->sortByDesc(function ($desv) {
            $severidadPeso = [
                'critica' => 4,
                'alta' => 3,
                'media' => 2,
                'baja' => 1,
            ];
            return $severidadPeso[$desv['severidad']] ?? 0;
        })->values();
    }

    /**
     * Calcula la ruta crítica del proyecto usando CPM (Critical Path Method)
     * Adaptado para Scrum (sprints) y Cascada (fases)
     */
    public function calcularRutaCritica(Proyecto $proyecto): array
    {
        $tareas = $proyecto->tareas()->with(['fase', 'sprint'])->get();

        if ($tareas->isEmpty()) {
            return [
                'tareas_criticas' => [],
                'duracion_proyecto' => 0,
                'fecha_fin_calculada' => null,
            ];
        }

        // Construir grafo de dependencias
        $grafo = $this->construirGrafoDependencias($tareas);

        // Forward Pass: Calcular ES (Early Start) y EF (Early Finish)
        $es = [];
        $ef = [];

        foreach ($tareas as $tarea) {
            $dependencias = $tarea->dependencias ?? [];

            if (empty($dependencias)) {
                $es[$tarea->id_tarea] = 0;
            } else {
                $maxEF = 0;
                foreach ($dependencias as $depId) {
                    if (isset($ef[$depId])) {
                        $maxEF = max($maxEF, $ef[$depId]);
                    }
                }
                $es[$tarea->id_tarea] = $maxEF;
            }

            $duracion = $this->calcularDuracion($tarea);
            $ef[$tarea->id_tarea] = $es[$tarea->id_tarea] + $duracion;
        }

        // Encontrar duración total del proyecto
        $duracionProyecto = !empty($ef) ? max($ef) : 0;

        // Backward Pass: Calcular LS (Late Start) y LF (Late Finish)
        $lf = [];
        $ls = [];

        foreach ($tareas->reverse() as $tarea) {
            $sucesores = $this->encontrarSucesores($tarea->id_tarea, $grafo);

            if (empty($sucesores)) {
                $lf[$tarea->id_tarea] = $duracionProyecto;
            } else {
                $minLS = $duracionProyecto;
                foreach ($sucesores as $sucId) {
                    if (isset($ls[$sucId])) {
                        $minLS = min($minLS, $ls[$sucId]);
                    }
                }
                $lf[$tarea->id_tarea] = $minLS;
            }

            $duracion = $this->calcularDuracion($tarea);
            $ls[$tarea->id_tarea] = $lf[$tarea->id_tarea] - $duracion;
        }

        // Calcular holgura y identificar ruta crítica
        $tareasCriticas = [];

        foreach ($tareas as $tarea) {
            $slack = isset($ls[$tarea->id_tarea]) && isset($es[$tarea->id_tarea])
                ? $ls[$tarea->id_tarea] - $es[$tarea->id_tarea]
                : 0;

            $esCritica = $slack == 0;

            // Actualizar tarea en BD
            $tarea->update([
                'holgura_dias' => $slack,
                'es_ruta_critica' => $esCritica,
            ]);

            if ($esCritica) {
                $tareasCriticas[] = [
                    'id' => $tarea->id_tarea,
                    'nombre' => $tarea->nombre,
                    'fase' => $tarea->fase->nombre_fase ?? '',
                    'duracion' => $this->calcularDuracion($tarea),
                    'es' => $es[$tarea->id_tarea],
                    'ef' => $ef[$tarea->id_tarea],
                ];
            }
        }

        // Calcular fecha fin proyectada
        $fechaInicio = Carbon::parse($proyecto->fecha_inicio);
        $fechaFinCalculada = $fechaInicio->copy()->addDays($duracionProyecto);

        return [
            'tareas_criticas' => $tareasCriticas,
            'duracion_proyecto' => $duracionProyecto,
            'fecha_fin_calculada' => $fechaFinCalculada,
            'fecha_fin_oficial' => Carbon::parse($proyecto->fecha_fin),
            'dias_diferencia' => $fechaFinCalculada->diffInDays(Carbon::parse($proyecto->fecha_fin), false),
        ];
    }

    /**
     * Construir grafo de dependencias
     */
    private function construirGrafoDependencias(Collection $tareas): array
    {
        $grafo = [];

        foreach ($tareas as $tarea) {
            $grafo[$tarea->id_tarea] = [
                'dependencias' => $tarea->dependencias ?? [],
                'sucesores' => [],
            ];
        }

        // Llenar sucesores
        foreach ($tareas as $tarea) {
            // Validar que la tarea sea válida antes de acceder a sus propiedades
            if (!$tarea || !isset($tarea->id_tarea)) continue;

            foreach ($tarea->dependencias ?? [] as $depId) {
                if (isset($grafo[$depId])) {
                    $grafo[$depId]['sucesores'][] = $tarea->id_tarea;
                }
            }
        }

        return $grafo;
    }

    /**
     * Encontrar tareas sucesoras
     */
    private function encontrarSucesores(int $tareaId, array $grafo): array
    {
        return $grafo[$tareaId]['sucesores'] ?? [];
    }

    /**
     * Calcular duración de una tarea en días
     */
    private function calcularDuracion(TareaProyecto $tarea): int
    {
        if ($tarea->fecha_inicio && $tarea->fecha_fin) {
            return Carbon::parse($tarea->fecha_inicio)
                ->diffInDays(Carbon::parse($tarea->fecha_fin)) + 1;
        }

        // Fallback: estimar por horas
        if ($tarea->horas_estimadas) {
            return ceil($tarea->horas_estimadas / 8); // 8 horas por día
        }

        return 1; // Mínimo 1 día
    }

    /**
     * Calcular severidad de un atraso
     */
    private function calcularSeveridad(TareaProyecto $tarea, int $diasAtraso): string
    {
        if ($tarea->es_ruta_critica && $diasAtraso > 3) {
            return 'critica';
        }

        if ($diasAtraso > 7 || $tarea->es_ruta_critica) {
            return 'alta';
        }

        if ($diasAtraso > 3) {
            return 'media';
        }

        return 'baja';
    }

    /**
     * Calcular impacto de un atraso
     */
    private function calcularImpactoAtraso(TareaProyecto $tarea, int|float $diasAtraso): string
    {
        // Redondear a entero para mejor lectura
        $diasAtraso = round($diasAtraso, 0);

        if ($tarea->es_ruta_critica) {
            return "Afecta directamente la fecha de entrega del proyecto (+{$diasAtraso} días)";
        }

        $holgura = round($tarea->holgura_dias ?? 0, 0);

        if ($diasAtraso > $holgura) {
            $exceso = $diasAtraso - $holgura;
            return "Excede holgura por {$exceso} días, puede afectar ruta crítica";
        }

        return "Dentro de holgura ({$holgura} días), impacto controlado";
    }

    /**
     * Calcular probabilidad de atraso (0-100%)
     */
    private function calcularProbabilidadAtraso(TareaProyecto $tarea): float
    {
        $hoy = Carbon::now();
        $fechaInicio = Carbon::parse($tarea->fecha_inicio);
        $fechaFin = Carbon::parse($tarea->fecha_fin);

        // Si no ha empezado, probabilidad baja
        if ($hoy->lt($fechaInicio)) {
            return 0;
        }

        $duracionTotal = $fechaInicio->diffInDays($fechaFin);
        $diasTranscurridos = $fechaInicio->diffInDays($hoy);
        $progresoEsperado = ($diasTranscurridos / max($duracionTotal, 1)) * 100;

        $progresoReal = $tarea->progreso_real ?? 0;

        // Si el progreso real está muy por debajo del esperado
        $diferencia = $progresoEsperado - $progresoReal;

        if ($diferencia > 40) {
            return 90;
        } elseif ($diferencia > 25) {
            return 75;
        } elseif ($diferencia > 15) {
            return 60;
        } elseif ($diferencia > 5) {
            return 40;
        }

        return max(0, $diferencia * 2); // Escalar diferencia a probabilidad
    }
}
