<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sprint;
use App\Models\TareaProyecto;

echo "=== ARREGLO Y TEST DE RELACIONES ===\n\n";

// Test 1: Sprint -> UserStories (hasMany)
$sprint = Sprint::where('id_sprint', 3)->first();
echo "Sprint: {$sprint->nombre}\n";
$userStories = $sprint->userStories;
echo "User Stories: {$userStories->count()}\n";

if ($userStories->count() > 0) {
    echo "✅ Sprint->userStories() funciona correctamente\n\n";

    // Test 2: TareaProyecto -> Sprint (belongsTo)
    $tarea = $userStories->first();
    echo "Tarea: {$tarea->nombre}\n";
    echo "  id_sprint en tarea: {$tarea->id_sprint}\n";

    $sprintDeTarea = $tarea->sprint;
    if ($sprintDeTarea) {
        echo "✅ Tarea->sprint() funciona: {$sprintDeTarea->nombre}\n";
    } else {
        echo "❌ Tarea->sprint() NO funciona\n";

        // Debug
        $relation = $tarea->sprint();
        echo "  Foreign Key: {$relation->getForeignKeyName()}\n";
        echo "  Owner Key: {$relation->getOwnerKeyName()}\n";
        echo "  Relacionado: " . get_class($relation->getRelated()) . "\n";
        echo "  Query SQL: {$relation->toSql()}\n";
    }
} else {
    echo "❌ Sprint->userStories() retorna 0 tareas\n";
}
