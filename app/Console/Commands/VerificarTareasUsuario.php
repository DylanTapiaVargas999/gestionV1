<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TareaProyecto;
use App\Models\Usuario;
use App\Models\Proyecto;

class VerificarTareasUsuario extends Command
{
    protected $signature = 'tareas:verificar {correo?}';
    protected $description = 'Verificar tareas asignadas a un usuario';

    public function handle()
    {
        $correo = $this->argument('correo') ?? 'dev1@demo.com';
        
        $usuario = Usuario::where('correo', $correo)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario con correo {$correo} no encontrado");
            return 1;
        }

        $this->info("👤 Usuario: {$usuario->nombre_completo} ({$usuario->correo})");
        $this->info("🔑 ID: {$usuario->id}");
        $this->newLine();

        // Obtener todas las tareas del usuario
        $tareas = TareaProyecto::where('responsable', $usuario->id)
            ->with(['proyecto', 'fase'])
            ->get();

        if ($tareas->isEmpty()) {
            $this->warn("⚠️  No hay tareas asignadas a este usuario");
            $this->newLine();
            $this->info("💡 Puedes crear tareas de prueba ejecutando:");
            $this->line("   php artisan db:seed --class=TareasProyectoSeeder");
            return 0;
        }

        $this->info("📋 Total de tareas asignadas: {$tareas->count()}");
        $this->newLine();

        // Agrupar por estado
        $porEstado = $tareas->groupBy('estado');
        
        $this->info("Tareas por estado:");
        foreach ($porEstado as $estado => $tareasEstado) {
            $emoji = match($estado) {
                'Pendiente', 'PENDIENTE' => '⭕',
                'En Progreso', 'EN_PROGRESO' => '🔄',
                'Completado', 'COMPLETADA' => '✅',
                default => '📝'
            };
            $this->line("   {$emoji} {$estado}: {$tareasEstado->count()} tareas");
        }
        
        $this->newLine();
        $this->info("📝 Detalle de tareas:");
        $this->newLine();

        foreach ($tareas as $index => $tarea) {
            $numero = $index + 1;
            $proyecto = $tarea->proyecto ? $tarea->proyecto->nombre : 'Sin proyecto';
            $fase = $tarea->fase ? $tarea->fase->nombre_fase : 'Sin fase';
            
            $this->line("{$numero}. {$tarea->nombre}");
            $this->line("   - Proyecto: {$proyecto}");
            $this->line("   - Fase: {$fase}");
            $this->line("   - Estado: {$tarea->estado}");
            $this->line("   - Prioridad: {$tarea->prioridad}");
            if ($tarea->fecha_fin) {
                $this->line("   - Fecha fin: {$tarea->fecha_fin->format('d/m/Y')}");
            }
            $this->newLine();
        }

        $this->info("✅ Verificación completada");
        return 0;
    }
}
