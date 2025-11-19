<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\TareaProyecto;

echo "🔍 VERIFICACIÓN DE SCRUM EN BD\n";
echo "================================\n\n";

$proyecto = Proyecto::where('codigo', 'ECOM-2024')->first();

if (!$proyecto) {
    echo "❌ Proyecto ECOM-2024 no encontrado\n";
    exit(1);
}

echo "📦 Proyecto: {$proyecto->nombre}\n";
echo "📋 Metodología: {$proyecto->metodologia->nombre}\n\n";

// Verificar Sprints
$sprints = $proyecto->sprints()->orderBy('fecha_inicio')->get();
echo "🏃 SPRINTS ({$sprints->count()})\n";
echo "-------------------\n";

foreach ($sprints as $sprint) {
    $tareas = $sprint->tareas;
    $storyPoints = $tareas->sum('story_points');
    echo "  {$sprint->nombre} (ID: {$sprint->id_sprint})\n";
    echo "    - Estado: {$sprint->estado}\n";
    echo "    - Tareas: {$tareas->count()}\n";
    echo "    - Story Points: {$storyPoints}\n";
    echo "    - Fechas: {$sprint->fecha_inicio} → {$sprint->fecha_fin}\n\n";
}

// Verificar tareas sin sprint (Product Backlog)
$sinSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNull('id_sprint')
    ->get();

echo "📋 PRODUCT BACKLOG ({$sinSprint->count()} tareas sin sprint)\n";
echo "-------------------\n";
foreach ($sinSprint as $tarea) {
    echo "  - {$tarea->nombre} ({$tarea->story_points} pts)\n";
}
echo "\n";

// Verificar relación con ECs
echo "🔗 RELACIÓN TAREAS → ELEMENTOS CONFIGURACIÓN\n";
echo "-------------------\n";
$tareasConEC = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('id_ec')
    ->with('elementoConfiguracion')
    ->get();

echo "Tareas con EC: {$tareasConEC->count()}\n";
foreach ($tareasConEC as $tarea) {
    $ec = $tarea->elementoConfiguracion;
    echo "  - {$tarea->nombre} → " . ($ec ? $ec->codigo_ec : 'EC no encontrado') . "\n";
}

echo "\n✅ Verificación completada\n";
