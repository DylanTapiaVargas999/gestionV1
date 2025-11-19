<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 BUSCANDO TODAS LAS TAREAS CON ESTADOS INVÁLIDOS\n";
echo "===================================================\n\n";

// Estados válidos del sistema
$estadosValidos = [
    'Pendiente',
    'En Progreso',
    'En Revisión',
    'Completada',
    // Variaciones aceptadas (se normalizan automáticamente)
    'pendiente',
    'to do',
    'todo',
    'por hacer',
    'en progreso',
    'en_progreso',
    'in progress',
    'en revisión',
    'en revision',
    'in review',
    'review',
    'completada',
    'completado',
    'done',
    'finalizado'
];

// Obtener todas las tareas
$todasLasTareas = DB::table('tareas_proyecto')->get();

$tareasConProblema = [];

foreach ($todasLasTareas as $tarea) {
    $estadoLower = strtolower(trim($tarea->estado ?? ''));
    
    // Si el estado no está en la lista de válidos
    if (!in_array($estadoLower, array_map('strtolower', $estadosValidos))) {
        $tareasConProblema[] = $tarea;
    }
}

if (empty($tareasConProblema)) {
    echo "✅ ¡Excelente! No se encontraron tareas con estados inválidos.\n";
    echo "   Todas las {$todasLasTareas->count()} tareas tienen estados correctos.\n";
} else {
    echo "⚠️ Se encontraron " . count($tareasConProblema) . " tareas con estados inválidos:\n\n";
    
    foreach ($tareasConProblema as $tarea) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: {$tarea->id_tarea}\n";
        echo "Nombre: {$tarea->nombre}\n";
        echo "Estado INVÁLIDO: '{$tarea->estado}'\n";
        
        $fase = DB::table('fases_metodologia')->where('id_fase', $tarea->id_fase)->first();
        if ($fase) {
            echo "Fase: {$fase->nombre_fase}\n";
        }
        
        // Sugerir corrección
        echo "Corrección sugerida: Cambiar a 'Pendiente'\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
    
    // Preguntar si quiere corregir
    echo "\n¿Deseas corregir automáticamente estas tareas? (todas se pondrán en 'Pendiente')\n";
    echo "Ejecuta: php corregir_todas_las_tareas.php\n";
}

echo "\n📊 ESTADÍSTICAS:\n";
echo "===================================================\n";
echo "Total de tareas: {$todasLasTareas->count()}\n";
echo "Tareas con estado válido: " . ($todasLasTareas->count() - count($tareasConProblema)) . " ✅\n";
echo "Tareas con estado inválido: " . count($tareasConProblema) . " ❌\n";
