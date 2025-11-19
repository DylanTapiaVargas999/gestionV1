<?php

/**
 * Script de diagnóstico para Product Backlog
 * Ejecutar: php debug_product_backlog.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proyecto;
use App\Models\TareaProyecto;

echo "\n🔍 DIAGNÓSTICO DE PRODUCT BACKLOG\n";
echo str_repeat("=", 60) . "\n\n";

// Buscar proyecto con metodología Scrum
$proyecto = Proyecto::with('metodologia')->get()->first(function ($p) {
    return $p->metodologia && strtolower($p->metodologia->nombre) === 'scrum';
});

if (!$proyecto) {
    echo "❌ No se encontró ningún proyecto con metodología Scrum\n";

    $proyectos = Proyecto::with('metodologia')->get();
    echo "\n📊 Proyectos disponibles:\n";
    foreach ($proyectos as $p) {
        echo "  - {$p->nombre} (ID: {$p->id}) - Metodología: " . ($p->metodologia->nombre ?? 'Sin metodología') . "\n";
    }
    exit(1);
}

echo "✅ Proyecto encontrado: {$proyecto->nombre} (ID: {$proyecto->id})\n";
echo "   Metodología: {$proyecto->metodologia->nombre}\n\n";

// Product Backlog (sin sprint asignado)
$productBacklog = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNull('id_sprint')
    ->get();

echo "📦 PRODUCT BACKLOG (id_sprint IS NULL):\n";
echo str_repeat("-", 60) . "\n";
if ($productBacklog->isEmpty()) {
    echo "   ⚠️  VACÍO - No hay user stories en el Product Backlog\n\n";
} else {
    echo "   Total: {$productBacklog->count()} user stories\n\n";
    foreach ($productBacklog as $story) {
        echo "   • {$story->nombre}\n";
        echo "     ID: {$story->id_tarea}\n";
        echo "     Story Points: " . ($story->story_points ?? 'NULL') . "\n";
        echo "     Estado: {$story->estado}\n";
        echo "     Sprint: " . ($story->id_sprint ?? 'NULL (Product Backlog)') . "\n";
        echo "\n";
    }
}

// Tareas con sprint asignado
$tareasConSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('id_sprint')
    ->with('sprint')
    ->get();

echo "🚀 TAREAS EN SPRINTS (id_sprint NOT NULL):\n";
echo str_repeat("-", 60) . "\n";
if ($tareasConSprint->isEmpty()) {
    echo "   ℹ️  No hay tareas asignadas a sprints\n\n";
} else {
    echo "   Total: {$tareasConSprint->count()} tareas\n\n";
    foreach ($tareasConSprint as $tarea) {
        echo "   • {$tarea->nombre}\n";
        echo "     ID: {$tarea->id_tarea}\n";
        echo "     Story Points: " . ($tarea->story_points ?? 'NULL') . "\n";
        echo "     Sprint: {$tarea->sprint->nombre} (ID: {$tarea->id_sprint})\n";
        echo "     Estado: {$tarea->estado}\n";
        echo "\n";
    }
}

// Todas las tareas del proyecto
$todasLasTareas = TareaProyecto::where('id_proyecto', $proyecto->id)->get();

echo "📊 RESUMEN GENERAL:\n";
echo str_repeat("-", 60) . "\n";
echo "   Total tareas del proyecto: {$todasLasTareas->count()}\n";
echo "   - En Product Backlog (sin sprint): {$productBacklog->count()}\n";
echo "   - Asignadas a sprints: {$tareasConSprint->count()}\n";
echo "   - Con story_points (Scrum): " . $todasLasTareas->whereNotNull('story_points')->count() . "\n";
echo "   - Con horas_estimadas (Cascada): " . $todasLasTareas->whereNotNull('horas_estimadas')->count() . "\n";
echo "\n";

// Verificar si hay tareas sin clasificar correctamente
$tareasSinMetodologia = $todasLasTareas->filter(function ($t) {
    return is_null($t->story_points) && is_null($t->horas_estimadas);
});

if ($tareasSinMetodologia->isNotEmpty()) {
    echo "⚠️  ADVERTENCIA: {$tareasSinMetodologia->count()} tareas sin story_points ni horas_estimadas\n";
    echo "   Estas tareas no están clasificadas como Scrum ni Cascada:\n\n";
    foreach ($tareasSinMetodologia as $tarea) {
        echo "   • {$tarea->nombre} (ID: {$tarea->id_tarea})\n";
        echo "     Estado: {$tarea->estado}\n";
        echo "     Sprint: " . ($tarea->id_sprint ?? 'NULL') . "\n";
        echo "\n";
    }
}

echo "\n✅ Diagnóstico completado\n\n";

// Sugerencias
if ($productBacklog->isEmpty() && $todasLasTareas->isNotEmpty()) {
    echo "💡 SUGERENCIAS:\n";
    echo str_repeat("-", 60) . "\n";

    if ($tareasConSprint->isNotEmpty()) {
        echo "   Las tareas están asignadas a sprints.\n";
        echo "   Para verlas en Product Backlog, debes:\n";
        echo "   1. Ir al sprint donde están\n";
        echo "   2. Removerlas del sprint\n";
        echo "   3. Regresarán al Product Backlog\n\n";
    }

    if ($tareasSinMetodologia->isNotEmpty()) {
        echo "   Hay tareas sin story_points.\n";
        echo "   Para convertirlas en User Stories de Scrum:\n";
        echo "   1. Edita cada tarea\n";
        echo "   2. Asigna story_points (1, 2, 3, 5, 8, 13, 21)\n";
        echo "   3. Asegúrate de que id_sprint sea NULL\n\n";
    }

    if ($productBacklog->isEmpty() && $tareasConSprint->isEmpty()) {
        echo "   No tienes tareas en este proyecto.\n";
        echo "   Crea nuevas User Stories desde Sprint Planning:\n";
        echo "   1. Haz clic en '+ Nueva User Story'\n";
        echo "   2. Asigna story_points\n";
        echo "   3. No selecciones ningún sprint (déjalo vacío)\n\n";
    }
}
