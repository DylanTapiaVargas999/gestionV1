<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\FaseMetodologia;

echo "=== PRUEBA DE BÚSQUEDA DE FASES ===\n\n";

// Obtener un proyecto Scrum
$proyectoScrum = Proyecto::where('id_metodologia', 1)->first();
echo "Proyecto Scrum: {$proyectoScrum->nombre}\n";
echo "ID Metodología del proyecto: {$proyectoScrum->id_metodologia}\n\n";

// Intentar buscar fase como lo hace el Job
echo "Buscando 'Product Backlog' con id_metodologia = {$proyectoScrum->id_metodologia}\n";
$faseBacklog = FaseMetodologia::where('id_metodologia', $proyectoScrum->id_metodologia)
    ->where('nombre_fase', 'Product Backlog')
    ->first();

if ($faseBacklog) {
    echo "✅ FASE ENCONTRADA:\n";
    echo "   ID: {$faseBacklog->id_fase}\n";
    echo "   Nombre: {$faseBacklog->nombre_fase}\n";
    echo "   ID Metodología: {$faseBacklog->id_metodologia}\n";
} else {
    echo "❌ FASE NO ENCONTRADA\n";
    echo "Buscando TODAS las fases de esta metodología:\n";
    $fases = FaseMetodologia::where('id_metodologia', $proyectoScrum->id_metodologia)->get();
    foreach ($fases as $f) {
        echo "   - {$f->nombre_fase} (ID: {$f->id_fase})\n";
    }
}

echo "\n=== PRUEBA PROYECTO CASCADA ===\n\n";
$proyectoCascada = Proyecto::where('id_metodologia', 2)->first();
echo "Proyecto Cascada: {$proyectoCascada->nombre}\n";
echo "ID Metodología del proyecto: {$proyectoCascada->id_metodologia}\n\n";

echo "Buscando 'Implementación' con id_metodologia = {$proyectoCascada->id_metodologia}\n";
$faseImpl = FaseMetodologia::where('id_metodologia', $proyectoCascada->id_metodologia)
    ->where('nombre_fase', 'Implementación')
    ->first();

if ($faseImpl) {
    echo "✅ FASE ENCONTRADA:\n";
    echo "   ID: {$faseImpl->id_fase}\n";
    echo "   Nombre: {$faseImpl->nombre_fase}\n";
    echo "   ID Metodología: {$faseImpl->id_metodologia}\n";
} else {
    echo "❌ FASE NO ENCONTRADA\n";
}
