<?php

namespace App\Services;

use App\Models\Proyecto;
use App\Models\ElementoConfiguracion;
use App\Models\TareaProyecto;
use App\Models\SolicitudCambio;
use Illuminate\Support\Collection;

class SGCSMetodologiaService
{
    /**
     * Obtener datos integrados SGCS + metodología para un colaborador
     */
    public function getDatosColaborador(Proyecto $proyecto, string $rolUsuario, string $usuarioId): array
    {
        $metodologia = strtolower($proyecto->metodologia->nombre ?? '');

        $datos = [
            'metodologia' => $metodologia,
            'rol' => $rolUsuario,
            'metricas' => $this->getMetricasColaborador($proyecto, $rolUsuario, $usuarioId),
            'elementos' => $this->getECsAsignados($proyecto, $usuarioId),
            'elementos_configuracion' => $this->getECsAsignados($proyecto, $usuarioId), // Alias para compatibilidad
            'tareas' => $this->getTodasLasTareasUsuario($proyecto, $usuarioId), // Todas las tareas del usuario
            'tareas_activas' => $this->getTareasActivas($proyecto, $usuarioId),
            'cambios_relacionados' => $this->getCambiosRelacionados($proyecto, $usuarioId),
        ];

        // Agregar datos específicos por metodología
        if ($metodologia === 'scrum') {
            $datos['sprint_data'] = $this->getSprintData($proyecto, $usuarioId);
        } elseif ($metodologia === 'cascada') {
            $datos['fase_data'] = $this->getFaseData($proyecto, $usuarioId);
        }

        return $datos;
    }

    /**
     * Obtener métricas específicas por rol
     */
    private function getMetricasColaborador(Proyecto $proyecto, string $rol, string $usuarioId): array
    {
        $metricas = [
            'tareas_asignadas' => 0,
            'tareas_completadas' => 0,
            'tareas_en_progreso' => 0,
            'elementos_asignados' => 0,
        ];

        // Tareas asignadas al usuario
        $tareasUsuario = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('responsable', $usuarioId)
            ->get();

        $metricas['tareas_asignadas'] = $tareasUsuario->count();
        $metricas['tareas_completadas'] = $tareasUsuario->filter(function($tarea) {
            return in_array(strtolower($tarea->estado), ['completado', 'completada']);
        })->count();
        $metricas['tareas_en_progreso'] = $tareasUsuario->filter(function($tarea) {
            return in_array(strtolower($tarea->estado), ['en progreso', 'en_progreso', 'en desarrollo', 'en_desarrollo']);
        })->count();

        // ECs donde el usuario tiene tareas asignadas
        $ecsConTareas = $tareasUsuario->pluck('id_ec')->unique();
        $metricas['elementos_asignados'] = $ecsConTareas->count();

        // Métricas específicas por rol
        switch (strtolower($rol)) {
            case 'desarrollador':
                $metricas = array_merge($metricas, $this->getMetricasDesarrollador($proyecto, $usuarioId));
                break;
            case 'tester':
                $metricas = array_merge($metricas, $this->getMetricasTester($proyecto, $usuarioId));
                break;
            case 'analista':
                $metricas = array_merge($metricas, $this->getMetricasAnalista($proyecto, $usuarioId));
                break;
        }

        return $metricas;
    }

    /**
     * Métricas específicas para desarrollador
     */
    private function getMetricasDesarrollador(Proyecto $proyecto, string $usuarioId): array
    {
        $metodologia = strtolower($proyecto->metodologia->nombre ?? '');

        if ($metodologia === 'scrum') {
            return [
                'user_stories' => 0, // TODO: implementar cuando tengamos sprints
                'story_points' => 0,
                'sprint_completado' => 0,
            ];
        } elseif ($metodologia === 'cascada') {
            return [
                'modulos_asignados' => 0,
                'horas_estimadas' => 0,
                'entregables_completados' => 0,
            ];
        }

        return [];
    }

    /**
     * Métricas específicas para tester
     */
    private function getMetricasTester(Proyecto $proyecto, string $usuarioId): array
    {
        // Contar ECs en estado de testing asignados al usuario
        $ecsEnTesting = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->whereHas('tareas', function($query) use ($usuarioId) {
                $query->where('responsable', $usuarioId)
                      ->where('nombre', 'LIKE', '%test%');
            })
            ->get();

        return [
            'casos_prueba' => $ecsEnTesting->count(),
            'casos_pasados' => $ecsEnTesting->where('estado', 'LIBERADO')->count(),
            'casos_fallidos' => 0, // TODO: implementar cuando tengamos sistema de bugs
            'cobertura_testing' => $ecsEnTesting->count() > 0 ? round(($ecsEnTesting->where('estado', 'LIBERADO')->count() / $ecsEnTesting->count()) * 100, 1) : 0,
        ];
    }

    /**
     * Métricas específicas para analista
     */
    private function getMetricasAnalista(Proyecto $proyecto, string $usuarioId): array
    {
        // Contar ECs de documentación asignados al usuario
        $ecsDocumentacion = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->where('tipo', 'DOCUMENTO')
            ->whereHas('tareas', function($query) use ($usuarioId) {
                $query->where('responsable', $usuarioId);
            })
            ->get();

        return [
            'documentos_asignados' => $ecsDocumentacion->count(),
            'documentos_aprobados' => $ecsDocumentacion->where('estado', 'APROBADO')->count(),
            'requisitos_en_revision' => $ecsDocumentacion->where('estado', 'EN_REVISION')->count(),
            'criterios_definidos' => $ecsDocumentacion->where('estado', 'APROBADO')->count(), // Por ahora, los documentos aprobados tienen criterios definidos
        ];
    }

    /**
     * Obtener ECs asignados al usuario
     */
    private function getECsAsignados(Proyecto $proyecto, string $usuarioId): Collection
    {
        return ElementoConfiguracion::where('proyecto_id', $proyecto->id)
            ->whereHas('tareas', function($query) use ($usuarioId) {
                $query->where('responsable', $usuarioId);
            })
            ->with(['versionActual', 'tareas' => function($query) use ($usuarioId) {
                $query->where('responsable', $usuarioId);
            }])
            ->get();
    }

    /**
     * Obtener TODAS las tareas del usuario (incluyendo completadas)
     */
    private function getTodasLasTareasUsuario(Proyecto $proyecto, string $usuarioId): Collection
    {
        return TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('responsable', $usuarioId)
            ->with(['elementoConfiguracion', 'fase', 'responsableUsuario'])
            ->orderByRaw("CASE 
                WHEN estado IN ('Pendiente', 'PENDIENTE') THEN 1
                WHEN estado IN ('En Progreso', 'EN_PROGRESO', 'EN_DESARROLLO') THEN 2
                WHEN estado IN ('Completado', 'COMPLETADA') THEN 3
                ELSE 4
            END")
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_fin', 'asc')
            ->get();
    }

    /**
     * Obtener tareas activas del usuario (sin completadas)
     */
    private function getTareasActivas(Proyecto $proyecto, string $usuarioId): Collection
    {
        return TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('responsable', $usuarioId)
            ->whereNotIn('estado', ['COMPLETADA', 'CANCELADA', 'Completado', 'Cancelado'])
            ->with(['elementoConfiguracion', 'fase'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_fin', 'asc')
            ->get();
    }

    /**
     * Obtener cambios relacionados con ECs del usuario
     */
    private function getCambiosRelacionados(Proyecto $proyecto, string $usuarioId): Collection
    {
        // Obtener IDs de ECs donde el usuario tiene tareas
        $ecsUsuario = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('responsable', $usuarioId)
            ->pluck('id_ec')
            ->filter();

        // Si no tiene ECs asignados, retornar colección vacía
        if ($ecsUsuario->isEmpty()) {
            return collect();
        }

        return SolicitudCambio::where('proyecto_id', $proyecto->id)
            ->whereHas('items', function($query) use ($ecsUsuario) {
                $query->whereIn('ec_id', $ecsUsuario);
            })
            ->with(['items.elementoConfiguracion'])
            ->orderBy('creado_en', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Datos específicos para Scrum
     */
    private function getSprintData(Proyecto $proyecto, string $usuarioId): array
    {
        // TODO: Implementar cuando tengamos modelo Sprint completo
        return [
            'sprint_actual' => 'Sprint #1',
            'dias_restantes' => 10,
            'story_points_completados' => 0,
            'story_points_total' => 0,
            'impedimentos' => [],
        ];
    }

    /**
     * Datos específicos para Cascada
     */
    private function getFaseData(Proyecto $proyecto, string $usuarioId): array
    {
        // Obtener fase actual (primera fase con tareas no completadas)
        $faseActual = \App\Models\FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
            ->whereHas('tareas', function($query) use ($proyecto) {
                $query->where('id_proyecto', $proyecto->id)
                      ->whereNotIn('estado', ['COMPLETADA']);
            })
            ->orderBy('orden')
            ->first();

        return [
            'fase_actual' => $faseActual->nombre ?? 'Planificación',
            'progreso_fase' => 0, // TODO: calcular progreso real
            'dias_restantes' => 30, // TODO: calcular días reales
            'entregables_pendientes' => 0,
            'hitos_proximos' => [],
        ];
    }

    /**
     * Obtener estado de EC con color para badges
     */
    public function getColorEstadoEC(string $estado): string
    {
        return match($estado) {
            'PENDIENTE' => 'bg-gray-100 text-gray-800',
            'BORRADOR' => 'bg-yellow-100 text-yellow-800',
            'EN_REVISION' => 'bg-blue-100 text-blue-800',
            'APROBADO' => 'bg-green-100 text-green-800',
            'LIBERADO' => 'bg-emerald-100 text-emerald-800',
            'OBSOLETO' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
