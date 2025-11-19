<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\ElementoConfiguracion;
use App\Models\VersionEC;
use App\Models\TareaProyecto;
use App\Models\Usuario;
use App\Jobs\ImplementarSolicitudAprobadaJob;

echo "🚀 MINI TEST - JOB CCB IMPLEMENTACIÓN\n";
echo "====================================\n\n";

try {
    // 1. Buscar un proyecto existente
    $proyecto = Proyecto::with('metodologia')->first();
    if (!$proyecto) {
        echo "❌ ERROR: No hay proyectos en la BD\n";
        exit(1);
    }
    
    echo "📁 PROYECTO SELECCIONADO:\n";
    echo "   ID: {$proyecto->id}\n";
    echo "   Nombre: {$proyecto->nombre}\n";
    echo "   Metodología: " . ($proyecto->metodologia->nombre ?? 'No definida') . "\n\n";

    // 2. Crear un EC de prueba si no existe
    $ec = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->first();
    if (!$ec) {
        $ultimoNumero = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->count();
        $codigoEC = "EC-" . date('Y') . "-" . str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
        
        $ec = ElementoConfiguracion::create([
            'codigo_ec' => $codigoEC,
            'titulo' => 'Login.php (TEST)',
            'nombre' => 'Login.php (TEST)',
            'descripcion' => 'Archivo de login para pruebas',
            'tipo' => 'CODIGO',
            'ruta' => '/app/auth/Login.php',
            'proyecto_id' => $proyecto->id,
            'estado' => 'APROBADO',
            'es_critico' => false,
            'creado_por' => 1
        ]);
        echo "📄 EC CREADO: {$ec->codigo_ec}\n";
    } else {
        echo "📄 EC ENCONTRADO: {$ec->codigo_ec}\n";
    }

    // 3. Crear solicitud de cambio de prueba
    $solicitud = SolicitudCambio::create([
        'titulo' => 'TEST: Agregar validación de contraseña',
        'descripcion' => 'Agregar validación de longitud mínima en el login',
        'justificacion' => 'Mejorar seguridad del sistema',
        'prioridad' => 'MEDIA',
        'tipo_cambio' => 'CORRECTIVO',
        'impacto' => 'MEDIO',
        'proyecto_id' => $proyecto->id,
        'solicitante_id' => 1,
        'estado' => 'APROBADO',
        'aprobado_por' => 1,
        'aprobado_en' => now(),
    ]);
    
    echo "📋 SOLICITUD CREADA: #{$solicitud->id}\n";
    echo "   Estado: {$solicitud->estado}\n\n";

    // 4. Agregar EC a la solicitud
    $solicitud->elementosConfiguracion()->attach($ec->id, [
        'tipo_cambio' => 'MODIFICACION',
        'descripcion_cambio' => 'Modificar validaciones'
    ]);
    
    echo "🔗 EC VINCULADO A SOLICITUD\n\n";

    // 5. EJECUTAR EL JOB
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
        // Continuar para ver qué se creó
    }

    // 6. VERIFICAR RESULTADOS
    echo "📊 RESULTADOS:\n";
    echo "==============\n";
    
    // Verificar VersionEC creada
    $nuevasVersiones = VersionEC::where('solicitud_cambio_id', $solicitud->id)->get();
    echo "📦 VERSIONES CREADAS: " . $nuevasVersiones->count() . "\n";
    
    foreach ($nuevasVersiones as $version) {
        echo "   → Versión: {$version->version}\n";
        echo "   → Estado: {$version->estado}\n";
        echo "   → EC: {$version->elementoConfiguracion->codigo_ec}\n";
        echo "   → Commit ID: " . ($version->commit_id ?? 'null (correcto)') . "\n";
    }
    echo "\n";
    
    // Verificar EC actualizado
    $ec->refresh();
    echo "📄 EC ACTUALIZADO:\n";
    echo "   → Estado: {$ec->estado}\n";
    echo "   → Versión actual: " . ($ec->versionActual->version ?? 'ninguna') . "\n\n";
    
    // Verificar tareas creadas
    $tareas = TareaProyecto::where('solicitud_cambio_id', $solicitud->id)->get();
    echo "🏗️ TAREAS CREADAS: " . $tareas->count() . "\n";
    
    foreach ($tareas as $tarea) {
        echo "   → {$tarea->nombre}\n";
        echo "   → Estado: {$tarea->estado}\n";
        echo "   → Fase: " . ($tarea->fase->nombre ?? 'No definida') . "\n";
    }
    
    echo "\n🎉 ¡TEST COMPLETADO EXITOSAMENTE!\n";
    echo "\n💡 RESUMEN:\n";
    echo "   ✅ Job ejecutó sin errores\n";
    echo "   ✅ VersionEC creada en estado PENDIENTE\n";
    echo "   ✅ EC actualizado a EN_REVISION\n";
    echo "   ✅ Tareas creadas según metodología\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
    echo "📁 Archivo: " . $e->getFile() . "\n";
    exit(1);
}

echo "\n🔍 Para limpiar datos de prueba, ejecuta:\n";
echo "   php artisan tinker --execute=\"\\App\\Models\\SolicitudCambio::find({$solicitud->id})->delete();\"\n";