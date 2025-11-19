<?php

namespace App\Notifications\Tareas;

use App\Models\TareaProyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TareaAsignada extends Notification
{
    use Queueable;

    public function __construct(
        public TareaProyecto $tarea
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'tarea_id' => $this->tarea->id_tarea,
            'tarea_nombre' => $this->tarea->nombre,
            'proyecto_id' => $this->tarea->proyecto_id,
            'proyecto_nombre' => $this->tarea->proyecto->nombre,
            'fecha_fin' => $this->tarea->fecha_fin,
            'tipo' => 'tarea_asignada',
            'icono' => 'clipboard-check',
            'color' => 'blue',
            'mensaje' => "Se te ha asignado la tarea: {$this->tarea->nombre}",
            'url' => route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva Tarea Asignada')
            ->line("Se te ha asignado una nueva tarea: {$this->tarea->nombre}")
            ->line("Proyecto: {$this->tarea->proyecto->nombre}")
            ->line("Fecha de entrega: {$this->tarea->fecha_fin}")
            ->action('Ver Tarea', route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ]))
            ->line('¡Mucho éxito con tu tarea!');
    }
}
