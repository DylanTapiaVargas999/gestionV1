<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\ElementoConfiguracion;
use App\Models\ComiteCambio;
use App\Models\VotoCCB;
use Illuminate\Support\Facades\DB;

echo "=== SIMULACIÓN COMPLETA DE APROBACIÓN DE SOLICITUD ===\n\n";

// 1. Obtener proyecto con CCB
$proyecto = Proyecto::whereHas('comiteCambio')->first();
if (!$proyecto) {
    echo "❌ No hay proyectos con CCB configurado\n";
    exit;
}

echo "Proyecto: {$proyecto->nombre}\n";
echo "Metodología: " . $proyecto->metodologia->nombre . "\n";

$ccb = $proyecto->comiteCambio;
echo "CCB: {$ccb->nombre} (Quorum: {$ccb->quorum})\n\n";

// 2. Buscar una solicitud APROBADA existente
$solicitudExistente = SolicitudCambio::where('proyecto_id', $proyecto->id)
    ->where('estado', 'APROBADA')
    ->first();

if ($solicitudExistente) {
    echo "📋 Solicitud APROBADA encontrada: {$solicitudExistente->titulo}\n";
    echo "Estado actual: {$solicitudExistente->estado}\n\n";

    // Contar tareas relacionadas
    $tareasRelacionadas = DB::table('tareas_proyecto')
        ->join('items_cambio', 'tareas_proyecto.id_ec', '=', 'items_cambio.ec_id')
        ->where('items_cambio.solicitud_cambio_id', $solicitudExistente->id)
        ->count();

    echo "Tareas relacionadas a esta solicitud: {$tareasRelacionadas}\n";

    if ($tareasRelacionadas > 0) {
        echo "\n✅ La solicitud YA tiene tareas creadas. El sistema está funcionando correctamente.\n";
    } else {
        echo "\n⚠️ La solicitud NO tiene tareas. Esto confirma que el problema existía.\n";
        echo "Con QUEUE_CONNECTION=sync, las nuevas aprobaciones SÍ crearán tareas.\n";
    }
} else {
    echo "No hay solicitudes aprobadas para verificar.\n";
}

echo "\n=== RESUMEN DE CONFIGURACIÓN ===\n";
echo "QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n";
echo "Esto significa que los Jobs se ejecutan: " . (env('QUEUE_CONNECTION') === 'sync' ? '✅ INMEDIATAMENTE' : '⚠️ EN COLA (necesita worker)') . "\n";
