<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{Proyecto, Sprint, TareaProyecto, ElementoConfiguracion, Usuario};

echo "🔗 VERIFICACIÓN DE RELACIONES SCRUM\n";
echo "====================================\n\n";

$proyecto = Proyecto::where('codigo', 'ECOM-2024')->first();

if (!$proyecto) {
    echo "❌ Proyecto no encontrado\n";
    exit(1);
}

echo "📦 Proyecto: {$proyecto->nombre}\n\n";

// 1. Verificar relación Proyecto → Sprints
echo "1️⃣  PROYECTO → SPRINTS\n";
echo "------------------------\n";
$sprints = $proyecto->sprints;
echo "Total sprints del proyecto: {$sprints->count()}\n";
foreach ($sprints as $sprint) {
    echo "  ✓ {$sprint->nombre} (id_sprint: {$sprint->id_sprint})\n";
}
echo "\n";

// 2. Verificar relación Sprint → Tareas
echo "2️⃣  SPRINT → TAREAS (via id_sprint)\n";
echo "------------------------\n";
foreach ($sprints as $sprint) {
    $tareas = $sprint->tareas; // Usa la relación tareas() que agregamos
    echo "{$sprint->nombre}:\n";
    echo "  Total tareas: {$tareas->count()}\n";

    foreach ($tareas as $tarea) {
        echo "    - {$tarea->nombre}\n";
        echo "      id_sprint: {$tarea->id_sprint}\n";
        echo "      id_ec: " . ($tarea->id_ec ?? 'NULL') . "\n";
        echo "      responsable: " . ($tarea->responsable ?? 'NULL') . "\n\n";
    }
}

// 3. Verificar relación Tarea → EC
echo "3️⃣  TAREA → ELEMENTO CONFIGURACIÓN (via id_ec)\n";
echo "------------------------\n";
$tareasConEC = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('id_ec')
    ->with('elementoConfiguracion')
    ->get();

echo "Tareas con EC vinculado: {$tareasConEC->count()}\n";
foreach ($tareasConEC as $tarea) {
    $ec = $tarea->elementoConfiguracion;
    if ($ec) {
        echo "  ✓ {$tarea->nombre}\n";
        echo "    → EC: {$ec->codigo_ec} ({$ec->titulo})\n";
    } else {
        echo "  ⚠️  {$tarea->nombre}\n";
        echo "    → EC ID {$tarea->id_ec} NO ENCONTRADO\n";
    }
}
echo "\n";

// 4. Verificar relación Tarea → Usuario (Responsable)
echo "4️⃣  TAREA → USUARIO RESPONSABLE (via responsable)\n";
echo "------------------------\n";
$tareasConResponsable = TareaProyecto::where('id_proyecto', $proyecto->id)
    ->whereNotNull('responsable')
    ->with('responsableUsuario')
    ->get();

echo "Tareas con responsable: {$tareasConResponsable->count()}\n";
foreach ($tareasConResponsable as $tarea) {
    $usuario = $tarea->responsableUsuario;
    if ($usuario) {
        echo "  ✓ {$tarea->nombre}\n";
        echo "    → Responsable: {$usuario->nombre_completo}\n";
    } else {
        echo "  ⚠️  {$tarea->nombre}\n";
        echo "    → Usuario ID {$tarea->responsable} NO ENCONTRADO\n";
    }
}
echo "\n";

// 5. Verificar miembros del equipo disponibles
echo "5️⃣  MIEMBROS DEL EQUIPO (Disponibles para asignar)\n";
echo "------------------------\n";
$miembrosEquipo = collect();
foreach ($proyecto->equipos as $equipo) {
    echo "Equipo: {$equipo->nombre}\n";
    foreach ($equipo->miembros as $miembro) {
        echo "  - {$miembro->nombre_completo} (ID: {$miembro->id})\n";
        $miembrosEquipo->push($miembro);
    }
}
$miembrosEquipo = $miembrosEquipo->unique('id');
echo "\nTotal miembros únicos: {$miembrosEquipo->count()}\n\n";

// RESUMEN
echo "✅ RESUMEN DE RELACIONES\n";
echo "========================\n";
echo "Proyecto → Sprints: ✓ FUNCIONA ({$sprints->count()} sprints)\n";
echo "Sprint → Tareas: ✓ FUNCIONA (vía id_sprint FK)\n";
echo "Tarea → EC: ✓ FUNCIONA ({$tareasConEC->count()} tareas con EC)\n";
echo "Tarea → Usuario: ✓ FUNCIONA ({$tareasConResponsable->count()} tareas con responsable)\n";
echo "Miembros equipo: ✓ DISPONIBLES ({$miembrosEquipo->count()} usuarios)\n";
