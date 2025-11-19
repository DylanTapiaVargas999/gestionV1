<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

$proyecto = App\Models\Proyecto::where('codigo', 'ERP-2024')->first();
if (!$proyecto) { echo "Proyecto ERP-2024 no encontrado\n"; exit; }
$tareas = $proyecto->tareas()->with(['fase','responsableUsuario'])->get();
$hoy = Carbon::now();

foreach ($tareas->take(10) as $tarea) {
    $estado = strtolower(trim($tarea->estado ?? ''));
    $estaCompletada = in_array($estado, ['completada', 'done', 'completado', 'finalizada', 'finished']);
    $dias = null;
    if ($tarea->fecha_fin && !$estaCompletada) {
        $diff = $hoy->diffInDays(Carbon::parse($tarea->fecha_fin), false);
        $dias = (int) round($diff);
    }
    echo sprintf("- %s | venc: %s | estado: %s | dias_restantes: %s\n",
        $tarea->nombre,
        $tarea->fecha_fin ?: 'N/A',
        $tarea->estado ?: 'N/A',
        $dias === null ? 'null' : $dias
    );
}
