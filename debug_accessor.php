<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TareaProyecto;

echo "\n🔍 DEBUG PROGRESO\n";
echo str_repeat("=", 60) . "\n\n";

$tarea = TareaProyecto::where('nombre', 'LIKE', 'US-011%')->first();

if (!$tarea) {
    echo "❌ No se encontró la tarea\n";
    exit(1);
}

echo "Tarea: {$tarea->nombre}\n";
echo "Estado en BD: '{$tarea->getRawOriginal('estado')}'\n";
echo "Estado via property: '{$tarea->estado}'\n";
echo "\n";

// Probar el mapeo manualmente
$mapeoProgreso = [
    'To Do' => 0,
    'Pendiente' => 0,
    'Product Backlog' => 0,
    'Sprint Planning' => 10,
    'In Progress' => 50,
    'En Progreso' => 50,
    'In Review' => 75,
    'En Revisión' => 75,
    'Testing' => 80,
    'Bloqueado' => 25,
];

$estado = $tarea->getRawOriginal('estado');
echo "¿Existe '{$estado}' en el mapeo? ";
echo isset($mapeoProgreso[$estado]) ? "SÍ" : "NO";
echo "\n";

if (isset($mapeoProgreso[$estado])) {
    echo "Valor del mapeo: {$mapeoProgreso[$estado]}%\n";
} else {
    echo "Valor por defecto: 0%\n";
}

echo "\nProgreso calculado por el accessor: {$tarea->progreso}%\n";
