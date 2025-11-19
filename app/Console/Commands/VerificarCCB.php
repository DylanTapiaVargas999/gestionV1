<?php

namespace App\Console\Commands;

use App\Models\ComiteCambio;
use App\Models\Proyecto;
use Illuminate\Console\Command;

class VerificarCCB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccb:verificar {proyecto?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar el estado del CCB para un proyecto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $proyectoId = $this->argument('proyecto');

        if ($proyectoId) {
            $proyecto = Proyecto::find($proyectoId);
        } else {
            $proyecto = Proyecto::first();
        }

        if (!$proyecto) {
            $this->error('No se encontró el proyecto');
            return;
        }

        $this->info("Proyecto: {$proyecto->nombre} (ID: {$proyecto->id})");

        $ccb = $proyecto->hasOne(ComiteCambio::class, 'proyecto_id')->first();

        if (!$ccb) {
            $this->error('No hay CCB configurado para este proyecto');
            return;
        }

        $this->info("CCB: {$ccb->nombre} (ID: {$ccb->id})");
        $this->info("Quorum: {$ccb->quorum}");

        $miembros = $ccb->miembros;
        $this->info("Total miembros: {$miembros->count()}");

        if ($miembros->count() > 0) {
            $this->info("Miembros:");
            foreach ($miembros as $miembro) {
                $this->line("  - {$miembro->name} (ID: {$miembro->id})");
            }
        } else {
            $this->warn("No hay miembros en el CCB");
        }

        // Verificar solicitudes en revisión
        $solicitudesRevision = \App\Models\SolicitudCambio::where('proyecto_id', $proyecto->id)->where('estado', 'EN_REVISION')->count();
        $this->info("Solicitudes en revisión: {$solicitudesRevision}");

        // Verificar usuario actual si está autenticado
        if (\Illuminate\Support\Facades\Auth::check()) {
            $usuarioActual = \Illuminate\Support\Facades\Auth::user();
            $this->info("Usuario actual: {$usuarioActual->name} (ID: {$usuarioActual->id})");
            $esMiembro = $ccb->esMiembro($usuarioActual->id);
            $this->info("Es miembro del CCB: " . ($esMiembro ? 'Sí' : 'No'));
        } else {
            $this->warn("No hay usuario autenticado");
        }
    }
}
