<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TareaProyecto;
use App\Models\Sprint;
use Illuminate\Support\Facades\DB;

echo "=== ANÁLISIS DETALLADO RELACIÓN SPRINT ===\n\n";

// Obtener una tarea con id_sprint
$tarea = TareaProyecto::whereNotNull('id_sprint')->first();
echo "Tarea: {$tarea->nombre}\n";
echo "  id_sprint (FK): {$tarea->id_sprint}\n";
echo "  Tipo: " . gettype($tarea->id_sprint) . "\n\n";

// Obtener sprint correspondiente
$sprint = Sprint::find($tarea->id_sprint);
if ($sprint) {
    echo "Sprint encontrado con find({$tarea->id_sprint}):\n";
    echo "  id_sprint: {$sprint->id_sprint} (tipo: " . gettype($sprint->id_sprint) . ")\n";
    echo "  nombre: {$sprint->nombre}\n\n";
} else {
    echo "❌ NO se encontró Sprint con find({$tarea->id_sprint})\n\n";
}

// Verificar query SQL de la relación
echo "SQL de la relación sprint():\n";
$query = $tarea->sprint()->toSql();
$bindings = $tarea->sprint()->getBindings();
echo "  SQL: {$query}\n";
echo "  Bindings: " . json_encode($bindings) . "\n\n";

// Ejecutar la query manualmente
$sprintRelacion = $tarea->sprint;
if ($sprintRelacion) {
    echo "✅ Relación sprint() funcionó:\n";
    echo "  Sprint: {$sprintRelacion->nombre}\n";
} else {
    echo "❌ Relación sprint() retorna NULL\n";

    // Query manual para comparar
    $sprintManual = DB::table('sprints')->where('id_sprint', $tarea->id_sprint)->first();
    if ($sprintManual) {
        echo "\n✅ Query manual SÍ encontró el sprint:\n";
        echo "  " . json_encode($sprintManual) . "\n";
    }
}
