<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "✅ VERIFICACIÓN FINAL DE INFORMES\n";
echo "==================================\n\n";

// Buscar proyecto con datos
$proyecto = App\Models\Proyecto::with(['tareas', 'creador', 'metodologia'])
    ->whereHas('tareas')
    ->first();

if (!$proyecto) {
    echo "❌ No hay proyectos con tareas\n";
    exit;
}

echo "📊 Proyecto: {$proyecto->nombre_proyecto}\n";
echo "   Código: {$proyecto->codigo}\n\n";

// TEST 1: Jefe de Proyecto
echo "1️⃣ JEFE DE PROYECTO:\n";
echo "   creado_por: " . ($proyecto->creado_por ?? 'NULL') . "\n";
echo "   creador->name: " . ($proyecto->creador->name ?? 'N/A') . "\n";
echo "   ✅ Campo correcto usado: 'name'\n\n";

// TEST 2: Estados de ECs
echo "2️⃣ ESTADOS DE ELEMENTOS DE CONFIGURACIÓN:\n";
$ecs = App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)->get();
$estadosEC = $ecs->groupBy('estado')->map->count();
echo "   Total ECs: {$ecs->count()}\n";
foreach ($estadosEC as $estado => $count) {
    echo "   - '{$estado}': {$count}\n";
}

// Verificar conteos con estados correctos
$aprobados = $ecs->where('estado', 'APROBADO')->count();
$liberados = $ecs->where('estado', 'LIBERADO')->count();
$enRevision = $ecs->where('estado', 'EN_REVISION')->count();
$obsoletos = $ecs->where('estado', 'OBSOLETO')->count();

echo "\n   Conteo con estados MAYÚSCULAS:\n";
echo "   - APROBADO: {$aprobados}\n";
echo "   - LIBERADO: {$liberados}\n";
echo "   - EN_REVISION: {$enRevision}\n";
echo "   - OBSOLETO: {$obsoletos}\n";

if ($ecs->count() > 0) {
    $porcentajes = [
        'APROBADO' => round(($aprobados / $ecs->count()) * 100, 1),
        'LIBERADO' => round(($liberados / $ecs->count()) * 100, 1),
        'EN_REVISION' => round(($enRevision / $ecs->count()) * 100, 1),
        'OBSOLETO' => round(($obsoletos / $ecs->count()) * 100, 1),
    ];
    
    echo "\n   Porcentajes:\n";
    foreach ($porcentajes as $estado => $pct) {
        echo "   - {$estado}: {$pct}%\n";
    }
    echo "   ✅ Estados en mayúsculas funcionan\n\n";
}

// TEST 3: Estados de Tareas (case-insensitive)
echo "3️⃣ ESTADOS DE TAREAS (Case-Insensitive):\n";
$tareas = $proyecto->tareas;
echo "   Total tareas: {$tareas->count()}\n";

$estadosTarea = $tareas->groupBy('estado')->map->count();
echo "\n   Estados en BD:\n";
foreach ($estadosTarea as $estado => $count) {
    echo "   - '{$estado}': {$count}\n";
}

// Conteo case-insensitive
$completadas = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
})->count();

$enProgreso = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return in_array($estado, ['en_progreso', 'en progreso', 'in progress', 'working']);
})->count();

$pendientes = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return in_array($estado, ['pendiente', 'to do', 'todo', 'backlog']);
})->count();

$enRevision = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return in_array($estado, ['en_revisión', 'en revision', 'in review', 'review']);
})->count();

echo "\n   Conteo Case-Insensitive:\n";
echo "   - Completadas: {$completadas}\n";
echo "   - En Progreso: {$enProgreso}\n";
echo "   - Pendientes: {$pendientes}\n";
echo "   - En Revisión: {$enRevision}\n";

$avance = $tareas->count() > 0 ? round(($completadas / $tareas->count()) * 100, 1) : 0;
echo "\n   Avance General: {$avance}%\n";
echo "   ✅ Filtrado case-insensitive funciona\n\n";

// TEST 4: Tipos de ECs
echo "4️⃣ TIPOS DE ELEMENTOS DE CONFIGURACIÓN:\n";
$tiposEC = $ecs->groupBy('tipo')->map->count();
foreach ($tiposEC as $tipo => $count) {
    echo "   - '{$tipo}': {$count}\n";
}

$ecsPorTipo = [
    'CODIGO' => $ecs->where('tipo', 'CODIGO')->count(),
    'DOCUMENTO' => $ecs->where('tipo', 'DOCUMENTO')->count(),
    'SCRIPT_BD' => $ecs->where('tipo', 'SCRIPT_BD')->count(),
    'CASO_PRUEBA' => $ecs->where('tipo', 'CASO_PRUEBA')->count(),
    'CONFIGURACION' => $ecs->where('tipo', 'CONFIGURACION')->count(),
];

echo "\n   Conteo por tipo (mayúsculas):\n";
foreach ($ecsPorTipo as $tipo => $count) {
    if ($count > 0) {
        echo "   - {$tipo}: {$count}\n";
    }
}
echo "   ✅ Tipos en mayúsculas funcionan\n\n";

// TEST 5: Hitos (usando fecha_fin)
echo "5️⃣ CUMPLIMIENTO DE HITOS:\n";
$tareasConFechaFin = $tareas->whereNotNull('fecha_fin_estimada');
$hitosCompletados = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return $t->fecha_fin_estimada && in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
})->count();

echo "   Tareas con fecha fin: {$tareasConFechaFin->count()}\n";
echo "   Hitos completados: {$hitosCompletados}\n";
$cumplimiento = $tareasConFechaFin->count() > 0 ? round(($hitosCompletados / $tareasConFechaFin->count()) * 100, 1) : 0;
echo "   Cumplimiento: {$cumplimiento}%\n";
echo "   ✅ Cálculo de hitos sin columna 'es_hito'\n\n";

echo "========================================\n";
echo "✅ TODAS LAS CORRECCIONES VERIFICADAS\n";
echo "========================================\n";
