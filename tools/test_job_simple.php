<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SolicitudCambio;
use App\Models\VersionEC;
use App\Models\TareaProyecto;
use App\Jobs\ImplementarSolicitudAprobadaJob;

echo "🚀 TEST SIMPLE - JOB CCB\n";
echo "========================\n\n";

try {
    // 1. Buscar solicitudes existentes
    $solicitudes = SolicitudCambio::with(['proyecto.metodologia', 'elementosConfiguracion'])
                                  ->whereIn('estado', ['APROBADO', 'IMPLEMENTADA'])
                                  ->get();
    
    if ($solicitudes->isEmpty()) {
        echo "❌ No hay solicitudes APROBADAS o IMPLEMENTADAS para probar\n";
        echo "📋 SOLICITUDES DISPONIBLES:\n";
        
        $todasSolicitudes = SolicitudCambio::with('proyecto')->get();
        foreach ($todasSolicitudes as $sol) {
            echo "   ID: {$sol->id} | {$sol->titulo} | Estado: {$sol->estado}\n";
        }
        exit(1);
    }
    
    // 2. Usar la primera solicitud y cambiarla a APROBADO para probar
    $solicitud = $solicitudes->first();
    
    // Cambiar temporalmente a APROBADO para poder probar el Job
    $estadoOriginal = $solicitud->estado;
    $solicitud->update(['estado' => 'APROBADO']);
    
    echo "📋 SOLICITUD SELECCIONADA:\n";
    echo "   ID: {$solicitud->id}\n";
    echo "   Título: {$solicitud->titulo}\n";
    echo "   Estado original: {$estadoOriginal} → Cambió a: APROBADO (para test)\n";
    echo "   Proyecto: {$solicitud->proyecto->nombre}\n";
    echo "   Metodología: " . ($solicitud->proyecto->metodologia->nombre ?? 'No definida') . "\n";
    echo "   ECs afectados: " . $solicitud->elementosConfiguracion->count() . "\n\n";
    
    // 3. Contar datos ANTES del Job
    $versionesAntes = VersionEC::where('solicitud_cambio_id', $solicitud->id)->count();
    $tareasAntes = TareaProyecto::where('solicitud_cambio_id', $solicitud->id)->count();
    
    echo "📊 ESTADO ANTES DEL JOB:\n";
    echo "   Versiones EC: {$versionesAntes}\n";
    echo "   Tareas: {$tareasAntes}\n\n";
    
    // 4. EJECUTAR EL JOB
    echo "⚡ EJECUTANDO JOB...\n";
    echo "==================\n";
    
    try {
        $job = new ImplementarSolicitudAprobadaJob($solicitud->id);
        $job->handle();
        echo "✅ JOB EJECUTADO SIN ERRORES!\n\n";
    } catch (Exception $jobError) {
        echo "❌ ERROR EN JOB: " . $jobError->getMessage() . "\n";
        echo "📍 Línea: " . $jobError->getLine() . "\n";
        echo "📁 Archivo: " . basename($jobError->getFile()) . "\n\n";
    }
    
    // 5. VERIFICAR RESULTADOS
    echo "📊 RESULTADOS DESPUÉS DEL JOB:\n";
    echo "===============================\n";
    
    // Contar versiones creadas
    $versionesDespues = VersionEC::where('solicitud_cambio_id', $solicitud->id)->count();
    $nuevasVersiones = VersionEC::where('solicitud_cambio_id', $solicitud->id)->get();
    
    echo "📦 VERSIONES:\n";
    echo "   Antes: {$versionesAntes} | Después: {$versionesDespues}\n";
    echo "   Nuevas creadas: " . ($versionesDespues - $versionesAntes) . "\n";
    
    if ($nuevasVersiones->count() > 0) {
        echo "\n   📋 DETALLE DE VERSIONES:\n";
        foreach ($nuevasVersiones as $version) {
            echo "      → {$version->version} | Estado: {$version->estado}\n";
            echo "        EC: {$version->elementoConfiguracion->codigo_ec}\n";
            echo "        Commit: " . ($version->commit_id ?? 'null (correcto)') . "\n";
        }
    }
    
    // Contar tareas creadas
    $tareasDespues = TareaProyecto::where('solicitud_cambio_id', $solicitud->id)->count();
    $nuevasTareas = TareaProyecto::where('solicitud_cambio_id', $solicitud->id)->get();
    
    echo "\n🏗️ TAREAS:\n";
    echo "   Antes: {$tareasAntes} | Después: {$tareasDespues}\n";
    echo "   Nuevas creadas: " . ($tareasDespues - $tareasAntes) . "\n";
    
    if ($nuevasTareas->count() > 0) {
        echo "\n   📋 DETALLE DE TAREAS:\n";
        foreach ($nuevasTareas as $tarea) {
            echo "      → {$tarea->nombre}\n";
            echo "        Estado: {$tarea->estado}\n";
            echo "        Fase: " . ($tarea->fase->nombre ?? 'No definida') . "\n";
        }
    }
    
    // 6. RESUMEN
    echo "\n🎉 RESUMEN DEL TEST:\n";
    echo "===================\n";
    
    if ($versionesDespues > $versionesAntes) {
        echo "✅ Versiones EC creadas correctamente\n";
    } else {
        echo "⚠️ No se crearon nuevas versiones\n";
    }
    
    if ($tareasDespues > $tareasAntes) {
        echo "✅ Tareas creadas correctamente\n";
    } else {
        echo "⚠️ No se crearon nuevas tareas\n";
    }
    
    echo "\n💡 El Job " . (($versionesDespues > $versionesAntes || $tareasDespues > $tareasAntes) ? "FUNCIONÓ" : "NO CREÓ DATOS NUEVOS") . "\n";

    // 7. RESTAURAR ESTADO ORIGINAL
    echo "\n🔄 Restaurando estado original ({$estadoOriginal})...\n";
    $solicitud->update(['estado' => $estadoOriginal]);
    echo "✅ Estado restaurado\n";

} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
    echo "📁 Archivo: " . basename($e->getFile()) . "\n";
    exit(1);
}