<?php

namespace App\Notifications\Scrum;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SprintIniciado extends Notification
{
    use Queueable;

    public function __construct(
        public Sprint $sprint
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'sprint_id' => $this->sprint->id_sprint,
            'sprint_nombre' => $this->sprint->nombre,
            'proyecto_id' => $this->sprint->proyecto_id,
            'proyecto_nombre' => $this->sprint->proyecto->nombre,
            'fecha_inicio' => $this->sprint->fecha_inicio,
            'fecha_fin' => $this->sprint->fecha_fin,
            'tipo' => 'sprint_iniciado',
            'icono' => 'play-circle',
            'color' => 'green',
            'mensaje' => "🏃 El sprint '{$this->sprint->nombre}' ha comenzado",
            'url' => route('proyectos.scrum.show', $this->sprint->proyecto_id)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sprint Iniciado')
            ->line("El sprint '{$this->sprint->nombre}' ha comenzado.")
            ->line("Proyecto: {$this->sprint->proyecto->nombre}")
            ->line("Fecha de fin: {$this->sprint->fecha_fin}")
            ->action('Ver Tablero Scrum', route('proyectos.scrum.show', $this->sprint->proyecto_id))
            ->line('¡Mucho éxito en este sprint!');
    }
}
