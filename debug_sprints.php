<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTADO DE SPRINTS ===\n";

$sprints = DB::table('sprints')
    ->select('id_sprint', 'nombre', 'estado', 'id_proyecto')
    ->get();

foreach ($sprints as $sprint) {
    echo "Sprint {$sprint->id_sprint}: {$sprint->nombre} - Estado: {$sprint->estado} - Proyecto: {$sprint->id_proyecto}\n";
}

echo "\n=== SPRINTS ACTIVOS ===\n";
$sprintsActivos = DB::table('sprints')
    ->where('estado', 'activo')
    ->get();

if ($sprintsActivos->count() > 0) {
    foreach ($sprintsActivos as $sprint) {
        echo "ACTIVO: Sprint {$sprint->id_sprint}: {$sprint->nombre}\n";
    }
} else {
    echo "No hay sprints activos\n";
}
