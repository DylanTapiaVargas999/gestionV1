<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 BUSCANDO TAREAS CON ESTADO INCORRECTO\n";
echo "==========================================\n\n";

// Buscar tareas con estado "Mantenimiento" o nombre "aaaa"
$tareas = DB::table('tareas_proyecto')
    ->where('nombre', 'like', '%aaaa%')
    ->orWhere('estado', 'Mantenimiento')
    ->orWhere('estado', 'like', '%Manten%')
    ->get();

if ($tareas->isEmpty()) {
    echo "✅ No se encontraron tareas con estado 'Mantenimiento'\n";
} else {
    echo "❌ Se encontraron {$tareas->count()} tareas con problemas:\n\n";
    
    foreach ($tareas as $tarea) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID Tarea: {$tarea->id_tarea}\n";
        echo "Nombre: {$tarea->nombre}\n";
        echo "Estado: {$tarea->estado}\n";
        echo "Fase ID: {$tarea->id_fase}\n";
        echo "Proyecto ID: {$tarea->id_proyecto}\n";
        
        // Obtener nombre de la fase
        $fase = DB::table('fases_metodologia')->where('id_fase', $tarea->id_fase)->first();
        if ($fase) {
            echo "Fase: {$fase->nombre_fase}\n";
        }
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

echo "\n📊 ESTADOS VÁLIDOS DEL SISTEMA:\n";
echo "================================\n";
echo "✅ Pendiente\n";
echo "✅ En Progreso\n";
echo "✅ En Revisión\n";
echo "✅ Completada\n";
echo "\n❌ 'Mantenimiento' NO es un estado válido (es un nombre de fase)\n";
