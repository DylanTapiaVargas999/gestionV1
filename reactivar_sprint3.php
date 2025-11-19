<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REACTIVANDO SPRINT 3 ===\n";

// Cambiar Sprint 5 a planificado y Sprint 3 a activo
DB::table('sprints')
    ->where('id_sprint', 11) // Sprint 5
    ->update(['estado' => 'planificado']);

DB::table('sprints')
    ->where('id_sprint', 3) // Sprint 3
    ->update(['estado' => 'activo']);

echo "✅ Sprint 3 reactivado\n";
echo "✅ Sprint 5 pasado a planificado\n";
echo "\nAhora Sprint Board debería mostrar las 4 User Stories del Sprint 3\n";
