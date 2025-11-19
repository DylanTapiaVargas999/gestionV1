<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNÓSTICO DE ELEMENTOS DE CONFIGURACIÓN\n";
echo "==============================================\n\n";

$ecs = App\Models\ElementoConfiguracion::all();

echo "📊 Total de ECs: " . $ecs->count() . "\n\n";

echo "📦 Tipos únicos encontrados en la BD:\n";
$tiposUnicos = $ecs->pluck('tipo')->unique()->sort();
foreach ($tiposUnicos as $tipo) {
    $count = $ecs->where('tipo', $tipo)->count();
    echo "  - '{$tipo}': {$count} elementos\n";
}

echo "\n🔎 Búsqueda por tipos esperados en el controlador:\n";
$tiposEsperados = [
    'codigo_fuente' => 'Código Fuente',
    'documentacion' => 'Documentación',
    'script_bd' => 'Scripts BD',
    'caso_prueba' => 'Casos de Prueba',
    'configuracion' => 'Configuración'
];

foreach ($tiposEsperados as $tipoKey => $tipoNombre) {
    $count = $ecs->where('tipo', $tipoKey)->count();
    echo "  - '{$tipoKey}' ({$tipoNombre}): {$count} elementos\n";
}

echo "\n🔧 PROBLEMA IDENTIFICADO:\n";
echo "========================================\n";
echo "El controlador busca tipos con snake_case: 'codigo_fuente', 'script_bd'\n";
echo "Pero la BD tiene tipos con espacios: 'Codigo Fuente', 'Script BD'\n";
echo "\n❌ SOLUCIÓN: Normalizar la búsqueda en el controlador\n";
