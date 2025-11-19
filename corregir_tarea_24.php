<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CORRIGIENDO TAREA ID 24\n";
echo "===========================\n\n";

DB::table('tareas_proyecto')
    ->where('id_tarea', 24)
    ->update([
        'estado' => 'Pendiente',
        'actualizado_en' => now()
    ]);

echo "✅ Tarea ID 24 corregida: Estado cambiado a 'Pendiente'\n";

// Verificar
$tarea = DB::table('tareas_proyecto')->where('id_tarea', 24)->first();
echo "\n📋 Verificación:\n";
echo "   ID: {$tarea->id_tarea}\n";
echo "   Nombre: {$tarea->nombre}\n";
echo "   Estado: {$tarea->estado} ✅\n";
