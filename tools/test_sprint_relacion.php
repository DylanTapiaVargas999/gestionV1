<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sprint;

echo "=== TEST MODELO SPRINT ===\n\n";

// Intentar cargar un Sprint directamente
$sprint = Sprint::where('id_sprint', 3)->first();

if (!$sprint) {
    echo "❌ No se pudo cargar Sprint con id_sprint=3\n";
    exit(1);
}

echo "✅ Sprint cargado correctamente:\n";
echo "  id_sprint: {$sprint->id_sprint}\n";
echo "  nombre: {$sprint->nombre}\n";
echo "  getKey(): {$sprint->getKey()}\n";
echo "  getKeyName(): {$sprint->getKeyName()}\n";
echo "  timestamps: " . ($sprint->timestamps ? 'true' : 'false') . "\n";
echo "  CREATED_AT: " . Sprint::CREATED_AT . "\n";
echo "  UPDATED_AT: " . Sprint::UPDATED_AT . "\n";

// Cargar tareas de este sprint
echo "\n=== RELACIÓN INVERSA (Sprint -> Tareas) ===\n";
$tareas = $sprint->tareas;
echo "Tareas en este sprint: {$tareas->count()}\n";

if ($tareas->count() > 0) {
    echo "\n✅ La relación inversa (Sprint->tareas) SÍ funciona\n";
    echo "Primera tarea: {$tareas->first()->nombre}\n";

    // Ahora intentar la relación directa (Tarea -> Sprint)
    $primeraTarea = $tareas->first();
    echo "\n=== RELACIÓN DIRECTA (Tarea -> Sprint) ===\n";
    echo "Tarea: {$primeraTarea->nombre}\n";
    echo "  id_sprint en DB: {$primeraTarea->getAttributes()['id_sprint']}\n";

    $sprintDeLaTarea = $primeraTarea->sprint;
    if ($sprintDeLaTarea) {
        echo "✅ Relación sprint() funcionó!\n";
        echo "  Sprint: {$sprintDeLaTarea->nombre}\n";
    } else {
        echo "❌ Relación sprint() retorna NULL\n";
        echo "\nDebug de belongsTo:\n";
        $relation = $primeraTarea->sprint();
        echo "  Foreign Key: {$relation->getForeignKeyName()}\n";
        echo "  Owner Key: {$relation->getOwnerKeyName()}\n";
        echo "  Parent: " . get_class($relation->getRelated()) . "\n";
    }
}
