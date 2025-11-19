<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TareaProyecto;

echo "=== STORY POINTS DE TAREAS SPRINT 3 ===\n\n";

$tareas = TareaProyecto::where('id_sprint', 3)->get();

echo "Total tareas: " . $tareas->count() . "\n\n";

foreach ($tareas as $t) {
    echo "{$t->nombre}\n";
    echo "  - Story Points: " . ($t->story_points ?? 'NULL') . "\n";
    echo "  - Prioridad: " . ($t->prioridad ?? 'NULL') . "\n";
    echo "  - Fase: " . ($t->fase ? $t->fase->nombre_fase : 'NULL') . "\n\n";
}

$total = $tareas->sum('story_points');
echo "TOTAL STORY POINTS: $total\n";
