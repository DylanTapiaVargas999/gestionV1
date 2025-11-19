<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\Sprint;

echo "=== VERIFICACIÓN SPRINT 3 ===\n\n";

$proyecto = Proyecto::where('nombre', 'E-Commerce Platform')->first();

if (!$proyecto) {
    echo "❌ Proyecto no encontrado\n";
    exit(1);
}

echo "✅ Proyecto: {$proyecto->nombre} (ID: {$proyecto->id})\n\n";

// Obtener sprints
$sprints = $proyecto->sprints;
echo "📊 Total sprints: " . $sprints->count() . "\n";
foreach ($sprints as $s) {
    echo "  - {$s->nombre} (ID: {$s->id_sprint}) - Estado: {$s->estado}\n";
}

// Obtener tareas
echo "\n=== TAREAS DEL PROYECTO ===\n";
$tareas = TareaProyecto::where('id_proyecto', $proyecto->id)->get();
echo "📋 Total tareas: " . $tareas->count() . "\n\n";

$tareasPorSprint = [];
foreach ($tareas as $t) {
    $sprintNombre = $t->id_sprint ? ($t->sprint ? $t->sprint->nombre : "Sprint ID {$t->id_sprint} (no encontrado)") : 'Sin Sprint';

    if (!isset($tareasPorSprint[$sprintNombre])) {
        $tareasPorSprint[$sprintNombre] = [];
    }
    $tareasPorSprint[$sprintNombre][] = $t;

    echo "Tarea #{$t->id_tarea}: {$t->nombre}\n";
    echo "  - id_sprint: " . ($t->id_sprint ?? 'NULL') . "\n";
    echo "  - Sprint obj: " . ($t->sprint ? $t->sprint->nombre : 'NULL') . "\n";
    echo "  - Fase: " . ($t->fase ? $t->fase->nombre_fase : 'NULL') . "\n";
    echo "\n";
}

echo "\n=== RESUMEN POR SPRINT ===\n";
foreach ($tareasPorSprint as $sprint => $tareasArray) {
    echo "$sprint: " . count($tareasArray) . " tareas\n";
}

// Verificar Sprint 3 específicamente
echo "\n=== SPRINT 3 DETALLE ===\n";
$sprint3 = Sprint::where('nombre', 'Sprint 3')
    ->where('id_proyecto', $proyecto->id)
    ->first();

if ($sprint3) {
    echo "✅ Sprint 3 encontrado (ID: {$sprint3->id_sprint})\n";
    echo "Estado: {$sprint3->estado}\n";
    echo "Fecha inicio: {$sprint3->fecha_inicio}\n";
    echo "Fecha fin: {$sprint3->fecha_fin}\n";

    // Tareas directamente desde la BD
    $tareasSprint3 = TareaProyecto::where('id_sprint', $sprint3->id_sprint)->get();
    echo "\n📊 Tareas con id_sprint = {$sprint3->id_sprint}: " . $tareasSprint3->count() . "\n";

    foreach ($tareasSprint3 as $t) {
        echo "  - {$t->nombre} (Fase: " . ($t->fase ? $t->fase->nombre_fase : 'N/A') . ")\n";
    }

    // Tareas desde relación
    $tareasRelacion = $sprint3->userStories;
    echo "\n📊 Tareas desde relación userStories(): " . $tareasRelacion->count() . "\n";
} else {
    echo "❌ Sprint 3 no encontrado en la BD\n";
}
