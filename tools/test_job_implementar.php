<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\ElementoConfiguracion;
use App\Models\ItemCambio;
use App\Models\TareaProyecto;
use App\Jobs\ImplementarSolicitudAprobadaJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== PRUEBA COMPLETA DEL JOB ===\n\n";

// Obtener proyecto Scrum
$proyecto = Proyecto::where('id_metodologia', 1)->first();
echo "Proyecto: {$proyecto->nombre} (Metodología ID: {$proyecto->id_metodologia})\n";

// Obtener un EC del proyecto
$ec = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->first();
if (!$ec) {
    echo "❌ No hay ECs en el proyecto\n";
    exit;
}
echo "EC: {$ec->codigo_ec} - {$ec->titulo}\n\n";

// Crear una solicitud de cambio de prueba
echo "Creando solicitud de cambio de prueba...\n";
DB::beginTransaction();

$solicitud = new SolicitudCambio();
$solicitud->id = Str::uuid()->toString();
$solicitud->proyecto_id = $proyecto->id;
$solicitud->titulo = 'TEST: Solicitud de cambio para prueba';
$solicitud->descripcion_cambio = 'Prueba del Job de implementación';
$solicitud->motivo_cambio = 'Testing';
$solicitud->prioridad = 'ALTA';
$solicitud->estado = 'APROBADA';
$solicitud->solicitante_id = DB::table('usuarios')->first()->id;
$solicitud->aprobado_por = DB::table('usuarios')->first()->id;
$solicitud->aprobado_en = now();
$solicitud->save();

// Crear item de cambio
$item = new ItemCambio();
$item->id = Str::uuid()->toString();
$item->solicitud_cambio_id = $solicitud->id;
$item->ec_id = $ec->id;
$item->nota = 'Cambio de prueba';
$item->save();

echo "✅ Solicitud creada: {$solicitud->id}\n";
echo "✅ Item de cambio creado para EC: {$ec->codigo_ec}\n\n";

// Contar tareas antes
$tareasAntes = TareaProyecto::where('id_proyecto', $proyecto->id)->count();
echo "Tareas ANTES del Job: {$tareasAntes}\n\n";

// Ejecutar el Job
echo "🚀 Ejecutando Job...\n";
try {
    $job = new ImplementarSolicitudAprobadaJob($solicitud);
    $job->handle();
    echo "✅ Job ejecutado sin errores\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR en Job: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    DB::rollBack();
    exit;
}

// Contar tareas después
$tareasDespues = TareaProyecto::where('id_proyecto', $proyecto->id)->count();
echo "Tareas DESPUÉS del Job: {$tareasDespues}\n";
echo "Tareas CREADAS: " . ($tareasDespues - $tareasAntes) . "\n\n";

// Mostrar las tareas nuevas
if ($tareasDespues > $tareasAntes) {
    echo "=== TAREAS CREADAS ===\n";
    $tareasNuevas = TareaProyecto::where('id_proyecto', $proyecto->id)
        ->where('id_ec', $ec->id)
        ->get();
    foreach ($tareasNuevas as $tarea) {
        echo "- {$tarea->nombre} (Fase ID: {$tarea->id_fase}, Estado: {$tarea->estado})\n";
    }
} else {
    echo "⚠️ NO SE CREARON TAREAS\n";
    echo "Verificando logs...\n";
    $logs = file_get_contents(storage_path('logs/laravel.log'));
    $logLines = explode("\n", $logs);
    $relevantLogs = array_slice($logLines, -50);
    foreach ($relevantLogs as $line) {
        if (stripos($line, 'implementa') !== false || stripos($line, 'fase') !== false || stripos($line, 'tarea') !== false) {
            echo $line . "\n";
        }
    }
}

DB::rollBack();
echo "\n✅ Transacción revertida (solo fue prueba)\n";
