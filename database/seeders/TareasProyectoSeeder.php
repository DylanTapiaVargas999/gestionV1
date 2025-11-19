<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TareasProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $scrumProject = DB::table('proyectos')->where('codigo', 'SCRUM-DEMO')->first();
        $cascadaProject = DB::table('proyectos')->where('codigo', 'CASCADA-DEMO')->first();
        if (!$scrumProject || !$cascadaProject) return;

        // --- PROYECTO SCRUM: TAREAS ---
        $scrumFases = DB::table('fases_metodologia')->where('id_metodologia', $scrumProject->id_metodologia)->orderBy('orden')->get();
        $scrumECs = DB::table('elementos_configuracion')->where('proyecto_id', $scrumProject->id)->pluck('id')->toArray();
        $scrumMiembros = DB::table('miembros_equipo')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->where('equipos.proyecto_id', $scrumProject->id)
            ->pluck('miembros_equipo.usuario_id')->toArray();

        $scrumTareas = [
            ['nombre' => 'Crear Product Backlog', 'fase_orden' => 1, 'story_points' => 2, 'horas' => 4, 'prioridad' => 3, 'dias' => 1],
            ['nombre' => 'Sprint Planning - Sprint 1', 'fase_orden' => 2, 'story_points' => 3, 'horas' => 6, 'prioridad' => 3, 'dias' => 1],
            ['nombre' => 'Implementar API Auth', 'fase_orden' => 3, 'story_points' => 8, 'horas' => 20, 'prioridad' => 3, 'dias' => 5],
            ['nombre' => 'Diseñar UI Login', 'fase_orden' => 3, 'story_points' => 5, 'horas' => 12, 'prioridad' => 2, 'dias' => 3],
            ['nombre' => 'Code Review - Auth Module', 'fase_orden' => 4, 'story_points' => 2, 'horas' => 4, 'prioridad' => 2, 'dias' => 1],
            ['nombre' => 'Deploy Sprint 1', 'fase_orden' => 5, 'story_points' => 3, 'horas' => 6, 'prioridad' => 3, 'dias' => 1],
        ];

        $fechaScrum = Carbon::now();
        foreach ($scrumTareas as $idx => $tarea) {
            $fase = $scrumFases->where('orden', $tarea['fase_orden'])->first();
            if (!$fase) continue;

            $fechaInicio = $fechaScrum->copy();
            $fechaFin = $fechaScrum->copy()->addDays($tarea['dias']);

            DB::table('tareas_proyecto')->insert([
                'id_proyecto' => $scrumProject->id,
                'id_fase' => $fase->id_fase,
                'id_ec' => !empty($scrumECs) ? $scrumECs[array_rand($scrumECs)] : null,
                'nombre' => $tarea['nombre'],
                'descripcion' => 'Tarea del proyecto Scrum: ' . $tarea['nombre'],
                'story_points' => $tarea['story_points'],
                'horas_estimadas' => $tarea['horas'],
                'responsable' => !empty($scrumMiembros) ? $scrumMiembros[array_rand($scrumMiembros)] : null,
                'estado' => 'pendiente',
                'prioridad' => $tarea['prioridad'],
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'sprint' => 'Sprint 1',
                'notas' => 'Generado automáticamente',
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]);

            $fechaScrum = $fechaFin->copy()->addDay();
        }

        // --- PROYECTO CASCADA: TAREAS ---
        $cascadaFases = DB::table('fases_metodologia')->where('id_metodologia', $cascadaProject->id_metodologia)->orderBy('orden')->get();
        $cascadaECs = DB::table('elementos_configuracion')->where('proyecto_id', $cascadaProject->id)->pluck('id')->toArray();
        $cascadaMiembros = DB::table('miembros_equipo')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->where('equipos.proyecto_id', $cascadaProject->id)
            ->pluck('miembros_equipo.usuario_id')->toArray();

        $cascadaTareas = [
            ['nombre' => 'Levantamiento de Requisitos', 'fase_orden' => 1, 'story_points' => 5, 'horas' => 16, 'prioridad' => 3, 'dias' => 3],
            ['nombre' => 'Análisis de Viabilidad', 'fase_orden' => 2, 'story_points' => 3, 'horas' => 8, 'prioridad' => 2, 'dias' => 2],
            ['nombre' => 'Diseño de Arquitectura', 'fase_orden' => 3, 'story_points' => 8, 'horas' => 20, 'prioridad' => 3, 'dias' => 5],
            ['nombre' => 'Diseño de Base de Datos', 'fase_orden' => 3, 'story_points' => 5, 'horas' => 12, 'prioridad' => 3, 'dias' => 3],
            ['nombre' => 'Desarrollo Módulo Core', 'fase_orden' => 4, 'story_points' => 13, 'horas' => 40, 'prioridad' => 3, 'dias' => 10],
            ['nombre' => 'Pruebas de Integración', 'fase_orden' => 5, 'story_points' => 5, 'horas' => 16, 'prioridad' => 2, 'dias' => 4],
            ['nombre' => 'Deploy a Producción', 'fase_orden' => 6, 'story_points' => 3, 'horas' => 8, 'prioridad' => 3, 'dias' => 2],
            ['nombre' => 'Soporte Post-Despliegue', 'fase_orden' => 7, 'story_points' => 2, 'horas' => 8, 'prioridad' => 1, 'dias' => 3],
        ];

        $fechaCascada = Carbon::now();
        foreach ($cascadaTareas as $idx => $tarea) {
            $fase = $cascadaFases->where('orden', $tarea['fase_orden'])->first();
            if (!$fase) continue;

            $fechaInicio = $fechaCascada->copy();
            $fechaFin = $fechaCascada->copy()->addDays($tarea['dias']);

            DB::table('tareas_proyecto')->insert([
                'id_proyecto' => $cascadaProject->id,
                'id_fase' => $fase->id_fase,
                'id_ec' => !empty($cascadaECs) ? $cascadaECs[array_rand($cascadaECs)] : null,
                'nombre' => $tarea['nombre'],
                'descripcion' => 'Tarea del proyecto Cascada: ' . $tarea['nombre'],
                'story_points' => $tarea['story_points'],
                'horas_estimadas' => $tarea['horas'],
                'responsable' => !empty($cascadaMiembros) ? $cascadaMiembros[array_rand($cascadaMiembros)] : null,
                'estado' => 'pendiente',
                'prioridad' => $tarea['prioridad'],
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'sprint' => null,
                'notas' => 'Generado automáticamente',
                'creado_en' => now(),
                'actualizado_en' => now(),
            ]);

            $fechaCascada = $fechaFin->copy()->addDay();
        }

        $this->command->info('✅ Tareas creadas para proyectos Scrum y Cascada');
    }
}
