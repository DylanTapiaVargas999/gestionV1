<?php

namespace App\Notifications\Tareas;

use App\Models\TareaProyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TareaProximaAVencer extends Notification
{
    use Queueable;

    public function __construct(
        public TareaProyecto $tarea,
        public int $diasRestantes
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $mensajeDias = $this->diasRestantes === 1 ? 'vence mañana' : "vence en {$this->diasRestantes} días";

        return [
            'tarea_id' => $this->tarea->id_tarea,
            'tarea_nombre' => $this->tarea->nombre,
            'proyecto_id' => $this->tarea->proyecto_id,
            'proyecto_nombre' => $this->tarea->proyecto->nombre,
            'fecha_fin' => $this->tarea->fecha_fin,
            'dias_restantes' => $this->diasRestantes,
            'tipo' => 'tarea_proxima_vencer',
            'icono' => 'exclamation-triangle',
            'color' => 'orange',
            'mensaje' => "⚠️ La tarea '{$this->tarea->nombre}' {$mensajeDias}",
            'url' => route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mensajeDias = $this->diasRestantes === 1 ? 'vence mañana' : "vence en {$this->diasRestantes} días";

        return (new MailMessage)
            ->subject('Tarea Próxima a Vencer')
            ->line("⚠️ La tarea '{$this->tarea->nombre}' {$mensajeDias}.")
            ->line("Proyecto: {$this->tarea->proyecto->nombre}")
            ->line("Progreso actual: {$this->tarea->progreso_real}%")
            ->action('Ver Tarea', route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ]))
            ->line('Por favor, asegúrate de completarla a tiempo.');
    }
}
