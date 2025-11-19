<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sprint;
use App\Models\TareaProyecto;
use App\Models\Proyecto;

$proyecto = Proyecto::where('codigo', 'ECOM-2024')->first();

if (!$proyecto) {
    echo "Proyecto no encontrado\n";
    exit;
}

echo "Proyecto: {$proyecto->nombre}\n\n";

$sprints = Sprint::where('id_proyecto', $proyecto->id)->orderBy('nombre')->get();

foreach ($sprints as $sprint) {
    $tareas = TareaProyecto::where('id_sprint', $sprint->id_sprint)->get();
    echo "{$sprint->nombre}:\n";
    echo "  Estado: {$sprint->estado}\n";
    echo "  Total tareas: {$tareas->count()}\n";

    if ($tareas->count() > 0) {
        foreach ($tareas as $tarea) {
            echo "    - {$tarea->nombre}\n";
        }
    } else {
        echo "    (sin tareas)\n";
    }
    echo "\n";
}
