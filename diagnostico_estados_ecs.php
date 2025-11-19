<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNÓSTICO DE ESTADOS DE ECs\n";
echo "===================================\n\n";

$ecs = App\Models\ElementoConfiguracion::all();

echo "📊 Estados únicos encontrados en la BD:\n";
$estadosUnicos = $ecs->pluck('estado')->unique()->sort();
foreach ($estadosUnicos as $estado) {
    $count = $ecs->where('estado', $estado)->count();
    echo "  - '{$estado}': {$count} elementos\n";
}

echo "\n🔎 Búsqueda por estados esperados en el controlador:\n";
$estadosEsperados = [
    'aprobado' => 'Aprobados',
    'liberado' => 'Liberados',
    'en_revision' => 'En Revisión',
    'obsoleto' => 'Obsoletos'
];

foreach ($estadosEsperados as $estadoKey => $estadoNombre) {
    $count = $ecs->where('estado', $estadoKey)->count();
    echo "  - '{$estadoKey}' ({$estadoNombre}): {$count} elementos\n";
}
