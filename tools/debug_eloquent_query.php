<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TareaProyecto;
use App\Models\Sprint;
use Illuminate\Support\Facades\DB;

$tarea = TareaProyecto::whereNotNull('id_sprint')->first();

echo "Tarea: {$tarea->nombre}\n";
echo "  id_sprint: {$tarea->id_sprint}\n\n";

// Query manual
echo "=== QUERY MANUAL ===\n";
$resultManual = DB::table('sprints')->where('id_sprint', $tarea->id_sprint)->first();
var_dump($resultManual);

// Relación
echo "\n=== RELACIÓN ELOQUENT ===\n";
$relation = $tarea->sprint();
echo "SQL: {$relation->toSql()}\n";
echo "Bindings: " . json_encode($relation->getBindings()) . "\n";

$resultado = $relation->first();
var_dump($resultado);

echo "\n=== PRUEBA CON FIND ===\n";
$sprintFind = Sprint::find($tarea->id_sprint);
if ($sprintFind) {
    echo "✅ Sprint::find({$tarea->id_sprint}) funciona!\n";
    echo "  Nombre: {$sprintFind->nombre}\n";
    echo "  Attributes: " . json_encode($sprintFind->getAttributes()) . "\n";
} else {
    echo "❌ Sprint::find retorna NULL\n";
}
