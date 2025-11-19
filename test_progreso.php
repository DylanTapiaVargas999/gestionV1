<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TareaProyecto;

echo "\n🧪 TEST DE PROGRESO\n";
echo str_repeat("=", 60) . "\n\n";

// Obtener las tareas del Sprint 5
$tareas = TareaProyecto::whereHas('sprint', function($q) {
    $q->where('nombre', 'Sprint 5');
})->get();

if ($tareas->isEmpty()) {
    echo "❌ No hay tareas en Sprint 5\n";
    exit(1);
}

echo "📋 Tareas del Sprint 5:\n\n";

foreach ($tareas as $tarea) {
    echo "• {$tarea->nombre}\n";
    echo "  Estado: {$tarea->estado}\n";
    echo "  Progreso (accessor): {$tarea->progreso}%\n";
    echo "  Progreso (atributo directo): " . ($tarea->attributes['progreso_real'] ?? 'NULL') . "\n";
    echo "\n";
}
