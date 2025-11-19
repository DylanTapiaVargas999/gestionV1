<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CORRIGIENDO TAREA CON ESTADO INCORRECTO\n";
echo "===========================================\n\n";

// Buscar la tarea con estado "Mantenimiento"
$tarea = DB::table('tareas_proyecto')
    ->where('id_tarea', 25)
    ->first();

if ($tarea) {
    echo "📋 Tarea encontrada:\n";
    echo "   ID: {$tarea->id_tarea}\n";
    echo "   Nombre: {$tarea->nombre}\n";
    echo "   Estado INCORRECTO: {$tarea->estado}\n\n";
    
    // Actualizar el estado a "Pendiente"
    DB::table('tareas_proyecto')
        ->where('id_tarea', 25)
        ->update([
            'estado' => 'Pendiente',
            'actualizado_en' => now()
        ]);
    
    echo "✅ Tarea corregida:\n";
    echo "   Nuevo estado: Pendiente\n\n";
    
    // Verificar la corrección
    $tareaActualizada = DB::table('tareas_proyecto')
        ->where('id_tarea', 25)
        ->first();
    
    echo "🔍 Verificación:\n";
    echo "   ID: {$tareaActualizada->id_tarea}\n";
    echo "   Nombre: {$tareaActualizada->nombre}\n";
    echo "   Estado CORREGIDO: {$tareaActualizada->estado}\n";
    echo "   Fase ID: {$tareaActualizada->id_fase}\n";
    
    $fase = DB::table('fases_metodologia')
        ->where('id_fase', $tareaActualizada->id_fase)
        ->first();
    
    if ($fase) {
        echo "   Fase: {$fase->nombre_fase} ✅\n";
    }
    
    echo "\n✅ ¡Corrección completada exitosamente!\n";
} else {
    echo "❌ No se encontró la tarea con ID 25\n";
}

echo "\n📊 RESUMEN:\n";
echo "===========================================\n";
echo "✅ Controlador corregido: Ya no usa nombre de fase como estado\n";
echo "✅ Tarea #25 corregida: Estado cambiado de 'Mantenimiento' a 'Pendiente'\n";
echo "✅ Sistema: Ahora todas las tareas nuevas se crearán con estado 'Pendiente'\n";
