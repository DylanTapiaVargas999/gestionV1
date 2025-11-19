<?php

namespace App\Notifications\Tareas;

use App\Models\TareaProyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TareaAtrasada extends Notification
{
    use Queueable;

    public function __construct(
        public TareaProyecto $tarea,
        public int $diasAtraso
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'tarea_id' => $this->tarea->id_tarea,
            'tarea_nombre' => $this->tarea->nombre,
            'proyecto_id' => $this->tarea->proyecto_id,
            'proyecto_nombre' => $this->tarea->proyecto->nombre,
            'fecha_fin' => $this->tarea->fecha_fin,
            'dias_atraso' => $this->diasAtraso,
            'tipo' => 'tarea_atrasada',
            'icono' => 'exclamation-circle',
            'color' => 'red',
            'mensaje' => "🔴 La tarea '{$this->tarea->nombre}' está atrasada ({$this->diasAtraso} días)",
            'url' => route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tarea Atrasada - Acción Requerida')
            ->line("🔴 La tarea '{$this->tarea->nombre}' está atrasada.")
            ->line("Proyecto: {$this->tarea->proyecto->nombre}")
            ->line("Días de atraso: {$this->diasAtraso}")
            ->line("Progreso actual: {$this->tarea->progreso_real}%")
            ->action('Actualizar Tarea', route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ]))
            ->line('Por favor, actualiza el estado o contacta al líder del proyecto.');
    }
}
