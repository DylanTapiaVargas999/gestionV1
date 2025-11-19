<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE USER STORIES EN SPRINTS ===\n\n";

// Ver todas las tareas del proyecto
$proyectoId = '41999ad6-7656-4688-85c7-6f2444681de3';

echo "1. TODAS LAS TAREAS DEL PROYECTO:\n";
$tareas = DB::table('tareas_proyecto')
    ->where('id_proyecto', $proyectoId)
    ->select('id_tarea', 'nombre', 'id_sprint', 'id_fase', 'story_points')
    ->get();

foreach ($tareas as $tarea) {
    $sprintInfo = $tarea->id_sprint ? "Sprint: {$tarea->id_sprint}" : "Product Backlog";
    echo "- {$tarea->nombre} | {$sprintInfo} | Fase: {$tarea->id_fase} | SP: {$tarea->story_points}\n";
}

echo "\n2. TAREAS POR SPRINT:\n";
$sprintStats = DB::table('tareas_proyecto')
    ->join('sprints', 'tareas_proyecto.id_sprint', '=', 'sprints.id_sprint')
    ->where('tareas_proyecto.id_proyecto', $proyectoId)
    ->select('sprints.nombre as sprint_nombre', 'sprints.estado', DB::raw('COUNT(*) as total_tareas'))
    ->groupBy('sprints.id_sprint', 'sprints.nombre', 'sprints.estado')
    ->get();

foreach ($sprintStats as $stat) {
    echo "- {$stat->sprint_nombre} ({$stat->estado}): {$stat->total_tareas} tareas\n";
}

echo "\n3. PRODUCT BACKLOG (sin sprint):\n";
$backlog = DB::table('tareas_proyecto')
    ->where('id_proyecto', $proyectoId)
    ->whereNull('id_sprint')
    ->select('nombre', 'story_points')
    ->get();

echo "Total en Product Backlog: " . $backlog->count() . " tareas\n";
foreach ($backlog as $item) {
    echo "- {$item->nombre} | SP: {$item->story_points}\n";
}
