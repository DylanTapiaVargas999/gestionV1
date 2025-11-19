<?php

/**
 * Script para crear tareas de ejemplo en Cascada
 *
 * Uso: php tools/crear_tareas_cascada.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Proyecto;
use App\Models\TareaProyecto;
use App\Models\FaseMetodologia;
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

// Verificar si ya existen tareas
$tareasExistentes = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('horas_estimadas')
    ->count();

echo "\n📊 Tareas existentes en el proyecto: {$tareasExistentes}\n";

if ($tareasExistentes > 0) {
    echo "\n⚠️  Ya existen tareas. ¿Deseas crear más? (y/n): ";
    $respuesta = trim(fgets(STDIN));
    if (strtolower($respuesta) !== 'y') {
        echo "❌ Cancelado por el usuario.\n";
        exit(0);
    }
}

// Tareas de ejemplo para cada fase
$tareasEjemplo = [
    'Requisitos' => [
        ['nombre' => 'Reunión con stakeholders', 'horas' => 8, 'prioridad' => 10, 'estado' => 'Completada'],
        ['nombre' => 'Documentar requisitos funcionales', 'horas' => 16, 'prioridad' => 10, 'estado' => 'Completada'],
        ['nombre' => 'Validar requisitos con el cliente', 'horas' => 4, 'prioridad' => 8, 'estado' => 'En Revisión'],
    ],
    'Análisis' => [
        ['nombre' => 'Analizar viabilidad técnica', 'horas' => 12, 'prioridad' => 9, 'estado' => 'Completada'],
        ['nombre' => 'Identificar riesgos del proyecto', 'horas' => 8, 'prioridad' => 8, 'estado' => 'En Progreso'],
        ['nombre' => 'Definir alcance del proyecto', 'horas' => 6, 'prioridad' => 10, 'estado' => 'En Progreso'],
    ],
    'Diseño' => [
        ['nombre' => 'Diseñar arquitectura de software', 'horas' => 20, 'prioridad' => 10, 'estado' => 'En Progreso'],
        ['nombre' => 'Crear diagramas UML', 'horas' => 12, 'prioridad' => 8, 'estado' => 'Pendiente'],
        ['nombre' => 'Diseñar base de datos', 'horas' => 16, 'prioridad' => 9, 'estado' => 'Pendiente'],
    ],
    'Implementación' => [
        ['nombre' => 'Configurar entorno de desarrollo', 'horas' => 8, 'prioridad' => 9, 'estado' => 'Pendiente'],
        ['nombre' => 'Implementar módulo de autenticación', 'horas' => 24, 'prioridad' => 10, 'estado' => 'Pendiente'],
        ['nombre' => 'Desarrollar API REST', 'horas' => 40, 'prioridad' => 8, 'estado' => 'Pendiente'],
    ],
    'Pruebas' => [
        ['nombre' => 'Crear casos de prueba unitarios', 'horas' => 16, 'prioridad' => 9, 'estado' => 'Pendiente'],
        ['nombre' => 'Ejecutar pruebas de integración', 'horas' => 12, 'prioridad' => 8, 'estado' => 'Pendiente'],
        ['nombre' => 'Realizar pruebas de aceptación', 'horas' => 8, 'prioridad' => 10, 'estado' => 'Pendiente'],
    ],
    'Despliegue' => [
        ['nombre' => 'Preparar entorno de producción', 'horas' => 10, 'prioridad' => 10, 'estado' => 'Pendiente'],
        ['nombre' => 'Realizar despliegue a producción', 'horas' => 6, 'prioridad' => 10, 'estado' => 'Pendiente'],
        ['nombre' => 'Validar funcionalidad en producción', 'horas' => 4, 'prioridad' => 9, 'estado' => 'Pendiente'],
    ],
    'Mantenimiento' => [
        ['nombre' => 'Documentar manual de usuario', 'horas' => 12, 'prioridad' => 7, 'estado' => 'Pendiente'],
        ['nombre' => 'Capacitar al equipo de soporte', 'horas' => 8, 'prioridad' => 6, 'estado' => 'Pendiente'],
        ['nombre' => 'Configurar monitoreo en producción', 'horas' => 4, 'prioridad' => 8, 'estado' => 'Pendiente'],
    ],
];

echo "\n🚀 Creando tareas de ejemplo...\n\n";

DB::beginTransaction();

try {
    $totalCreadas = 0;

    foreach ($fases as $fase) {
        $nombreFase = $fase->nombre_fase;

        if (!isset($tareasEjemplo[$nombreFase])) {
            continue;
        }

        echo "📌 Fase: {$nombreFase}\n";

        foreach ($tareasEjemplo[$nombreFase] as $tareaData) {
            // Calcular fechas (cada tarea dura aproximadamente sus horas estimadas / 8 días)
            $diasDuracion = ceil($tareaData['horas'] / 8);
            $fechaInicio = now()->addDays($fase->orden * 10); // Separar fases por 10 días
            $fechaFin = $fechaInicio->copy()->addDays($diasDuracion);

            $tarea = TareaProyecto::create([
                'id_proyecto' => $proyecto->id,
                'id_fase' => $fase->id_fase,
                'nombre' => $tareaData['nombre'],
                'descripcion' => "Actividad de la fase {$nombreFase} para el proyecto {$proyecto->nombre_proyecto}",
                'horas_estimadas' => $tareaData['horas'],
                'prioridad' => $tareaData['prioridad'],
                'estado' => $tareaData['estado'],
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
            ]);

            echo "   ✅ {$tarea->nombre} ({$tarea->estado}) - {$tareaData['horas']}h\n";
            $totalCreadas++;
        }

        echo "\n";
    }

    DB::commit();

    echo "✨ ¡Proceso completado!\n";
    echo "📊 Total de tareas creadas: {$totalCreadas}\n";
    echo "\n🔗 Accede a: /proyectos/{$proyecto->id}/cascada/dashboard\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error al crear tareas: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
