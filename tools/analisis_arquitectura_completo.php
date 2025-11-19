<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════════╗\n";
echo "║   ANÁLISIS COMPLETO DE ARQUITECTURA: SCRUM + CASCADA + SGCS              ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ==================== ANÁLISIS DE TABLAS ====================
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "1. INVENTARIO DE TABLAS ACTUALES\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$allTables = DB::select('SHOW TABLES');
$tableList = [];
foreach ($allTables as $table) {
    $tableArray = (array)$table;
    $tableList[] = array_values($tableArray)[0];
}

echo "Total de tablas: " . count($tableList) . "\n\n";

// Categorizar tablas
$categories = [
    'Core SGCS' => ['proyectos', 'metodologias', 'fases_metodologia', 'usuarios', 'roles', 'usuarios_roles'],
    'Elementos Configuración' => ['elementos_configuracion', 'versiones_ec', 'relaciones_ec', 'plantillas_ec'],
    'Gestión de Cambios' => ['solicitudes_cambio', 'items_cambio', 'comite_cambios', 'miembros_ccb', 'votos_ccb'],
    'Tareas y Equipos' => ['tareas_proyecto', 'equipos', 'miembros_equipo'],
    'Scrum' => ['sprints', 'daily_scrums'],
    'Impedimentos' => ['impedimentos'],
    'Liberaciones' => ['liberaciones', 'items_liberacion'],
    'Cronograma' => ['ajustes_cronograma', 'historial_ajustes_tareas'],
    'Commits/Git' => ['commits_repositorio'],
    'Sistema' => ['jobs', 'failed_jobs', 'cache', 'sessions', 'password_reset_tokens'],
];

foreach ($categories as $category => $expectedTables) {
    echo "📂 $category:\n";
    foreach ($expectedTables as $table) {
        $exists = in_array($table, $tableList);
        if ($exists) {
            $count = DB::table($table)->count();
            echo "   ✅ $table ($count registros)\n";
        } else {
            echo "   ❌ $table (NO EXISTE)\n";
        }
    }
    echo "\n";
}

// ==================== ANÁLISIS DE INCONSISTENCIAS ====================
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "2. PROBLEMAS CRÍTICOS IDENTIFICADOS\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$problemas = [];
$advertencias = [];

// Verificar tablas Scrum faltantes
$sprints_existe = in_array('sprints', $tableList);
$daily_scrums_existe = in_array('daily_scrums', $tableList);

if (!$sprints_existe) {
    $problemas[] = [
        'tipo' => 'CRÍTICO',
        'area' => 'Scrum',
        'problema' => "Tabla 'sprints' NO EXISTE",
        'impacto' => "El modelo Sprint no puede funcionar. Las vistas y controladores fallarán.",
        'solucion' => "Crear migración para tabla sprints"
    ];
}

if (!$daily_scrums_existe) {
    $problemas[] = [
        'tipo' => 'CRÍTICO',
        'area' => 'Scrum',
        'problema' => "Tabla 'daily_scrums' NO EXISTE",
        'impacto' => "El modelo DailyScrum no puede funcionar. No se pueden registrar daily meetings.",
        'solucion' => "Crear migración para tabla daily_scrums"
    ];
}

// Verificar inconsistencia en relación Sprint-Tarea
$tareasColumns = DB::select("DESCRIBE tareas_proyecto");
$tieneIdSprint = false;
$tieneSprint = false;

foreach ($tareasColumns as $col) {
    if ($col->Field === 'id_sprint') $tieneIdSprint = true;
    if ($col->Field === 'sprint') $tieneSprint = true;
}

if ($tieneSprint && !$tieneIdSprint) {
    $problemas[] = [
        'tipo' => 'ARQUITECTURA',
        'area' => 'Scrum/Tareas',
        'problema' => "Campo 'sprint' es STRING en lugar de FK a tabla sprints",
        'impacto' => "No hay integridad referencial. Los sprints son texto libre, no entidades gestionables.",
        'solucion' => "Cambiar 'sprint' (string) por 'id_sprint' (FK) en tareas_proyecto"
    ];
}

if ($tieneIdSprint && !$sprints_existe) {
    $problemas[] = [
        'tipo' => 'CRÍTICO',
        'area' => 'Scrum',
        'problema' => "tareas_proyecto tiene 'id_sprint' pero tabla 'sprints' no existe",
        'impacto' => "FK sin tabla destino causará errores en migraciones y consultas.",
        'solucion' => "Crear tabla sprints ANTES que la FK en tareas_proyecto"
    ];
}

// Verificar relación impedimentos-sprint
if (in_array('impedimentos', $tableList)) {
    $impedimentosColumns = DB::select("DESCRIBE impedimentos");
    $impedimentoTieneIdSprint = false;
    foreach ($impedimentosColumns as $col) {
        if ($col->Field === 'id_sprint') {
            $impedimentoTieneIdSprint = true;
            break;
        }
    }

    if ($impedimentoTieneIdSprint && !$sprints_existe) {
        $problemas[] = [
            'tipo' => 'CRÍTICO',
            'area' => 'Scrum/Impedimentos',
            'problema' => "impedimentos tiene 'id_sprint' pero tabla 'sprints' no existe",
            'impacto' => "FK sin tabla destino. Impedimentos no se pueden asociar correctamente a sprints.",
            'solucion' => "Crear tabla sprints primero"
        ];
    }
}

// Verificar si hay suficientes campos para Cascada
$advertencias[] = [
    'tipo' => 'ADVERTENCIA',
    'area' => 'Cascada',
    'problema' => "No hay tablas específicas para gestión de Cascada",
    'impacto' => "Cascada usa las mismas tareas_proyecto y fases. Funciona, pero no tiene entidades propias como Scrum.",
    'solucion' => "OPCIONAL: Crear entidades específicas si se necesitan (ej: hitos, entregables formales)"
];

// Mostrar problemas
if (count($problemas) > 0) {
    echo "🚨 PROBLEMAS CRÍTICOS:\n\n";
    foreach ($problemas as $i => $p) {
        echo ($i + 1) . ". [{$p['tipo']}] {$p['area']}\n";
        echo "   ❌ Problema: {$p['problema']}\n";
        echo "   💥 Impacto: {$p['impacto']}\n";
        echo "   ✅ Solución: {$p['solucion']}\n\n";
    }
} else {
    echo "✅ No se encontraron problemas críticos\n\n";
}

if (count($advertencias) > 0) {
    echo "⚠️  ADVERTENCIAS:\n\n";
    foreach ($advertencias as $i => $a) {
        echo ($i + 1) . ". [{$a['tipo']}] {$a['area']}\n";
        echo "   ⚠️  {$a['problema']}\n";
        echo "   📝 {$a['impacto']}\n";
        echo "   💡 {$a['solucion']}\n\n";
    }
}

// ==================== PROPUESTA DE ARQUITECTURA ====================
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "3. ARQUITECTURA PROPUESTA (CORRECTA)\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "📐 DISEÑO ÓPTIMO PARA SCRUM + CASCADA + SGCS:\n\n";

echo "A) CORE SGCS (gestión de configuración):\n";
echo "   ✅ proyectos → define metodología (Scrum/Cascada)\n";
echo "   ✅ elementos_configuracion → artefactos versionados\n";
echo "   ✅ versiones_ec → historial de cambios\n";
echo "   ✅ solicitudes_cambio → gestión de cambios formal\n";
echo "   ✅ liberaciones → releases del sistema\n\n";

echo "B) GESTIÓN DE TAREAS (compartido por ambas metodologías):\n";
echo "   ✅ tareas_proyecto → base común para ambas metodologías\n";
echo "   • Campos comunes: nombre, descripcion, estado, prioridad, responsable\n";
echo "   • Campos Scrum: story_points, id_sprint (FK)\n";
echo "   • Campos Cascada: horas_estimadas, entregable\n\n";

echo "C) ESPECÍFICO DE SCRUM:\n";
echo "   🔧 sprints → entidad principal de Scrum\n";
echo "      - id_sprint (PK)\n";
echo "      - id_proyecto (FK)\n";
echo "      - nombre (ej: 'Sprint 1', 'Sprint 2')\n";
echo "      - fecha_inicio, fecha_fin\n";
echo "      - objetivo, velocidad_estimada, velocidad_real\n";
echo "      - estado (planificado, activo, completado)\n\n";

echo "   🔧 daily_scrums → registro de daily meetings\n";
echo "      - id_daily (PK)\n";
echo "      - id_sprint (FK)\n";
echo "      - id_usuario (FK)\n";
echo "      - fecha\n";
echo "      - que_hice_ayer, que_hare_hoy, impedimentos\n\n";

echo "   ✅ impedimentos → bloqueos del equipo\n";
echo "      - id_sprint (FK nullable) → asociar a sprint\n\n";

echo "D) ESPECÍFICO DE CASCADA (opcional, puede crecer):\n";
echo "   💡 hitos_cascada (opcional)\n";
echo "      - id_hito (PK)\n";
echo "      - id_proyecto (FK)\n";
echo "      - nombre, fecha_compromiso, entregables\n\n";

echo "   💡 entregables_formales (opcional)\n";
echo "      - id_entregable (PK)\n";
echo "      - id_fase (FK)\n";
echo "      - documento, aprobado_por, fecha_aprobacion\n\n";

echo "E) RELACIONES:\n";
echo "   tareas_proyecto.id_sprint → sprints.id_sprint\n";
echo "   daily_scrums.id_sprint → sprints.id_sprint\n";
echo "   impedimentos.id_sprint → sprints.id_sprint\n";
echo "   sprints.id_proyecto → proyectos.id\n\n";

// ==================== ESTADO ACTUAL VS IDEAL ====================
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "4. COMPARACIÓN: ESTADO ACTUAL vs IDEAL\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$comparacion = [
    ['Tabla', 'Estado Actual', 'Estado Ideal', 'Acción'],
    ['sprints', '❌ NO EXISTE', '✅ DEBE EXISTIR', '🔧 CREAR'],
    ['daily_scrums', '❌ NO EXISTE', '✅ DEBE EXISTIR', '🔧 CREAR'],
    ['tareas_proyecto.sprint', '⚠️  STRING', '✅ id_sprint (FK)', '🔧 MIGRAR'],
    ['impedimentos.id_sprint', '✅ EXISTE', '✅ FK a sprints', '⏳ ESPERA tabla sprints'],
];

printf("%-25s %-20s %-25s %-15s\n", ...$comparacion[0]);
echo str_repeat("─", 90) . "\n";
for ($i = 1; $i < count($comparacion); $i++) {
    printf("%-25s %-20s %-25s %-15s\n", ...$comparacion[$i]);
}

echo "\n";

// ==================== PLAN DE ACCIÓN ====================
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "5. PLAN DE ACCIÓN RECOMENDADO\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "FASE 1: CREAR INFRAESTRUCTURA SCRUM (CRÍTICO)\n";
echo "──────────────────────────────────────────────\n";
echo "✅ Paso 1: Crear migración para tabla 'sprints'\n";
echo "✅ Paso 2: Crear migración para tabla 'daily_scrums'\n";
echo "✅ Paso 3: Migrar campo 'sprint' (string) a 'id_sprint' (FK) en tareas_proyecto\n";
echo "✅ Paso 4: Actualizar controladores para usar entidad Sprint en lugar de string\n";
echo "✅ Paso 5: Crear seeders para sprints de prueba\n\n";

echo "FASE 2: MEJORAR INTEGRACIÓN SGCS + SCRUM\n";
echo "──────────────────────────────────────────────\n";
echo "✅ Paso 6: Sincronizar creación de sprints con planificación de releases\n";
echo "✅ Paso 7: Asociar versiones de EC con sprints (trazabilidad)\n";
echo "✅ Paso 8: Dashboard Scrum completo con métricas reales\n\n";

echo "FASE 3: EXTENDER PARA CASCADA (FUTURO)\n";
echo "──────────────────────────────────────────────\n";
echo "💡 Paso 9: Crear tabla 'hitos_cascada' (opcional)\n";
echo "💡 Paso 10: Asociar entregables formales con fases\n";
echo "💡 Paso 11: Dashboard Cascada con diagramas de Gantt\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "6. DECISIÓN FINAL\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "¿QUÉ HACER?\n\n";
echo "OPCIÓN A (RECOMENDADA): CORREGIR ARQUITECTURA\n";
echo "   ✅ Crear tablas sprints y daily_scrums\n";
echo "   ✅ Migrar de 'sprint' (string) a 'id_sprint' (FK)\n";
echo "   ✅ Sistema robusto, escalable, con integridad referencial\n";
echo "   ⏱️  Tiempo: ~2-3 horas de desarrollo\n\n";

echo "OPCIÓN B (RÁPIDA PERO LIMITADA): MANTENER STRING\n";
echo "   ⚠️  Eliminar modelos Sprint y DailyScrum\n";
echo "   ⚠️  Usar 'sprint' como texto libre\n";
echo "   ⚠️  Sin entidades gestionables, sin métricas avanzadas\n";
echo "   ⏱️  Tiempo: ~30 minutos (solo limpiar código)\n\n";

echo "💡 RECOMENDACIÓN: OPCIÓN A\n";
echo "   Razón: El sistema está diseñado para crecer. Scrum necesita entidades\n";
echo "   reales para gestionar sprints, daily scrums, burndown charts, etc.\n\n";

echo "╔═══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         FIN DEL ANÁLISIS                                  ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════╝\n";
