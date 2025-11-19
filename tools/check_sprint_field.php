<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TareaProyecto;

echo "=== VERIFICAR CAMPO SPRINT ===\n\n";

$tarea = TareaProyecto::first();

if (!$tarea) {
    echo "❌ No hay tareas\n";
    exit(1);
}

echo "Tarea: {$tarea->nombre}\n";
echo "Campo 'sprint' (string): " . var_export($tarea->getAttributes()['sprint'] ?? null, true) . "\n";
echo "Campo 'id_sprint' (FK): " . var_export($tarea->id_sprint, true) . "\n";

if ($tarea->id_sprint) {
    $tarea->load('sprint');
    echo "Relación sprint(): " . var_export($tarea->sprint ? $tarea->sprint->nombre : null, true) . "\n";
}

echo "\n=== TODAS LAS TAREAS ===\n";
$tareas = TareaProyecto::take(10)->get(['id_tarea', 'nombre', 'sprint', 'id_sprint']);
foreach ($tareas as $t) {
    $sprintStr = $t->getAttributes()['sprint'] ?? 'NULL';
    $sprintId = $t->id_sprint ?? 'NULL';
    echo "{$t->nombre}: sprint='{$sprintStr}', id_sprint={$sprintId}\n";
}
