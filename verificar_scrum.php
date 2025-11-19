<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REVISIÓN COMPLETA DEL FLUJO SCRUM ===\n\n";

$proyecto_id = '41999ad6-7656-4688-85c7-6f2444681de3';

// 1. Verificar Sprints
echo "1. SPRINTS DEL PROYECTO:\n";
$sprints = DB::table('sprints')
    ->where('id_proyecto', $proyecto_id)
    ->orderBy('id_sprint')
    ->get();

foreach ($sprints as $sprint) {
    $tareas_count = DB::table('tareas_proyecto')
        ->where('id_sprint', $sprint->id_sprint)
        ->count();

    echo "   Sprint {$sprint->id_sprint}: {$sprint->nombre}\n";
    echo "   - Estado: {$sprint->estado}\n";
    echo "   - Tareas asignadas: {$tareas_count}\n";
    echo "   - Fechas: {$sprint->fecha_inicio} - {$sprint->fecha_fin}\n\n";
}

// 2. Verificar Product Backlog
echo "\n2. PRODUCT BACKLOG (Tareas sin sprint):\n";
$backlog = DB::table('tareas_proyecto')
    ->where('id_proyecto', $proyecto_id)
    ->whereNull('id_sprint')
    ->get();

echo "   Total en Product Backlog: " . $backlog->count() . "\n";
foreach ($backlog as $tarea) {
    echo "   - {$tarea->nombre} (Prioridad: {$tarea->prioridad})\n";
}

// 3. Verificar tareas por sprint
echo "\n3. TAREAS POR SPRINT:\n";
foreach ($sprints as $sprint) {
    $tareas = DB::table('tareas_proyecto')
        ->where('id_sprint', $sprint->id_sprint)
        ->get();

    if ($tareas->count() > 0) {
        echo "   {$sprint->nombre}:\n";
        foreach ($tareas as $tarea) {
            $fase = DB::table('fases_metodologia')->where('id_fase', $tarea->id_fase)->first();
            $ec = $tarea->id_ec ? DB::table('elementos_configuracion')->where('id', $tarea->id_ec)->first() : null;

            echo "      - {$tarea->nombre}\n";
            echo "        Fase: " . ($fase->nombre_fase ?? 'N/A') . "\n";
            echo "        EC: " . ($ec->titulo ?? 'Sin vincular') . "\n";
            echo "        Story Points: " . ($tarea->story_points ?? 'N/A') . "\n";
            echo "        Prioridad: {$tarea->prioridad}\n\n";
        }
    }
}

// 4. Verificar sprint activo
echo "\n4. SPRINT ACTIVO:\n";
$sprint_activo = DB::table('sprints')
    ->where('id_proyecto', $proyecto_id)
    ->where('estado', 'activo')
    ->first();

if ($sprint_activo) {
    echo "   Sprint activo: {$sprint_activo->nombre}\n";
    $tareas_activo = DB::table('tareas_proyecto')
        ->where('id_sprint', $sprint_activo->id_sprint)
        ->count();
    echo "   Tareas en sprint activo: {$tareas_activo}\n";
} else {
    echo "   ⚠️  NO HAY SPRINT ACTIVO\n";
}

// 5. Verificar relaciones
echo "\n5. VERIFICAR RELACIONES (sample):\n";
$sample_tarea = DB::table('tareas_proyecto')
    ->where('id_proyecto', $proyecto_id)
    ->first();

if ($sample_tarea) {
    echo "   Tarea ejemplo: {$sample_tarea->nombre}\n";
    echo "   - id_proyecto: " . ($sample_tarea->id_proyecto ? '✅' : '❌') . "\n";
    echo "   - id_fase: " . ($sample_tarea->id_fase ? '✅' : '❌') . "\n";
    echo "   - id_sprint: " . ($sample_tarea->id_sprint ? '✅' : '❌') . "\n";
    echo "   - id_ec: " . ($sample_tarea->id_ec ? '✅' : '❌') . "\n";
    echo "   - responsable: " . ($sample_tarea->responsable ? '✅' : '❌') . "\n";
}

echo "\n=== RESUMEN ===\n";
echo "Total sprints: " . $sprints->count() . "\n";
echo "Total tareas en proyecto: " . DB::table('tareas_proyecto')->where('id_proyecto', $proyecto_id)->count() . "\n";
echo "Product Backlog: " . $backlog->count() . "\n";
echo "Sprint activo: " . ($sprint_activo ? $sprint_activo->nombre : 'NINGUNO') . "\n";
