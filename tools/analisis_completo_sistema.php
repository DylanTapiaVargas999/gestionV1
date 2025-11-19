<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\Sprint;
use App\Models\ElementoConfiguracion;
use App\Models\FaseMetodologia;

echo "═══════════════════════════════════════════════════════════════\n";
echo "   ANÁLISIS COMPLETO DEL SISTEMA SCRUM + GESTIÓN DE CAMBIOS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. PROYECTO SCRUM
$proyecto = Proyecto::where('nombre', 'E-Commerce Platform')->first();
if (!$proyecto) {
    echo "❌ Proyecto no encontrado\n";
    exit(1);
}

echo "📦 PROYECTO: {$proyecto->nombre}\n";
echo "   - ID: {$proyecto->id}\n";
echo "   - Metodología: {$proyecto->metodologia->nombre}\n";
echo "   - Repositorio: {$proyecto->link_repositorio}\n\n";

// 2. SPRINTS
echo "🔄 SPRINTS:\n";
$sprints = Sprint::where('id_proyecto', $proyecto->id)->orderBy('fecha_inicio')->get();
foreach ($sprints as $sprint) {
    echo "   {$sprint->nombre} (ID: {$sprint->id_sprint})\n";
    echo "      Estado: {$sprint->estado}\n";
    echo "      Fechas: {$sprint->fecha_inicio} → {$sprint->fecha_fin}\n";
    echo "      Objetivo: {$sprint->objetivo}\n";
    echo "      Velocidad: {$sprint->velocidad_estimada} pts\n";

    // Contar tareas
    $numTareas = TareaProyecto::where('id_sprint', $sprint->id_sprint)->count();
    echo "      Tareas asignadas: {$numTareas}\n\n";
}

// 3. FASES DE SCRUM
echo "📋 FASES DE LA METODOLOGÍA:\n";
$fases = FaseMetodologia::where('id_metodologia', $proyecto->metodologia->id_metodologia)
    ->orderBy('orden')
    ->get();
foreach ($fases as $fase) {
    $numTareas = TareaProyecto::where('id_proyecto', $proyecto->id)
        ->where('id_fase', $fase->id_fase)
        ->count();
    echo "   {$fase->orden}. {$fase->nombre_fase} (ID: {$fase->id_fase}) - {$numTareas} tareas\n";
}

// 4. TAREAS (USER STORIES)
echo "\n\n📝 TAREAS (USER STORIES):\n";
$tareas = TareaProyecto::where('id_proyecto', $proyecto->id)->get();
echo "   Total: {$tareas->count()}\n\n";

foreach ($tareas as $tarea) {
    echo "   ┌─ Tarea #{$tarea->id_tarea}: {$tarea->nombre}\n";
    echo "   │  Fase: " . ($tarea->fase ? $tarea->fase->nombre_fase : 'NULL') . "\n";
    echo "   │  Sprint: id_sprint={$tarea->id_sprint}\n";

    $sprint = Sprint::find($tarea->id_sprint);
    echo "   │  Sprint objeto: " . ($sprint ? $sprint->nombre : 'NO ENCONTRADO') . "\n";

    echo "   │  Story Points: " . ($tarea->story_points ?? 'NULL') . "\n";
    echo "   │  Prioridad: " . ($tarea->prioridad ?? 'NULL') . "\n";
    echo "   │  Estado: " . ($tarea->estado ?? 'NULL') . "\n";
    echo "   │  Responsable: " . ($tarea->responsableUsuario ? $tarea->responsableUsuario->nombre : 'NULL') . "\n";
    echo "   │  EC asociado: " . ($tarea->id_elemento_configuracion ?? 'NULL') . "\n";

    if ($tarea->id_elemento_configuracion) {
        $ec = ElementoConfiguracion::find($tarea->id_elemento_configuracion);
        echo "   │  EC detalles: " . ($ec ? "{$ec->codigo_ec} - {$ec->titulo}" : 'NO ENCONTRADO') . "\n";
    }

    echo "   └─\n\n";
}

// 5. ELEMENTOS DE CONFIGURACIÓN
echo "\n📦 ELEMENTOS DE CONFIGURACIÓN (EC):\n";
$ecs = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->get();
echo "   Total: {$ecs->count()}\n\n";

foreach ($ecs as $ec) {
    echo "   {$ec->codigo_ec}: {$ec->titulo}\n";
    echo "      Tipo: {$ec->tipo}\n";
    echo "      Estado: {$ec->estado}\n";

    // Tareas vinculadas
    $tareasVinculadas = TareaProyecto::where('id_elemento_configuracion', $ec->id)->count();
    echo "      Tareas vinculadas: {$tareasVinculadas}\n";

    // Versiones
    $versiones = $ec->versiones()->count();
    echo "      Versiones: {$versiones}\n\n";
}

// 6. RELACIÓN TAREAS ↔ EC
echo "\n🔗 RELACIÓN TAREAS ↔ ELEMENTOS DE CONFIGURACIÓN:\n";
$tareasConEC = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('id_elemento_configuracion')
    ->get();

echo "   Tareas con EC: {$tareasConEC->count()} / {$tareas->count()}\n\n";

if ($tareasConEC->count() > 0) {
    foreach ($tareasConEC as $tarea) {
        $ec = ElementoConfiguracion::find($tarea->id_elemento_configuracion);
        echo "   ✓ {$tarea->nombre}\n";
        echo "     → {$ec->codigo_ec}: {$ec->titulo}\n\n";
    }
} else {
    echo "   ⚠️ NINGUNA TAREA ESTÁ VINCULADA A UN EC\n\n";
}

// 7. SOLICITUDES DE CAMBIO
echo "\n📄 SOLICITUDES DE CAMBIO:\n";
$solicitudes = \App\Models\SolicitudCambio::where('id_proyecto', $proyecto->id)->get();
echo "   Total: {$solicitudes->count()}\n\n";

foreach ($solicitudes as $sol) {
    echo "   SOL-{$sol->id_solicitud}: {$sol->titulo}\n";
    echo "      Estado: {$sol->estado}\n";
    echo "      Prioridad: {$sol->prioridad}\n";

    // Ítems de cambio
    $items = \App\Models\ItemCambio::where('id_solicitud', $sol->id_solicitud)->get();
    echo "      Ítems de cambio: {$items->count()}\n";

    foreach ($items as $item) {
        $ec = ElementoConfiguracion::find($item->id_elemento_configuracion);
        echo "         - {$item->tipo_cambio} en " . ($ec ? $ec->codigo_ec : 'EC no encontrado') . "\n";
    }
    echo "\n";
}

// 8. VERIFICAR FLUJO COMPLETO
echo "\n\n═══════════════════════════════════════════════════════════════\n";
echo "   VERIFICACIÓN DEL FLUJO SCRUM + GESTIÓN DE CONFIGURACIÓN\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✓ FLUJO ESPERADO:\n";
echo "  1. Crear User Story (Tarea) en Product Backlog\n";
echo "  2. Asignar a Sprint durante Sprint Planning\n";
echo "  3. Mover por columnas del Sprint Board (To Do → In Progress → In Review → Done)\n";
echo "  4. Al completar (Done), crear/actualizar Elemento de Configuración\n";
echo "  5. Vincular Tarea ↔ EC mediante campo 'id_elemento_configuracion'\n";
echo "  6. Si hay cambios, crear Solicitud de Cambio con ítems vinculados a ECs\n";
echo "  7. CCB aprueba/rechaza la solicitud\n";
echo "  8. Si se aprueba, crear nueva versión del EC y tareas de implementación\n\n";

echo "⚠️ PROBLEMAS DETECTADOS:\n\n";

$problemas = [];

// Problema 1: Tareas sin story points
$tareasSinSP = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNull('story_points')
    ->count();
if ($tareasSinSP > 0) {
    $problemas[] = "❌ {$tareasSinSP} tareas sin Story Points asignados";
}

// Problema 2: Tareas sin EC
$tareasSinEC = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNull('id_elemento_configuracion')
    ->count();
if ($tareasSinEC > 0) {
    $problemas[] = "❌ {$tareasSinEC} tareas sin Elemento de Configuración vinculado";
}

// Problema 3: Sprint sin tareas
foreach ($sprints as $sprint) {
    $numTareas = TareaProyecto::where('id_sprint', $sprint->id_sprint)->count();
    if ($numTareas == 0) {
        $problemas[] = "❌ {$sprint->nombre} no tiene tareas asignadas";
    }
}

// Problema 4: Fases incorrectas
$fasesIncorrectas = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereHas('fase', function($q) {
        $q->whereIn('nombre_fase', ['Product Backlog', 'Sprint Planning']);
    })
    ->count();
if ($fasesIncorrectas > 0) {
    $problemas[] = "⚠️ {$fasesIncorrectas} tareas en fases de ceremonia (Product Backlog/Sprint Planning) en lugar de fases del tablero";
}

if (count($problemas) > 0) {
    foreach ($problemas as $problema) {
        echo "   {$problema}\n";
    }
} else {
    echo "   ✅ No se detectaron problemas\n";
}

echo "\n\n═══════════════════════════════════════════════════════════════\n";
echo "   FIN DEL ANÁLISIS\n";
echo "═══════════════════════════════════════════════════════════════\n";
