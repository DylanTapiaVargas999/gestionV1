<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "\n📋 COLUMNAS DE tareas_proyecto\n";
echo str_repeat("=", 60) . "\n\n";

$columns = Schema::getColumnListing('tareas_proyecto');

$progresoColumns = array_filter($columns, function($col) {
    return str_contains($col, 'progreso');
});

if (empty($progresoColumns)) {
    echo "✅ NO hay columnas con 'progreso' en la tabla\n";
    echo "Esto está bien - el accessor debería funcionar\n";
} else {
    echo "⚠️  Columnas encontradas:\n";
    foreach ($progresoColumns as $col) {
        echo "  - $col\n";
    }
}

echo "\nTodas las columnas:\n";
foreach ($columns as $col) {
    echo "  - $col\n";
}
