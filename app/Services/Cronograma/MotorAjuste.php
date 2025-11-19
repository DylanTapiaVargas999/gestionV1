<?php

namespace App\Services\Cronograma;

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Motor de ajuste automático de cronograma
 * Genera y evalúa múltiples estrategias de optimización
 */
class MotorAjuste
{
    /**
     * Generar soluciones de ajuste para el proyecto
     */
    public function generarSoluciones(
        Proyecto $proyecto,
        array $analisis,
        array $opciones = []
    ): Collection {
        $soluciones = collect([]);

        // Opciones por defecto
        $opciones = array_merge([
            'permitir_compresion' => true,
            'permitir_paralelizacion' => true,
            'permitir_reasignacion' => true,
            'permitir_reduccion_alcance' => false,
            'max_compresion_porcentaje' => 30, // Máximo 30% de compresión
            'max_aumento_horas' => 50, // Máximo 50% más horas
        ], $opciones);

        $desviaciones = $analisis['desviaciones'] ?? collect([]);
        $rutaCritica = $analisis['ruta_critica']['tareas_criticas'] ?? [];

        // Si no hay desviaciones significativas, no hace falta ajustar
        if ($desviaciones->isEmpty()) {
            return $soluciones;
        }

        // ESTRATEGIA 1: Compresión de tareas críticas
        if ($opciones['permitir_compresion']) {
            $solComprimir = $this->estrategiaCompresion(
                $proyecto,
                $rutaCritica,
                $desviaciones,
                $opciones
            );
            if ($solComprimir) {
                $soluciones->push($solComprimir);
            }
        }

        // ESTRATEGIA 2: Paralelización de tareas
        if ($opciones['permitir_paralelizacion']) {
            $solParalelizar = $this->estrategiaParalelizacion(
                $proyecto,
                $rutaCritica,
                $desviaciones,
                $opciones
            );
            if ($solParalelizar) {
                $soluciones->push($solParalelizar);
            }
        }

        // ESTRATEGIA 3: Reasignación de recursos
        if ($opciones['permitir_reasignacion']) {
            $solReasignar = $this->estrategiaReasignacion(
                $proyecto,
                $rutaCritica,
                $desviaciones,
                $opciones
            );
            if ($solReasignar) {
                $soluciones->push($solReasignar);
            }
        }

        // ESTRATEGIA 4: Solución mixta (combinación de las anteriores)
        $solMixta = $this->estrategiaMixta(
            $proyecto,
            $rutaCritica,
            $desviaciones,
            $opciones
        );
        if ($solMixta) {
            $soluciones->push($solMixta);
        }

        // Evaluar cada solución
        return $soluciones
            ->map(function ($solucion) {
                return $this->evaluarSolucion($solucion);
            })
            ->sortByDesc('score')
            ->values();
    }

    /**
     * ESTRATEGIA 1: Comprimir duración de tareas en ruta crítica
     */
    private function estrategiaCompresion(
        Proyecto $proyecto,
        array $rutaCritica,
        Collection $desviaciones,
        array $opciones
    ): ?array {
        $ajustes = [];
        $diasRecuperados = 0;
        $costoAdicional = 0;

        // Calcular días que necesitamos recuperar
        $diasNecesarios = $this->calcularDiasNecesarios($desviaciones);

        foreach ($rutaCritica as $tareaData) {
            if ($diasRecuperados >= $diasNecesarios) {
                break;
            }

            $tarea = TareaProyecto::find($tareaData['id']);
            if (!$tarea) continue;

            $duracionActual = $this->calcularDuracionDias($tarea);
            $duracionMinima = $tarea->duracion_minima ?? ceil($duracionActual * 0.5);

            // Calcular compresión posible
            $maxCompresion = $duracionActual - $duracionMinima;
            $compresionDeseada = min($maxCompresion, ceil($duracionActual * ($opciones['max_compresion_porcentaje'] / 100)));

            if ($compresionDeseada > 0) {
                $nuevaDuracion = $duracionActual - $compresionDeseada;
                $nuevaFechaFin = Carbon::parse($tarea->fecha_inicio)->addDays($nuevaDuracion - 1);

                $ajustes[] = [
                    'tarea_id' => $tarea->id_tarea,
                    'tarea_nombre' => $tarea->nombre,
                    'accion' => 'comprimir',
                    'duracion_anterior' => $duracionActual,
                    'duracion_nueva' => $nuevaDuracion,
                    'fecha_fin_anterior' => $tarea->fecha_fin,
                    'fecha_fin_nueva' => $nuevaFechaFin->format('Y-m-d'),
                    'dias_ganados' => $compresionDeseada,
                    'riesgo' => $this->calcularRiesgoCompresion($compresionDeseada, $duracionActual),
                    'justificacion' => "Reducir duración de {$duracionActual} a {$nuevaDuracion} días para recuperar tiempo",
                ];

                $diasRecuperados += $compresionDeseada;
                $costoAdicional += $compresionDeseada * 500; // Estimación: $500 por día comprimido
            }
        }

        if (empty($ajustes)) {
            return null;
        }

        return [
            'estrategia' => 'compresion',
            'nombre' => 'Compresión de Tareas Críticas',
            'descripcion' => 'Reducir la duración de tareas en la ruta crítica',
            'ajustes' => $ajustes,
            'dias_recuperados' => $diasRecuperados,
            'recursos_afectados' => count($ajustes),
            'costo_adicional' => $costoAdicional,
            'riesgo_general' => $this->calcularRiesgoGeneral($ajustes),
            'score' => 0, // Se calculará después
        ];
    }

    /**
     * ESTRATEGIA 2: Paralelizar tareas que pueden ejecutarse simultáneamente
     */
    private function estrategiaParalelizacion(
        Proyecto $proyecto,
        array $rutaCritica,
        Collection $desviaciones,
        array $opciones
    ): ?array {
        $ajustes = [];
        $diasRecuperados = 0;

        $tareas = $proyecto->tareas()
            ->where('puede_paralelizarse', true)
            ->orWhereNull('puede_paralelizarse')
            ->get();

        // Buscar pares de tareas que actualmente son secuenciales pero podrían ser paralelas
        $parejas = $this->encontrarTareasParalelizables($tareas);

        foreach ($parejas as $pareja) {
            $tarea1 = $pareja['tarea1'];
            $tarea2 = $pareja['tarea2'];

            // Verificar que no tengan dependencias entre ellas
            if ($this->tieneDependencia($tarea1, $tarea2)) {
                continue;
            }

            // Calcular cuántos días se ahorrarían ejecutándolas en paralelo
            $diasAhorro = $this->calcularAhorroParalelizacion($tarea1, $tarea2);

            if ($diasAhorro > 0) {
                $ajustes[] = [
                    'tarea1_id' => $tarea1->id_tarea,
                    'tarea1_nombre' => $tarea1->nombre,
                    'tarea2_id' => $tarea2->id_tarea,
                    'tarea2_nombre' => $tarea2->nombre,
                    'accion' => 'paralelizar',
                    'fecha_inicio_nueva' => $tarea1->fecha_inicio,
                    'dias_ganados' => $diasAhorro,
                    'riesgo' => 'medio',
                    'justificacion' => "Ejecutar '{$tarea1->nombre}' y '{$tarea2->nombre}' en paralelo",
                ];

                $diasRecuperados += $diasAhorro;
            }
        }

        if (empty($ajustes)) {
            return null;
        }

        return [
            'estrategia' => 'paralelizacion',
            'nombre' => 'Paralelización de Tareas',
            'descripcion' => 'Ejecutar tareas compatibles simultáneamente',
            'ajustes' => $ajustes,
            'dias_recuperados' => $diasRecuperados,
            'recursos_afectados' => count($ajustes) * 2,
            'costo_adicional' => 0,
            'riesgo_general' => 'medio',
            'score' => 0,
        ];
    }

    /**
     * ESTRATEGIA 3: Reasignar recursos a tareas críticas
     */
    private function estrategiaReasignacion(
        Proyecto $proyecto,
        array $rutaCritica,
        Collection $desviaciones,
        array $opciones
    ): ?array {
        $ajustes = [];
        $diasRecuperados = 0;
        $costoAdicional = 0;

        // Obtener todos los usuarios miembros del proyecto
        $equipos = $proyecto->equipos()
            ->with('miembros')
            ->get();

        $usuarios = collect();
        foreach ($equipos as $equipo) {
            foreach ($equipo->miembros as $usuario) {
                // Los miembros ya son usuarios directamente (belongsToMany)
                if (!$usuarios->contains('id', $usuario->id)) {
                    $usuarios->push($usuario);
                }
            }
        }

        if ($usuarios->isEmpty()) {
            return null;
        }

        // Para cada tarea crítica atrasada, buscar mejor recurso
        foreach ($desviaciones as $desv) {
            if ($desv['tipo'] !== 'atraso' || !$desv['en_ruta_critica']) {
                continue;
            }

            $tarea = $desv['tarea'];
            $responsableActual = $tarea->responsable;

            // Buscar recurso con más experiencia/disponibilidad
            $mejorRecurso = $this->encontrarMejorRecurso($tarea, $usuarios, $responsableActual);

            if ($mejorRecurso && $mejorRecurso->id != $responsableActual) {
                $reduccionEstimada = ceil($desv['dias_atraso'] * 0.3); // 30% de mejora estimada

                $ajustes[] = [
                    'tarea_id' => $tarea->id_tarea,
                    'tarea_nombre' => $tarea->nombre,
                    'accion' => 'reasignar',
                    'responsable_anterior' => $responsableActual,
                    'responsable_anterior_nombre' => $tarea->responsableUsuario->nombre ?? 'No asignado',
                    'responsable_nuevo' => $mejorRecurso->id,
                    'responsable_nuevo_nombre' => $mejorRecurso->nombre,
                    'dias_ganados' => $reduccionEstimada,
                    'riesgo' => 'bajo',
                    'justificacion' => "Reasignar a {$mejorRecurso->nombre} (mayor experiencia)",
                ];

                $diasRecuperados += $reduccionEstimada;
            }
        }

        if (empty($ajustes)) {
            return null;
        }

        return [
            'estrategia' => 'reasignacion',
            'nombre' => 'Reasignación de Recursos',
            'descripcion' => 'Asignar tareas críticas a recursos más experimentados',
            'ajustes' => $ajustes,
            'dias_recuperados' => $diasRecuperados,
            'recursos_afectados' => count($ajustes),
            'costo_adicional' => $costoAdicional,
            'riesgo_general' => 'bajo',
            'score' => 0,
        ];
    }

    /**
     * ESTRATEGIA 4: Solución mixta (combinación inteligente)
     */
    private function estrategiaMixta(
        Proyecto $proyecto,
        array $rutaCritica,
        Collection $desviaciones,
        array $opciones
    ): ?array {
        // Combinar las mejores tácticas de cada estrategia
        $ajustes = [];
        $diasRecuperados = 0;
        $costoTotal = 0;

        // 1. Primero, reasignar (bajo costo, bajo riesgo)
        $solReasignar = $this->estrategiaReasignacion($proyecto, $rutaCritica, $desviaciones, $opciones);
        if ($solReasignar) {
            $ajustes = array_merge($ajustes, $solReasignar['ajustes']);
            $diasRecuperados += $solReasignar['dias_recuperados'];
        }

        // 2. Luego, paralelizar (sin costo, riesgo medio)
        $solParalelizar = $this->estrategiaParalelizacion($proyecto, $rutaCritica, $desviaciones, $opciones);
        if ($solParalelizar && $diasRecuperados < $this->calcularDiasNecesarios($desviaciones)) {
            $ajustes = array_merge($ajustes, array_slice($solParalelizar['ajustes'], 0, 2)); // Solo 2 paralelizaciones
            $diasRecuperados += min(10, $solParalelizar['dias_recuperados']);
        }

        // 3. Finalmente, comprimir solo si es necesario (alto costo)
        if ($diasRecuperados < $this->calcularDiasNecesarios($desviaciones)) {
            $solComprimir = $this->estrategiaCompresion($proyecto, $rutaCritica, $desviaciones, $opciones);
            if ($solComprimir) {
                $ajustesCompresion = array_slice($solComprimir['ajustes'], 0, 3); // Solo 3 compresiones
                $ajustes = array_merge($ajustes, $ajustesCompresion);
                $diasRecuperados += array_sum(array_column($ajustesCompresion, 'dias_ganados'));
                $costoTotal += $solComprimir['costo_adicional'];
            }
        }

        if (empty($ajustes)) {
            return null;
        }

        return [
            'estrategia' => 'mixta',
            'nombre' => 'Solución Híbrida Optimizada',
            'descripcion' => 'Combinación de reasignación, paralelización y compresión selectiva',
            'ajustes' => $ajustes,
            'dias_recuperados' => $diasRecuperados,
            'recursos_afectados' => count($ajustes),
            'costo_adicional' => $costoTotal,
            'riesgo_general' => 'medio',
            'score' => 0,
        ];
    }

    /**
     * Evaluar una solución usando scoring multi-criterio
     */
    private function evaluarSolucion(array $solucion): array
    {
        $score = 0;

        // Factor 1: Días recuperados (40% del score)
        $diasObjetivo = 10; // Objetivo por defecto
        $score += min(40, ($solucion['dias_recuperados'] / max($diasObjetivo, 1)) * 40);

        // Factor 2: Bajo impacto en recursos (25% del score)
        $maxRecursos = 10;
        $impactoRecursos = $solucion['recursos_afectados'] / max($maxRecursos, 1);
        $score += (1 - min(1, $impactoRecursos)) * 25;

        // Factor 3: Bajo riesgo (20% del score)
        $riesgoPeso = [
            'bajo' => 20,
            'medio' => 12,
            'alto' => 5,
            'critico' => 0,
        ];
        $score += $riesgoPeso[$solucion['riesgo_general']] ?? 10;

        // Factor 4: Bajo costo (15% del score)
        $maxCosto = 10000;
        $impactoCosto = $solucion['costo_adicional'] / max($maxCosto, 1);
        $score += (1 - min(1, $impactoCosto)) * 15;

        $solucion['score'] = round($score, 2);
        $solucion['score_detalle'] = [
            'dias_recuperados_score' => min(40, ($solucion['dias_recuperados'] / 10) * 40),
            'recursos_score' => (1 - min(1, $impactoRecursos)) * 25,
            'riesgo_score' => $riesgoPeso[$solucion['riesgo_general']] ?? 10,
            'costo_score' => (1 - min(1, $impactoCosto)) * 15,
        ];

        return $solucion;
    }

    // ===== MÉTODOS AUXILIARES =====

    private function calcularDiasNecesarios(Collection $desviaciones): int
    {
        return $desviaciones
            ->where('tipo', 'atraso')
            ->sum('dias_atraso');
    }

    private function calcularDuracionDias(TareaProyecto $tarea): int
    {
        if ($tarea->fecha_inicio && $tarea->fecha_fin) {
            return Carbon::parse($tarea->fecha_inicio)
                ->diffInDays(Carbon::parse($tarea->fecha_fin)) + 1;
        }
        return ceil(($tarea->horas_estimadas ?? 8) / 8);
    }

    private function calcularRiesgoCompresion(int $diasComprimidos, int $duracionOriginal): string
    {
        $porcentaje = ($diasComprimidos / max($duracionOriginal, 1)) * 100;

        if ($porcentaje > 40) return 'critico';
        if ($porcentaje > 25) return 'alto';
        if ($porcentaje > 15) return 'medio';
        return 'bajo';
    }

    private function calcularRiesgoGeneral(array $ajustes): string
    {
        $riesgos = array_column($ajustes, 'riesgo');

        if (in_array('critico', $riesgos)) return 'critico';
        if (in_array('alto', $riesgos)) return 'alto';
        if (count(array_filter($riesgos, fn($r) => $r === 'medio')) > 2) return 'medio';
        return 'bajo';
    }

    private function encontrarTareasParalelizables(Collection $tareas): array
    {
        $parejas = [];

        foreach ($tareas as $t1) {
            // Validar que t1 sea un objeto válido con id_tarea
            if (!$t1 || !isset($t1->id_tarea)) continue;

            foreach ($tareas as $t2) {
                // Validar que t2 sea un objeto válido con id_tarea
                if (!$t2 || !isset($t2->id_tarea)) continue;

                if ($t1->id_tarea >= $t2->id_tarea) continue;

                if ($this->sonParalelizables($t1, $t2)) {
                    $parejas[] = ['tarea1' => $t1, 'tarea2' => $t2];
                }
            }
        }

        return $parejas;
    }

    private function sonParalelizables(TareaProyecto $t1, TareaProyecto $t2): bool
    {
        // Validar que ambas tareas sean objetos válidos
        if (!$t1 || !$t2) return false;

        // Para Scrum: deben estar en el mismo sprint
        if (isset($t1->id_sprint) && isset($t2->id_sprint) && $t1->id_sprint && $t2->id_sprint) {
            if ($t1->id_sprint !== $t2->id_sprint) return false;
        }

        // Para Cascada: misma fase o fases consecutivas
        if (isset($t1->id_fase) && isset($t2->id_fase) && $t1->id_fase && $t2->id_fase) {
            if ($t1->id_fase == $t2->id_fase) return true;
        }

        // No tienen dependencias entre ellas
        return !$this->tieneDependencia($t1, $t2) && !$this->tieneDependencia($t2, $t1);
    }

    private function tieneDependencia(TareaProyecto $tarea, TareaProyecto $posibleDependencia): bool
    {
        // Validar objetos antes de acceder a propiedades
        if (!$tarea || !$posibleDependencia || !isset($posibleDependencia->id_tarea)) {
            return false;
        }

        $dependencias = $tarea->dependencias ?? [];
        return in_array($posibleDependencia->id_tarea, $dependencias);
    }

    private function calcularAhorroParalelizacion(TareaProyecto $t1, TareaProyecto $t2): int
    {
        $duracion1 = $this->calcularDuracionDias($t1);
        $duracion2 = $this->calcularDuracionDias($t2);

        // El ahorro es la duración de la tarea más corta
        return min($duracion1, $duracion2);
    }

    private function encontrarMejorRecurso(TareaProyecto $tarea, Collection $usuarios, ?string $excluirId): ?object
    {
        // Filtrar usuarios disponibles
        $candidatos = $usuarios->filter(function ($usuario) use ($excluirId) {
            return $usuario->id != $excluirId;
        });

        if ($candidatos->isEmpty()) {
            return null;
        }

        // Retornar el primer candidato (podría mejorarse con algoritmo de scoring)
        return $candidatos->first();
    }
}
