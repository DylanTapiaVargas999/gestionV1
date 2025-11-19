<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TareaProyecto;
use App\Models\Sprint;

echo "=== DEBUG RELACIÓN SPRINT ===\n\n";

$sprint = Sprint::first();
if (!$sprint) {
    echo "❌ No hay sprints\n";
    exit(1);
}

echo "Sprint encontrado:\n";
echo "  id_sprint: {$sprint->id_sprint}\n";
echo "  nombre: {$sprint->nombre}\n";
echo "  id_proyecto: {$sprint->id_proyecto}\n\n";

$tarea = TareaProyecto::whereNotNull('id_sprint')->first();
if (!$tarea) {
    echo "❌ No hay tareas con id_sprint asignado\n";
    exit(1);
}

echo "Tarea encontrada:\n";
echo "  id_tarea: {$tarea->id_tarea}\n";
echo "  nombre: {$tarea->nombre}\n";
echo "  id_sprint (FK): {$tarea->id_sprint}\n\n";

echo "Intentando cargar relación sprint()...\n";
$tarea->load('sprint');

if ($tarea->sprint) {
    echo "✅ Relación cargada exitosamente:\n";
    echo "  Sprint: {$tarea->sprint->nombre}\n";
} else {
    echo "❌ Relación retorna NULL\n";

    // Debug manual
    echo "\nDebug manual:\n";
    $sprintManual = Sprint::where('id_sprint', $tarea->id_sprint)->first();
    if ($sprintManual) {
        echo "✅ Sprint existe en BD: {$sprintManual->nombre}\n";
    } else {
        echo "❌ Sprint NO existe en BD para id_sprint={$tarea->id_sprint}\n";
    }
}
