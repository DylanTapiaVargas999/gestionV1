<?php

/**
 * Script para asignar las user stories al Sprint 5
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\TareaProyecto;

echo "\n🔧 ASIGNAR USER STORIES AL SPRINT 5\n";
echo str_repeat("=", 60) . "\n\n";

// Buscar el proyecto E-Commerce
$proyecto = Proyecto::where('nombre', 'E-Commerce Platform')->first();

if (!$proyecto) {
    echo "❌ No se encontró el proyecto E-Commerce Platform\n";
    exit(1);
}

// Buscar Sprint 5
$sprint5 = Sprint::where('id_proyecto', $proyecto->id)
    ->where('nombre', 'Sprint 5')
    ->first();

if (!$sprint5) {
    echo "❌ No se encontró Sprint 5\n";
    exit(1);
}

echo "✅ Proyecto: {$proyecto->nombre}\n";
echo "✅ Sprint: {$sprint5->nombre} (Estado: {$sprint5->estado})\n\n";

// Buscar las 3 user stories del Product Backlog
$userStories = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNull('id_sprint')
    ->whereNotNull('story_points')
    ->get();

if ($userStories->isEmpty()) {
    echo "⚠️  No hay user stories en el Product Backlog para asignar\n";
    exit(0);
}

echo "📦 User Stories encontradas en Product Backlog:\n";
foreach ($userStories as $story) {
    echo "   • {$story->nombre} ({$story->story_points} SP)\n";
}

echo "\n¿Asignar estas " . $userStories->count() . " user stories al Sprint 5? (s/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) !== 's' && trim($line) !== 'S') {
    echo "❌ Operación cancelada\n";
    exit(0);
}

echo "\n🔄 Asignando user stories...\n";

$totalStoryPoints = 0;
foreach ($userStories as $story) {
    $story->id_sprint = $sprint5->id_sprint;
    $story->save();

    $totalStoryPoints += $story->story_points ?? 0;
    echo "   ✅ {$story->nombre} asignada\n";
}

// Actualizar velocidad del sprint
$sprint5->velocidad_estimada = $totalStoryPoints;
$sprint5->save();

echo "\n✅ COMPLETADO\n";
echo "   User Stories asignadas: {$userStories->count()}\n";
echo "   Story Points totales: {$totalStoryPoints}\n";
echo "   Sprint: {$sprint5->nombre}\n";
echo "\n🎉 Ahora puedes ver las user stories en el Sprint Board\n\n";
