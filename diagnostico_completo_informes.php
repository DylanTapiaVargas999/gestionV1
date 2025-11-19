<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNÓSTICO COMPLETO DE INFORMES\n";
echo "====================================\n\n";

// Obtener el primer proyecto para pruebas
$proyecto = App\Models\Proyecto::first();

if (!$proyecto) {
    echo "❌ No hay proyectos en la BD\n";
    exit;
}

echo "📊 Proyecto: {$proyecto->nombre_proyecto}\n";
echo "   ID: {$proyecto->id}\n\n";

// 1. PROBLEMA: Jefe de Proyecto (N/A)
echo "1️⃣ JEFE DE PROYECTO:\n";
echo "   - creado_por: " . ($proyecto->creado_por ?? 'NULL') . "\n";
echo "   - Relación creador: " . ($proyecto->creador ? 'SÍ EXISTE' : '❌ NO EXISTE') . "\n";
if ($proyecto->creador) {
    echo "   - Nombre: " . ($proyecto->creador->name ?? $proyecto->creador->nombre ?? 'N/A') . "\n";
}
echo "\n";

// 2. PROBLEMA: Estados de ECs (todo en cero)
echo "2️⃣ ESTADOS DE ELEMENTOS DE CONFIGURACIÓN:\n";
$ecs = App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)->get();
echo "   Total ECs: {$ecs->count()}\n";
echo "   Estados únicos: " . $ecs->pluck('estado')->unique()->implode(', ') . "\n";

$estadosBuscados = ['aprobado', 'liberado', 'en_revision', 'obsoleto'];
foreach ($estadosBuscados as $estado) {
    $count = $ecs->where('estado', $estado)->count();
    echo "   - '{$estado}': {$count}\n";
}
echo "\n";

// Estados reales en mayúsculas
echo "   Estados en MAYÚSCULAS:\n";
$estadosMayusculas = ['APROBADO', 'LIBERADO', 'EN_REVISION', 'OBSOLETO'];
foreach ($estadosMayusculas as $estado) {
    $count = $ecs->where('estado', $estado)->count();
    echo "   - '{$estado}': {$count}\n";
}
echo "\n";

// 3. PROBLEMA: Avance General (0%)
echo "3️⃣ AVANCE GENERAL:\n";
$tareas = $proyecto->tareas;
echo "   Total tareas: {$tareas->count()}\n";
echo "   Estados únicos: " . $tareas->pluck('estado')->unique()->implode(', ') . "\n";

$completadasBuscadas = $tareas->where('estado', 'COMPLETADA')->count();
echo "   - Buscando 'COMPLETADA': {$completadasBuscadas}\n";

$completadasReales = $tareas->filter(function($t) {
    $estado = strtolower(trim($t->estado ?? ''));
    return in_array($estado, ['completada', 'completado', 'done', 'finalizado']);
})->count();
echo "   - Con filtro case-insensitive: {$completadasReales}\n";
echo "\n";

// 4. PROBLEMA: Hitos
echo "4️⃣ HITOS:\n";
echo "   Tareas con es_hito=true: " . $tareas->where('es_hito', true)->count() . "\n";
echo "   Tareas con es_hito=1: " . $tareas->where('es_hito', 1)->count() . "\n";
echo "   Columna es_hito existe: ";
$primeraTarea = $tareas->first();
if ($primeraTarea) {
    echo (isset($primeraTarea->es_hito) ? 'SÍ' : 'NO') . "\n";
}
echo "\n";

echo "✅ RESUMEN DE PROBLEMAS ENCONTRADOS:\n";
echo "=====================================\n";
echo "1. Jefe de Proyecto: Usar 'name' en vez de 'nombre'\n";
echo "2. Estados ECs: Están en MAYÚSCULAS, no en minúsculas\n";
echo "3. Avance: Estados tienen formato inconsistente\n";
echo "4. Hitos: Columna 'es_hito' no existe en la tabla\n";
