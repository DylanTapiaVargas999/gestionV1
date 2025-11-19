<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║          ANÁLISIS COMPLETO DE SCRUM EN EL SGCS                   ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "═══ 1. TABLAS RELACIONADAS CON SCRUM ═══\n\n";

$tables = [
    'sprints' => 'Gestión de Sprints',
    'daily_scrums' => 'Daily Scrum Meetings',
    'tareas_proyecto' => 'User Stories/Tareas (con campos Scrum)',
    'impedimentos' => 'Impedimentos del proyecto',
];

foreach ($tables as $table => $description) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if ($exists) {
            $count = DB::table($table)->count();
            echo "✅ $table - $description (Registros: $count)\n";

            // Mostrar estructura
            $columns = DB::select("DESCRIBE $table");
            echo "   Columnas: ";
            $cols = array_map(fn($c) => $c->Field, $columns);
            echo implode(', ', $cols) . "\n\n";
        } else {
            echo "❌ $table - NO EXISTE\n\n";
        }
    } catch (\Exception $e) {
        echo "❌ $table - ERROR: " . $e->getMessage() . "\n\n";
    }
}

echo "\n═══ 2. VERIFICAR RELACIONES ENTRE TABLAS ═══\n\n";

// Verificar si tareas_proyecto tiene id_sprint
echo "Verificando relación tareas_proyecto -> sprints:\n";
$tareasConSprint = DB::select("DESCRIBE tareas_proyecto");
$tieneIdSprint = false;
foreach ($tareasConSprint as $col) {
    if ($col->Field === 'id_sprint') {
        $tieneIdSprint = true;
        echo "✅ Campo 'id_sprint' EXISTE en tareas_proyecto\n";
        break;
    }
}

if (!$tieneIdSprint) {
    echo "❌ Campo 'id_sprint' NO EXISTE en tareas_proyecto\n";
    echo "   Solo tiene campo 'sprint' tipo string\n";
}

echo "\n═══ 3. DATOS DE PRUEBA EN PROYECTOS SCRUM ═══\n\n";

$proyectosScrum = DB::table('proyectos')
    ->join('metodologias', 'proyectos.id_metodologia', '=', 'metodologias.id_metodologia')
    ->where('metodologias.nombre', 'Scrum')
    ->select('proyectos.*', 'metodologias.nombre as metodologia')
    ->get();

echo "Proyectos con metodología Scrum: " . count($proyectosScrum) . "\n\n";

foreach ($proyectosScrum as $proyecto) {
    echo "📁 Proyecto: {$proyecto->nombre}\n";

    // Contar tareas
    $tareas = DB::table('tareas_proyecto')->where('id_proyecto', $proyecto->id)->count();
    echo "   Tareas: $tareas\n";

    // Verificar sprints (tabla sprints)
    $sprintsTabla = 0;
    try {
        $sprintsTabla = DB::table('sprints')->where('id_proyecto', $proyecto->id)->count();
    } catch (\Exception $e) {
        // Tabla no existe
    }
    echo "   Sprints (tabla sprints): $sprintsTabla\n";

    // Verificar campo sprint en tareas
    $tareasConSprintStr = DB::table('tareas_proyecto')
        ->where('id_proyecto', $proyecto->id)
        ->whereNotNull('sprint')
        ->count();
    echo "   Tareas con campo 'sprint': $tareasConSprintStr\n";

    // Daily Scrums
    $dailyScrums = 0;
    try {
        $dailyScrums = DB::table('daily_scrums')
            ->join('sprints', 'daily_scrums.id_sprint', '=', 'sprints.id_sprint')
            ->where('sprints.id_proyecto', $proyecto->id)
            ->count();
    } catch (\Exception $e) {
        // Tabla no existe
    }
    echo "   Daily Scrums: $dailyScrums\n";

    // Impedimentos
    $impedimentos = DB::table('impedimentos')
        ->where('id_proyecto', $proyecto->id)
        ->count();
    echo "   Impedimentos: $impedimentos\n\n";
}

echo "\n═══ 4. PROBLEMAS IDENTIFICADOS ═══\n\n";

$problemas = [];

// Verificar si tablas existen
try {
    DB::table('sprints')->count();
} catch (\Exception $e) {
    $problemas[] = "❌ Tabla 'sprints' NO EXISTE - Los modelos Sprint hacen referencia a una tabla inexistente";
}

try {
    DB::table('daily_scrums')->count();
} catch (\Exception $e) {
    $problemas[] = "❌ Tabla 'daily_scrums' NO EXISTE - Los modelos DailyScrum hacen referencia a una tabla inexistente";
}

// Verificar inconsistencia en campos
if (!$tieneIdSprint) {
    $problemas[] = "⚠️  INCONSISTENCIA: tareas_proyecto usa campo 'sprint' (string) en lugar de 'id_sprint' (FK)";
    $problemas[] = "   - El modelo Sprint espera relación con id_sprint";
    $problemas[] = "   - Las tareas usan campo 'sprint' como texto libre";
}

// Verificar datos
if ($sprintsTabla === 0 && count($proyectosScrum) > 0) {
    $problemas[] = "⚠️  No hay sprints creados en la tabla sprints (si existiera)";
}

if (count($problemas) === 0) {
    echo "✅ No se encontraron problemas críticos\n";
} else {
    foreach ($problemas as $problema) {
        echo "$problema\n";
    }
}

echo "\n═══ 5. ARQUITECTURA ACTUAL ═══\n\n";
echo "DISEÑO ACTUAL:\n";
echo "• ScrumController EXISTE y tiene métodos para:\n";
echo "  - dashboard()\n";
echo "  - sprintPlanning()\n";
echo "  - dailyScrum()\n";
echo "  - sprintReview()\n";
echo "  - sprintRetrospective()\n\n";

echo "• Modelos EXISTEN:\n";
echo "  - Sprint (espera tabla 'sprints' con id_sprint)\n";
echo "  - DailyScrum (espera tabla 'daily_scrums' con id_sprint FK)\n";
echo "  - TareaProyecto (tiene campos: sprint [string], story_points)\n";
echo "  - Impedimento (tiene id_sprint nullable)\n\n";

echo "• PROBLEMA: Las migraciones NO CREARON las tablas sprints y daily_scrums\n";
echo "  - Los modelos están huérfanos\n";
echo "  - El controlador usa campo 'sprint' como string en lugar de relación FK\n\n";

echo "\n╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║                      FIN DEL ANÁLISIS                             ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n";
