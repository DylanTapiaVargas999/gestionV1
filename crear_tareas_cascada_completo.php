<?php

/**
 * Script para crear tareas de ejemplo en Cascada CON Elementos de Configuración
 *
 * Uso: php crear_tareas_cascada_completo.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
use App\Models\ElementoConfiguracion;
use Illuminate\Support\Facades\DB;

echo "\n🔍 Buscando proyectos con metodología Cascada...\n";

// Buscar proyectos con metodología Cascada (id_metodologia = 2)
$proyectosCascada = Proyecto::where('id_metodologia', 2)->get();

if ($proyectosCascada->isEmpty()) {
    echo "❌ No se encontraron proyectos con metodología Cascada.\n";
    exit(1);
}

echo "✅ Se encontraron " . $proyectosCascada->count() . " proyecto(s) con Cascada:\n";
foreach ($proyectosCascada as $p) {
    echo "   - #{$p->id}: {$p->nombre_proyecto}\n";
}

// Seleccionar el primer proyecto
$proyecto = $proyectosCascada->first();
echo "\n📁 Trabajando con: {$proyecto->nombre_proyecto} (ID: {$proyecto->id})\n";

// Obtener fases de Cascada
$fases = FaseMetodologia::where('id_metodologia', 2)
    ->orderBy('orden')
    ->get();

echo "\n📋 Fases de Cascada encontradas:\n";
foreach ($fases as $fase) {
    echo "   {$fase->orden}. {$fase->nombre_fase} (ID: {$fase->id_fase})\n";
}

// Obtener elementos de configuración del proyecto
$ecs = ElementoConfiguracion::where('proyecto_id', $proyecto->id)
    ->orderBy('codigo_ec')
    ->get();

echo "\n📦 Elementos de Configuración (EC) disponibles: {$ecs->count()}\n";
if ($ecs->count() > 0) {
    foreach ($ecs->take(5) as $ec) {
        echo "   - {$ec->codigo_ec}: {$ec->nombre_ec}\n";
    }
    if ($ecs->count() > 5) {
        echo "   ... y " . ($ecs->count() - 5) . " más\n";
    }
} else {
    echo "   ⚠️  No hay ECs. Las tareas se crearán sin EC asociado.\n";
}

// Verificar si ya existen tareas
$tareasExistentes = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('horas_estimadas')
    ->count();

echo "\n📊 Tareas existentes en el proyecto: {$tareasExistentes}\n";

if ($tareasExistentes > 0) {
    echo "\n⚠️  Ya existen {$tareasExistentes} tareas. ¿Deseas crear más? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $respuesta = trim(fgets($handle));
    fclose($handle);

    if (strtolower($respuesta) !== 'y') {
        echo "❌ Cancelado por el usuario.\n";
        exit(0);
    }
}

// Tareas de ejemplo para cada fase (con posibles ECs)
$tareasEjemplo = [
    'Requisitos' => [
        ['nombre' => 'Reunión con stakeholders', 'horas' => 8, 'prioridad' => 10, 'estado' => 'Completada', 'ec_keyword' => 'DOC'],
        ['nombre' => 'Documentar requisitos funcionales', 'horas' => 16, 'prioridad' => 10, 'estado' => 'Completada', 'ec_keyword' => 'DOC'],
        ['nombre' => 'Validar requisitos con el cliente', 'horas' => 4, 'prioridad' => 8, 'estado' => 'En Revisión', 'ec_keyword' => 'DOC'],
    ],
    'Análisis' => [
        ['nombre' => 'Analizar viabilidad técnica', 'horas' => 12, 'prioridad' => 9, 'estado' => 'Completada', 'ec_keyword' => 'PLAN'],
        ['nombre' => 'Identificar riesgos del proyecto', 'horas' => 8, 'prioridad' => 8, 'estado' => 'En Progreso', 'ec_keyword' => 'DOC'],
        ['nombre' => 'Definir alcance del proyecto', 'horas' => 6, 'prioridad' => 10, 'estado' => 'En Progreso', 'ec_keyword' => 'PLAN'],
    ],
    'Diseño' => [
        ['nombre' => 'Diseñar arquitectura de software', 'horas' => 20, 'prioridad' => 10, 'estado' => 'En Progreso', 'ec_keyword' => 'DIS'],
        ['nombre' => 'Crear diagramas UML', 'horas' => 12, 'prioridad' => 8, 'estado' => 'Pendiente', 'ec_keyword' => 'DIS'],
        ['nombre' => 'Diseñar base de datos', 'horas' => 16, 'prioridad' => 9, 'estado' => 'Pendiente', 'ec_keyword' => 'DB'],
    ],
    'Implementación' => [
        ['nombre' => 'Configurar entorno de desarrollo', 'horas' => 8, 'prioridad' => 9, 'estado' => 'Pendiente', 'ec_keyword' => 'CONFIG'],
        ['nombre' => 'Implementar módulo de autenticación', 'horas' => 24, 'prioridad' => 10, 'estado' => 'Pendiente', 'ec_keyword' => 'AUTH'],
        ['nombre' => 'Desarrollar API REST', 'horas' => 40, 'prioridad' => 8, 'estado' => 'Pendiente', 'ec_keyword' => 'API'],
        ['nombre' => 'Integrar base de datos', 'horas' => 16, 'prioridad' => 9, 'estado' => 'Pendiente', 'ec_keyword' => 'DB'],
    ],
    'Pruebas' => [
        ['nombre' => 'Crear casos de prueba unitarios', 'horas' => 16, 'prioridad' => 9, 'estado' => 'Pendiente', 'ec_keyword' => 'TEST'],
        ['nombre' => 'Ejecutar pruebas de integración', 'horas' => 12, 'prioridad' => 8, 'estado' => 'Pendiente', 'ec_keyword' => 'TEST'],
        ['nombre' => 'Realizar pruebas de aceptación', 'horas' => 8, 'prioridad' => 10, 'estado' => 'Pendiente', 'ec_keyword' => 'QA'],
    ],
    'Despliegue' => [
        ['nombre' => 'Preparar entorno de producción', 'horas' => 10, 'prioridad' => 10, 'estado' => 'Pendiente', 'ec_keyword' => 'DEPLOY'],
        ['nombre' => 'Realizar despliegue a producción', 'horas' => 6, 'prioridad' => 10, 'estado' => 'Pendiente', 'ec_keyword' => 'DEPLOY'],
        ['nombre' => 'Validar funcionalidad en producción', 'horas' => 4, 'prioridad' => 9, 'estado' => 'Pendiente', 'ec_keyword' => 'QA'],
    ],
    'Mantenimiento' => [
        ['nombre' => 'Documentar manual de usuario', 'horas' => 12, 'prioridad' => 7, 'estado' => 'Pendiente', 'ec_keyword' => 'DOC'],
        ['nombre' => 'Capacitar al equipo de soporte', 'horas' => 8, 'prioridad' => 6, 'estado' => 'Pendiente', 'ec_keyword' => null],
        ['nombre' => 'Configurar monitoreo en producción', 'horas' => 4, 'prioridad' => 8, 'estado' => 'Pendiente', 'ec_keyword' => 'CONFIG'],
    ],
];

echo "\n🚀 Creando tareas de ejemplo...\n\n";

DB::beginTransaction();

try {
    $totalCreadas = 0;
    $tareasConEC = 0;

    foreach ($fases as $fase) {
        $nombreFase = $fase->nombre_fase;

        if (!isset($tareasEjemplo[$nombreFase])) {
            continue;
        }

        echo "📌 Fase: {$nombreFase}\n";

        foreach ($tareasEjemplo[$nombreFase] as $tareaData) {
            // Buscar un EC que coincida con el keyword
            $ecAsociado = null;
            if ($tareaData['ec_keyword'] && $ecs->count() > 0) {
                $ecAsociado = $ecs->first(function($ec) use ($tareaData) {
                    return stripos($ec->codigo_ec, $tareaData['ec_keyword']) !== false ||
                           stripos($ec->nombre_ec, $tareaData['ec_keyword']) !== false;
                });

                // Si no encuentra por keyword, asignar uno aleatorio
                if (!$ecAsociado) {
                    $ecAsociado = $ecs->random();
                }
            }

            // Calcular fechas (cada tarea dura aproximadamente sus horas estimadas / 8 días)
            $diasDuracion = ceil($tareaData['horas'] / 8);
            $fechaInicio = now()->addDays($fase->orden * 10); // Separar fases por 10 días
            $fechaFin = $fechaInicio->copy()->addDays($diasDuracion);

            $dataTarea = [
                'id_proyecto' => $proyecto->id,
                'id_fase' => $fase->id_fase,
                'nombre' => $tareaData['nombre'],
                'descripcion' => "Actividad de la fase {$nombreFase} para el proyecto {$proyecto->nombre_proyecto}",
                'horas_estimadas' => $tareaData['horas'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
            ];

            if ($ecAsociado) {
                $dataTarea['id_ec'] = $ecAsociado->id;
            }

            $tarea = TareaProyecto::create($dataTarea);

            $ecInfo = $ecAsociado ? " → 📦 {$ecAsociado->codigo_ec}" : "";
            echo "   ✅ {$tarea->nombre} ({$tarea->estado}) - {$tareaData['horas']}h{$ecInfo}\n";

            $totalCreadas++;
            if ($ecAsociado) $tareasConEC++;
        }

        echo "\n";
    }

    DB::commit();

    echo "✨ ¡Proceso completado!\n";
    echo "📊 Total de tareas creadas: {$totalCreadas}\n";
    echo "📦 Tareas con EC asociado: {$tareasConEC}\n";
    echo "📋 Tareas sin EC: " . ($totalCreadas - $tareasConEC) . "\n";
    echo "\n🔗 Accede a: /proyectos/{$proyecto->id}/cascada/dashboard\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error al crear tareas: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
