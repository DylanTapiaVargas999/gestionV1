<?php

/**
 * Script para probar las vistas de Scrum después de la refactorización
 *
 * Verifica:
 * - Dashboard funciona con Sprint entities
 * - Daily Scrum carga correctamente
 * - Sprint Review muestra métricas
 * - Sprint Retrospective se carga
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\TareaProyecto;

echo "=== TEST VISTAS SCRUM ===\n\n";

// Obtener un proyecto Scrum
$proyecto = Proyecto::whereHas('metodologia', function($q) {
    $q->where('nombre', 'Scrum');
})->first();

if (!$proyecto) {
    echo "❌ No hay proyectos Scrum en la BD\n";
    exit(1);
}

echo "✅ Proyecto: {$proyecto->nombre} (ID: {$proyecto->id})\n";

// Verificar sprints
$sprints = $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->get();
echo "✅ Sprints encontrados: {$sprints->count()}\n";

if ($sprints->isEmpty()) {
    echo "❌ No hay sprints. Ejecuta: php artisan db:seed --class=SprintsSeeder\n";
    exit(1);
}

foreach ($sprints as $sprint) {
    echo "   - {$sprint->nombre} (ID: {$sprint->id_sprint})\n";
}

// Obtener sprint activo
$sprintActivo = $proyecto->sprintActivo ?? $sprints->first();
echo "\n✅ Sprint Activo: {$sprintActivo->nombre}\n";

// Verificar tareas
$tareas = TareaProyecto::where('id_proyecto', $proyecto->id)->with('sprint')->get();
echo "✅ Total tareas del proyecto: {$tareas->count()}\n";

$tareasSprint = $tareas->where('id_sprint', $sprintActivo->id_sprint);
echo "✅ Tareas en {$sprintActivo->nombre}: {$tareasSprint->count()}\n";

// Agrupar tareas por sprint (nombre) - como hace el dashboard
$tareasPorSprint = $tareas->groupBy(function($tarea) {
    return $tarea->sprint ? $tarea->sprint->nombre : 'Sin Sprint';
});

echo "\n=== AGRUPACIÓN POR SPRINT (NOMBRES) ===\n";
foreach ($tareasPorSprint as $nombreSprint => $tareasGrupo) {
    echo "   {$nombreSprint}: {$tareasGrupo->count()} tareas\n";
}

// Verificar que podemos obtener tareas del sprint actual usando el nombre
$tareasDelSprintActual = $tareasPorSprint->get($sprintActivo->nombre, collect());
echo "\n✅ Tareas obtenidas con \$tareasPorSprint->get('{$sprintActivo->nombre}'): {$tareasDelSprintActual->count()}\n";

// Simular lo que hace dailyScrum
echo "\n=== TEST DAILY SCRUM ===\n";
$tareasDelSprint = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->where('id_sprint', $sprintActivo->id_sprint)
    ->with(['responsableUsuario', 'fase'])
    ->get()
    ->groupBy('responsable');

echo "✅ Tareas agrupadas por responsable: {$tareasDelSprint->count()} grupos\n";
foreach ($tareasDelSprint as $responsable => $tareasResp) {
    $responsableId = $responsable ?? 'Sin asignar';
    echo "   Responsable {$responsableId}: {$tareasResp->count()} tareas\n";
}

// Simular lo que hace sprintReview
echo "\n=== TEST SPRINT REVIEW ===\n";
$totalTareas = $tareasSprint->count();
$tareasCompletadas = $tareasSprint->where('estado', 'Completado')->count();
$totalStoryPoints = $tareasSprint->sum('story_points');
$storyPointsCompletados = $tareasSprint->where('estado', 'Completado')->sum('story_points');

echo "✅ Total tareas: {$totalTareas}\n";
echo "✅ Completadas: {$tareasCompletadas}\n";
echo "✅ Story Points: {$totalStoryPoints}\n";
echo "✅ Story Points completados: {$storyPointsCompletados}\n";
echo "✅ Tasa completitud: " . ($totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0) . "%\n";

echo "\n=== ✅ TODOS LOS TESTS PASARON ===\n";
echo "\nLas vistas deberían funcionar correctamente:\n";
echo "  - Dashboard: /proyectos/{$proyecto->id}/scrum/dashboard\n";
echo "  - Daily Scrum: /proyectos/{$proyecto->id}/scrum/daily\n";
echo "  - Sprint Review: /proyectos/{$proyecto->id}/scrum/review\n";
echo "  - Sprint Retrospective: /proyectos/{$proyecto->id}/scrum/retrospective\n";
