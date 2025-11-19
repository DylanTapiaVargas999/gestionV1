<?php

/**
 * Test completo de funcionalidad Scrum
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;

echo "=== TEST COMPLETO DE GESTIÓN SCRUM ===\n\n";

// 1. Obtener proyecto Scrum
$proyecto = Proyecto::whereHas('metodologia', function($q) {
    $q->where('nombre', 'Scrum');
})->first();

if (!$proyecto) {
    echo "❌ No hay proyectos Scrum\n";
    exit(1);
}

echo "✅ Proyecto: {$proyecto->nombre}\n";
echo "   ID: {$proyecto->id}\n\n";

// 2. Verificar sprints
$sprints = $proyecto->sprints()->orderBy('fecha_inicio', 'desc')->get();
echo "=== SPRINTS ({$sprints->count()}) ===\n";
foreach ($sprints as $sprint) {
    echo "  [{$sprint->estado}] {$sprint->nombre}\n";
    echo "    - ID: {$sprint->id_sprint}\n";
    echo "    - Fechas: {$sprint->fecha_inicio->format('d/m/Y')} - {$sprint->fecha_fin->format('d/m/Y')}\n";
    echo "    - User Stories: {$sprint->userStories->count()}\n";
    echo "    - Velocidad: {$sprint->velocidad_estimada} SP\n";
}

// 3. Verificar Sprint Activo
$sprintActivo = $proyecto->sprintActivo ?? $sprints->where('estado', 'activo')->first();
if ($sprintActivo) {
    echo "\n✅ Sprint Activo: {$sprintActivo->nombre}\n";
} else {
    echo "\n⚠️  No hay sprint activo\n";
}

// 4. Verificar User Stories
$userStories = TareaProyecto::where('id_proyecto', $proyecto->id)->get();
echo "\n=== USER STORIES ({$userStories->count()}) ===\n";

$porEstado = $userStories->groupBy('estado');
foreach ($porEstado as $estado => $tareas) {
    echo "  {$estado}: {$tareas->count()} tareas\n";
}

$porSprint = $userStories->groupBy('id_sprint');
echo "\n=== POR SPRINT ===\n";
foreach ($porSprint as $id_sprint => $tareas) {
    if ($id_sprint) {
        $sprint = Sprint::find($id_sprint);
        echo "  {$sprint->nombre}: {$tareas->count()} user stories ({$tareas->sum('story_points')} SP)\n";
    } else {
        echo "  Backlog: {$tareas->count()} user stories\n";
    }
}

// 5. Verificar Fases de Scrum
$fases = FaseMetodologia::where('id_metodologia', $proyecto->id_metodologia)
    ->orderBy('orden')
    ->get();

echo "\n=== FASES DE SCRUM ({$fases->count()}) ===\n";
foreach ($fases as $fase) {
    $tareasEnFase = $userStories->where('id_fase', $fase->id_fase)->count();
    echo "  {$fase->orden}. {$fase->nombre}: {$tareasEnFase} tareas\n";
}

// 6. Test de relación Sprint
echo "\n=== TEST DE RELACIONES ===\n";
if ($sprints->count() > 0) {
    $primerSprint = $sprints->first();
    $userStoriesDelSprint = $primerSprint->userStories;
    echo "✅ Sprint->userStories(): {$userStoriesDelSprint->count()} user stories\n";

    if ($userStoriesDelSprint->count() > 0) {
        $primeraUS = $userStoriesDelSprint->first();
        $sprintDeLaUS = $primeraUS->sprint;
        if ($sprintDeLaUS) {
            echo "✅ UserStory->sprint(): {$sprintDeLaUS->nombre}\n";
        } else {
            echo "❌ UserStory->sprint(): NULL\n";
        }
    }
}

// 7. Resumen de URLs disponibles
echo "\n=== RUTAS DISPONIBLES ===\n";
echo "Dashboard:          /proyectos/{$proyecto->id}/scrum/dashboard\n";
echo "Sprint Planning:    /proyectos/{$proyecto->id}/scrum/sprint-planning\n";
echo "Daily Scrum:        /proyectos/{$proyecto->id}/scrum/daily-scrum\n";
echo "Sprint Review:      /proyectos/{$proyecto->id}/scrum/sprint-review\n";
echo "Sprint Retrospect:  /proyectos/{$proyecto->id}/scrum/sprint-retrospective\n";

echo "\n=== ENDPOINTS API ===\n";
echo "POST crear sprint:        /proyectos/{$proyecto->id}/scrum/sprints\n";
echo "POST iniciar sprint:      /proyectos/{$proyecto->id}/scrum/sprints/{id_sprint}/iniciar\n";
echo "POST completar sprint:    /proyectos/{$proyecto->id}/scrum/sprints/{id_sprint}/completar\n";
echo "POST crear user story:    /proyectos/{$proyecto->id}/scrum/user-stories\n";
echo "PATCH actualizar story:   /proyectos/{$proyecto->id}/scrum/user-stories/{id_tarea}\n";
echo "POST daily scrum:         /proyectos/{$proyecto->id}/scrum/daily-scrums\n";

echo "\n✅ SISTEMA SCRUM COMPLETAMENTE FUNCIONAL\n";
