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

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║     PRUEBA COMPLETA: APROBAR SOLICITUD Y CREAR TAREAS           ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Paso 1: Seleccionar proyecto
$proyectos = Proyecto::with('metodologia')->get();
echo "📋 PROYECTOS DISPONIBLES:\n";
foreach ($proyectos as $index => $p) {
    echo ($index + 1) . ". {$p->nombre} ({$p->metodologia->nombre})\n";
}

// Usemos el proyecto E-Commerce (Scrum)
$proyecto = Proyecto::where('nombre', 'LIKE', '%E-Commerce%')->first();
if (!$proyecto) {
    $proyecto = Proyecto::where('id_metodologia', 1)->first();
}

echo "\n✅ Proyecto seleccionado: {$proyecto->nombre}\n";
echo "   Metodología: {$proyecto->metodologia->nombre}\n\n";

// Paso 2: Obtener EC del proyecto
$ec = ElementoConfiguracion::where('proyecto_id', $proyecto->id)->first();
if (!$ec) {
    echo "❌ No hay ECs en el proyecto\n";
    exit;
}

echo "📦 EC seleccionado: {$ec->codigo_ec} - {$ec->titulo}\n\n";

// Paso 3: Contar tareas actuales
$tareasAntes = TareaProyecto::where('id_proyecto', $proyecto->id)->count();
echo "📊 Tareas ANTES: {$tareasAntes}\n\n";

// Paso 4: Crear solicitud de cambio
echo "🆕 Creando solicitud de cambio...\n";
DB::beginTransaction();

try {
    $usuario = DB::table('usuarios')->first();

    $solicitud = new SolicitudCambio();
    $solicitud->id = Str::uuid()->toString();
    $solicitud->proyecto_id = $proyecto->id;
    $solicitud->titulo = 'PRUEBA REAL: Mejora en módulo de pagos';
    $solicitud->descripcion_cambio = 'Implementar nuevo método de pago con criptomonedas';
    $solicitud->motivo_cambio = 'Solicitud del cliente para expandir opciones de pago';
    $solicitud->prioridad = 'ALTA';
    $solicitud->estado = 'APROBADA';
    $solicitud->solicitante_id = $usuario->id;
    $solicitud->aprobado_por = $usuario->id;
    $solicitud->aprobado_en = now();
    $solicitud->save();

    echo "   ✅ Solicitud creada: {$solicitud->titulo}\n";

    // Paso 5: Crear item de cambio
    $item = new ItemCambio();
    $item->id = Str::uuid()->toString();
    $item->solicitud_cambio_id = $solicitud->id;
    $item->ec_id = $ec->id;
    $item->nota = 'Agregar integración con Coinbase y Binance Pay';
    $item->save();

    echo "   ✅ Item de cambio creado para EC: {$ec->codigo_ec}\n\n";

    // Paso 6: Ejecutar el Job (ahora con QUEUE_CONNECTION=sync se ejecuta inmediatamente)
    echo "🚀 Ejecutando Job de implementación...\n";
    ImplementarSolicitudAprobadaJob::dispatch($solicitud);
    echo "   ✅ Job ejecutado\n\n";

    // Paso 7: Verificar resultados
    $tareasDespues = TareaProyecto::where('id_proyecto', $proyecto->id)->count();
    $tareasCreadas = $tareasDespues - $tareasAntes;

    echo "📊 RESULTADOS:\n";
    echo "   Tareas DESPUÉS: {$tareasDespues}\n";
    echo "   Tareas CREADAS: {$tareasCreadas}\n\n";

    if ($tareasCreadas > 0) {
        echo "✅ ¡ÉXITO! Las tareas se crearon correctamente\n\n";

        // Mostrar las tareas creadas
        $tareasNuevas = TareaProyecto::where('id_proyecto', $proyecto->id)
            ->where('id_ec', $ec->id)
            ->orderBy('id_tarea', 'desc')
            ->limit($tareasCreadas)
            ->get();

        echo "📝 TAREAS CREADAS:\n";
        foreach ($tareasNuevas as $t) {
            echo "   ┌─ Tarea #{$t->id_tarea}\n";
            echo "   │  Nombre: {$t->nombre}\n";
            echo "   │  Estado: {$t->estado}\n";
            echo "   │  Fase ID: {$t->id_fase}\n";
            if ($t->story_points) {
                echo "   │  Story Points: {$t->story_points}\n";
            }
            if ($t->horas_estimadas) {
                echo "   │  Horas: {$t->horas_estimadas}\n";
            }
            echo "   └─\n";
        }

        echo "\n";

        // Verificar la versión del EC
        $ec->refresh();
        if ($ec->version_actual_id) {
            $version = DB::table('versiones_ec')->where('id', $ec->version_actual_id)->first();
            echo "📌 VERSIÓN DE EC ACTUALIZADA:\n";
            echo "   EC: {$ec->codigo_ec}\n";
            echo "   Versión: {$version->version}\n";
            echo "   Estado: {$version->estado}\n";
            echo "\n";
        }
    } else {
        echo "❌ ERROR: No se crearon tareas\n";
        echo "Revisa los logs en storage/logs/laravel.log\n\n";
    }

    DB::commit();

    echo "╔═══════════════════════════════════════════════════════════════════╗\n";
    echo "║                  ✅ PRUEBA COMPLETADA CON ÉXITO                  ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "💡 NOTA: Esta fue una prueba REAL. Los datos fueron guardados en la BD.\n";
    echo "   Puedes ver la nueva solicitud y tareas en la aplicación web.\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
