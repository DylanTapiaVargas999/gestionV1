<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== METODOLOGÍAS ===\n";
$metodologias = DB::table('metodologias')->get(['id_metodologia', 'nombre']);
foreach ($metodologias as $m) {
    echo "ID: {$m->id_metodologia} - Nombre: {$m->nombre}\n";
}

echo "\n=== FASES DE METODOLOGÍA ===\n";
$fases = DB::table('fases_metodologia')->get(['id_fase', 'id_metodologia', 'nombre_fase', 'orden']);
foreach ($fases as $f) {
    echo "ID Fase: {$f->id_fase} - ID Metodología: {$f->id_metodologia} - Nombre: {$f->nombre_fase} - Orden: {$f->orden}\n";
}

echo "\n=== PROYECTOS ===\n";
$proyectos = DB::table('proyectos')->get(['id', 'nombre', 'id_metodologia']);
foreach ($proyectos as $p) {
    echo "ID: {$p->id} - Nombre: {$p->nombre} - ID Metodología: {$p->id_metodologia}\n";
}

echo "\n=== TAREAS DE PROYECTO ===\n";
$tareas = DB::table('tareas_proyecto')->count();
echo "Total tareas: {$tareas}\n";
