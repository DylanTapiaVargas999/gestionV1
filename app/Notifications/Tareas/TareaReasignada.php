<?php

namespace App\Notifications\Tareas;

use App\Models\TareaProyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TareaReasignada extends Notification
{
    use Queueable;

    public function __construct(
        public TareaProyecto $tarea,
        public bool $esNuevoResponsable = true
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $mensaje = $this->esNuevoResponsable
            ? "La tarea '{$this->tarea->nombre}' te ha sido reasignada"
            : "La tarea '{$this->tarea->nombre}' ha sido reasignada a otro usuario";

        return [
            'tarea_id' => $this->tarea->id_tarea,
            'tarea_nombre' => $this->tarea->nombre,
            'proyecto_id' => $this->tarea->proyecto_id,
            'proyecto_nombre' => $this->tarea->proyecto->nombre,
            'es_nuevo_responsable' => $this->esNuevoResponsable,
            'tipo' => 'tarea_reasignada',
            'icono' => 'arrow-path',
            'color' => $this->esNuevoResponsable ? 'blue' : 'gray',
            'mensaje' => $mensaje,
            'url' => route('proyectos.tareas.show', [
                'proyecto' => $this->tarea->proyecto_id,
                'tarea' => $this->tarea->id_tarea
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->esNuevoResponsable) {
            return (new MailMessage)
                ->subject('Tarea Reasignada')
                ->line("La tarea '{$this->tarea->nombre}' te ha sido reasignada.")
                ->line("Proyecto: {$this->tarea->proyecto->nombre}")
                ->action('Ver Tarea', route('proyectos.tareas.show', [
                    'proyecto' => $this->tarea->proyecto_id,
                    'tarea' => $this->tarea->id_tarea
                ]))
                ->line('Por favor, revisa los detalles y avance actual.');
        }

        return (new MailMessage)
            ->subject('Tarea Reasignada')
            ->line("La tarea '{$this->tarea->nombre}' ha sido reasignada a otro usuario.")
            ->line('Ya no eres responsable de esta tarea.');
    }
}
