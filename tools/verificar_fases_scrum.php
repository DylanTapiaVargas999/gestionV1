<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Metodologia;
use App\Models\FaseMetodologia;

echo "=== FASES DE SCRUM ===\n\n";

$scrum = Metodologia::where('nombre', 'Scrum')->first();

if (!$scrum) {
    echo "❌ Metodología Scrum no encontrada\n";
    exit(1);
}

echo "✅ Metodología: {$scrum->nombre} (ID: {$scrum->id_metodologia})\n\n";

$fases = FaseMetodologia::where('id_metodologia', $scrum->id_metodologia)
    ->orderBy('orden')
    ->get();

echo "📊 Total fases: " . $fases->count() . "\n\n";

foreach ($fases as $fase) {
    echo "Fase #{$fase->id_fase}: {$fase->nombre_fase} (Orden: {$fase->orden})\n";
    echo "  Descripción: {$fase->descripcion}\n\n";
}
