<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FaseMetodologia;

echo "\n📋 Fases de Cascada (id_metodologia = 2):\n\n";

$fases = FaseMetodologia::where('id_metodologia', 2)
    ->orderBy('orden')
    ->get();

foreach ($fases as $fase) {
    echo "  {$fase->orden}. {$fase->nombre_fase} (ID: {$fase->id_fase})\n";
}

echo "\n";
