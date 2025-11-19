<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 BUSCANDO PROYECTO CON DATOS\n";
echo "================================\n\n";

$proyectos = App\Models\Proyecto::with('tareas')->get();

foreach ($proyectos as $proyecto) {
    $tareasCount = $proyecto->tareas->count();
    $ecsCount = App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)->count();
    
    if ($tareasCount > 0 || $ecsCount > 0) {
        echo "📊 Proyecto: {$proyecto->nombre_proyecto}\n";
        echo "   Código: {$proyecto->codigo}\n";
        echo "   Tareas: {$tareasCount}\n";
        echo "   ECs: {$ecsCount}\n";
        
        // Jefe de Proyecto
        echo "\n   JEFE DE PROYECTO:\n";
        echo "   - creado_por: " . ($proyecto->creado_por ?? 'NULL') . "\n";
        if ($proyecto->creador) {
            echo "   - name: " . ($proyecto->creador->name ?? 'N/A') . "\n";
            echo "   - nombre: " . ($proyecto->creador->nombre ?? 'N/A') . "\n";
        }
        
        // Estados de tareas
        if ($tareasCount > 0) {
            echo "\n   ESTADOS DE TAREAS:\n";
            $estados = $proyecto->tareas->pluck('estado')->unique();
            foreach ($estados as $estado) {
                $count = $proyecto->tareas->where('estado', $estado)->count();
                echo "   - '{$estado}': {$count}\n";
            }
        }
        
        // Estados de ECs
        if ($ecsCount > 0) {
            echo "\n   ESTADOS DE ECs:\n";
            $ecs = App\Models\ElementoConfiguracion::where('proyecto_id', $proyecto->id)->get();
            $estadosEC = $ecs->pluck('estado')->unique();
            foreach ($estadosEC as $estado) {
                $count = $ecs->where('estado', $estado)->count();
                echo "   - '{$estado}': {$count}\n";
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n\n";
    }
}
