<?php

namespace App\Services;

use App\Models\Proyecto;
use App\Models\AjusteCronograma;
use App\Models\HistorialAjusteTarea;
use App\Models\TareaProyecto;
use App\Services\Cronograma\DetectorDesviaciones;
use App\Services\Cronograma\MotorAjuste;
use App\Services\Cronograma\OptimizadorRecursos;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Servicio principal de Cronograma Inteligente
 * Orquesta la detección, análisis y ajuste automático del cronograma
 */
class CronogramaInteligenteService
{
    protected $detectorDesviaciones;
    protected $motorAjuste;
    protected $optimizadorRecursos;

    public function __construct()
    {
        $this->detectorDesviaciones = new DetectorDesviaciones();
        $this->motorAjuste = new MotorAjuste();
        $this->optimizadorRecursos = new OptimizadorRecursos();
    }

    /**
     * Analizar cronograma completo del proyecto
     */
    public function analizarCronograma(Proyecto $proyecto): array
    {
        // 1. Calcular ruta crítica
        $rutaCritica = $this->detectorDesviaciones->calcularRutaCritica($proyecto);

        // 2. Detectar desviaciones
        $desviaciones = $this->detectorDesviaciones->detectarDesviaciones($proyecto);

        // 3. Detectar recursos sobrecargados
        $sobrecarga = $this->optimizadorRecursos->detectarSobrecarga($proyecto);

        // 4. Calcular salud del proyecto
        $salud = $this->calcularSaludProyecto($rutaCritica, $desviaciones, $sobrecarga);

        return [
            'ruta_critica' => $rutaCritica,
            'desviaciones' => $desviaciones,
            'recursos_sobrecargados' => $sobrecarga,
            'salud_proyecto' => $salud,
            'estadisticas_recursos' => $this->optimizadorRecursos->obtenerEstadisticasRecursos($proyecto),
            'fecha_analisis' => Carbon::now(),
        ];
    }

    /**
     * Generar ajuste automático del cronograma
     */
    public function generarAjuste(Proyecto $proyecto, array $opciones = []): ?AjusteCronograma
    {
        // Analizar situación actual
        $analisis = $this->analizarCronograma($proyecto);

        // Si no hay problemas, no generar ajuste
        if ($analisis['desviaciones']->isEmpty() && empty($analisis['recursos_sobrecargados'])) {
            return null;
        }

        // Generar soluciones
        $soluciones = $this->motorAjuste->generarSoluciones($proyecto, $analisis, $opciones);

        if ($soluciones->isEmpty()) {
            return null;
        }

        // Seleccionar mejor solución (mayor score)
        $mejorSolucion = $soluciones->first();

        // Crear registro de ajuste propuesto
        $ajuste = AjusteCronograma::create([
            'proyecto_id' => $proyecto->id,
            'tipo_ajuste' => 'automatico',
            'estado' => 'propuesto',
            'desviaciones_detectadas' => $analisis['desviaciones']->toArray(),
            'ruta_critica' => $analisis['ruta_critica']['tareas_criticas'] ?? [],
            'recursos_sobrecargados' => $analisis['recursos_sobrecargados'],
            'estrategia' => $mejorSolucion['estrategia'],
            'ajustes_propuestos' => $mejorSolucion['ajustes'],
            'dias_recuperados' => $mejorSolucion['dias_recuperados'],
            'recursos_afectados' => $mejorSolucion['recursos_afectados'],
            'score_solucion' => $mejorSolucion['score'],
            'costo_adicional_estimado' => $mejorSolucion['costo_adicional'] ?? 0,
            'motivo_ajuste' => $this->generarMotivoAjuste($analisis),
            'creado_por' => Auth::id(),
        ]);

        // Crear historial de cambios propuestos para cada tarea
        foreach ($mejorSolucion['ajustes'] as $ajusteTarea) {
            $this->crearHistorialTarea($ajuste, $ajusteTarea);
        }

        return $ajuste;
    }

    /**
     * Simular ajuste sin guardarlo en BD (modo preview)
     */
    public function simularAjuste(Proyecto $proyecto, array $opciones = []): array
    {
        $analisis = $this->analizarCronograma($proyecto);
        $soluciones = $this->motorAjuste->generarSoluciones($proyecto, $analisis, $opciones);

        return [
            'analisis' => $analisis,
            'soluciones' => $soluciones->toArray(),
            'mejor_solucion' => $soluciones->first(),
            'total_soluciones' => $soluciones->count(),
        ];
    }

    /**
     * Aprobar un ajuste propuesto
     */
    public function aprobarAjuste(AjusteCronograma $ajuste, string $aprobadorId): bool
    {
        if ($ajuste->estado !== 'propuesto') {
            return false;
        }

        $ajuste->update([
            'estado' => 'aprobado',
            'aprobado_por' => $aprobadorId,
            'aprobado_en' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Rechazar un ajuste propuesto
     */
    public function rechazarAjuste(AjusteCronograma $ajuste, string $motivo, string $rechazadorId): bool
    {
        if ($ajuste->estado !== 'propuesto') {
            return false;
        }

        $ajuste->update([
            'estado' => 'rechazado',
            'notas_rechazo' => $motivo,
            'aprobado_por' => $rechazadorId,
            'aprobado_en' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Aplicar un ajuste aprobado al cronograma
     */
    public function aplicarAjuste(AjusteCronograma $ajuste): bool
    {
        if ($ajuste->estado !== 'aprobado') {
            return false;
        }

        DB::beginTransaction();

        try {
            $ajustesAplicados = [];

            foreach ($ajuste->historialTareas as $historial) {
                $tarea = $historial->tarea;

                if (!$tarea) continue;

                // Guardar valores originales si no existen
                if (!$tarea->fecha_inicio_original) {
                    $tarea->fecha_inicio_original = $tarea->fecha_inicio;
                    $tarea->fecha_fin_original = $tarea->fecha_fin;
                }

                // Aplicar cambios según tipo
                $cambiosAplicados = [];

                if ($historial->fecha_inicio_nueva) {
                    $tarea->fecha_inicio = $historial->fecha_inicio_nueva;
                    $cambiosAplicados[] = 'fecha_inicio';
                }

                if ($historial->fecha_fin_nueva) {
                    $tarea->fecha_fin = $historial->fecha_fin_nueva;
                    $cambiosAplicados[] = 'fecha_fin';
                }

                if ($historial->responsable_nuevo) {
                    $tarea->responsable = $historial->responsable_nuevo;
                    $cambiosAplicados[] = 'responsable';
                }

                if ($historial->horas_estimadas_nueva) {
                    $tarea->horas_estimadas = $historial->horas_estimadas_nueva;
                    $cambiosAplicados[] = 'horas_estimadas';
                }

                $tarea->save();

                // Marcar historial como aplicado
                $historial->update(['aplicado' => true]);

                $ajustesAplicados[] = [
                    'tarea_id' => $tarea->id_tarea,
                    'cambios' => $cambiosAplicados,
                ];
            }

            // Actualizar ajuste
            $ajuste->update([
                'estado' => 'aplicado',
                'ajustes_aplicados' => $ajustesAplicados,
            ]);

            DB::commit();

            // Recalcular ruta crítica después de aplicar cambios
            $this->detectorDesviaciones->calcularRutaCritica($ajuste->proyecto);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error aplicando ajuste de cronograma: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Revertir un ajuste aplicado
     */
    public function revertirAjuste(AjusteCronograma $ajuste): bool
    {
        if ($ajuste->estado !== 'aplicado') {
            return false;
        }

        DB::beginTransaction();

        try {
            foreach ($ajuste->historialTareas()->where('aplicado', true)->get() as $historial) {
                $tarea = $historial->tarea;

                if (!$tarea) continue;

                // Restaurar valores anteriores
                if ($historial->fecha_inicio_anterior) {
                    $tarea->fecha_inicio = $historial->fecha_inicio_anterior;
                }

                if ($historial->fecha_fin_anterior) {
                    $tarea->fecha_fin = $historial->fecha_fin_anterior;
                }

                if ($historial->responsable_anterior) {
                    $tarea->responsable = $historial->responsable_anterior;
                }

                if ($historial->horas_estimadas_anterior) {
                    $tarea->horas_estimadas = $historial->horas_estimadas_anterior;
                }

                $tarea->save();

                $historial->update(['aplicado' => false]);
            }

            $ajuste->update(['estado' => 'revertido']);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error revirtiendo ajuste: ' . $e->getMessage());
            return false;
        }
    }

    // ===== MÉTODOS AUXILIARES =====

    /**
     * Calcular salud general del proyecto (0-100)
     */
    private function calcularSaludProyecto(array $rutaCritica, $desviaciones, array $sobrecarga): array
    {
        $score = 100;
        $nivel = 'optimo';
        $alertas = [];

        // Penalizar por atrasos
        $atrasos = $desviaciones->where('tipo', 'atraso');
        $atrasosCriticos = $atrasos->where('en_ruta_critica', true);

        $score -= $atrasosCriticos->count() * 15;
        $score -= $atrasos->where('en_ruta_critica', false)->count() * 5;

        if ($atrasosCriticos->isNotEmpty()) {
            $alertas[] = "{$atrasosCriticos->count()} tarea(s) crítica(s) atrasada(s)";
        }

        // Penalizar por riesgos
        $riesgos = $desviaciones->where('tipo', 'riesgo');
        $score -= $riesgos->count() * 3;

        if ($riesgos->isNotEmpty()) {
            $alertas[] = "{$riesgos->count()} tarea(s) en riesgo de atraso";
        }

        // Penalizar por sobrecarga de recursos
        $score -= count($sobrecarga) * 8;

        if (!empty($sobrecarga)) {
            $alertas[] = count($sobrecarga) . " recurso(s) sobrecargado(s)";
        }

        // Penalizar si la fecha calculada excede la fecha oficial
        if (isset($rutaCritica['dias_diferencia']) && $rutaCritica['dias_diferencia'] < 0) {
            $diasExceso = abs($rutaCritica['dias_diferencia']);
            $score -= $diasExceso * 2;
            $alertas[] = "Proyecto se extiende {$diasExceso} días sobre fecha límite";
        }

        $score = max(0, min(100, $score));

        // Determinar nivel
        if ($score >= 90) {
            $nivel = 'optimo';
            $emoji = '🟢';
        } elseif ($score >= 70) {
            $nivel = 'bueno';
            $emoji = '🟡';
        } elseif ($score >= 50) {
            $nivel = 'regular';
            $emoji = '🟠';
        } else {
            $nivel = 'critico';
            $emoji = '🔴';
        }

        return [
            'score' => round($score, 2),
            'nivel' => $nivel,
            'emoji' => $emoji,
            'alertas' => $alertas,
            'total_tareas_criticas' => count($rutaCritica['tareas_criticas'] ?? []),
            'total_desviaciones' => $desviaciones->count(),
            'total_sobrecarga' => count($sobrecarga),
        ];
    }

    /**
     * Generar motivo del ajuste
     */
    private function generarMotivoAjuste(array $analisis): string
    {
        $motivos = [];

        $atrasos = $analisis['desviaciones']->where('tipo', 'atraso');
        if ($atrasos->isNotEmpty()) {
            $motivos[] = "{$atrasos->count()} tarea(s) con atraso detectado";
        }

        $riesgos = $analisis['desviaciones']->where('tipo', 'riesgo');
        if ($riesgos->isNotEmpty()) {
            $motivos[] = "{$riesgos->count()} tarea(s) en riesgo";
        }

        if (!empty($analisis['recursos_sobrecargados'])) {
            $motivos[] = count($analisis['recursos_sobrecargados']) . " recurso(s) sobrecargado(s)";
        }

        return 'Ajuste automático: ' . implode(', ', $motivos);
    }

    /**
     * Crear historial de cambio para una tarea
     */
    private function crearHistorialTarea(AjusteCronograma $ajuste, array $ajusteTarea): void
    {
        $tareaId = $ajusteTarea['tarea_id'] ?? null;

        if (!$tareaId) return;

        $tarea = TareaProyecto::find($tareaId);
        if (!$tarea) return;

        HistorialAjusteTarea::create([
            'ajuste_id' => $ajuste->id,
            'tarea_id' => $tareaId,
            'fecha_inicio_anterior' => $tarea->fecha_inicio,
            'fecha_fin_anterior' => $tarea->fecha_fin,
            'duracion_anterior' => $ajusteTarea['duracion_anterior'] ?? null,
            'responsable_anterior' => $tarea->responsable,
            'horas_estimadas_anterior' => $tarea->horas_estimadas,
            'fecha_inicio_nueva' => $ajusteTarea['fecha_inicio_nueva'] ?? null,
            'fecha_fin_nueva' => $ajusteTarea['fecha_fin_nueva'] ?? null,
            'duracion_nueva' => $ajusteTarea['duracion_nueva'] ?? null,
            'responsable_nuevo' => $ajusteTarea['responsable_nuevo'] ?? null,
            'horas_estimadas_nueva' => $ajusteTarea['horas_estimadas_nueva'] ?? null,
            'tipo_cambio' => $ajusteTarea['accion'] ?? 'otros',
            'impacto_estimado' => $ajusteTarea['justificacion'] ?? '',
            'aplicado' => false,
        ]);
    }
}
