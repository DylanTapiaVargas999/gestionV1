<?php

namespace App\Notifications\Scrum;

use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SprintCompletado extends Notification
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
            'tipo' => 'sprint_completado',
            'icono' => 'check-badge',
            'color' => 'green',
            'mensaje' => "✅ El sprint '{$this->sprint->nombre}' ha finalizado",
            'url' => route('proyectos.scrum.show', $this->sprint->proyecto_id)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sprint Completado')
            ->line("El sprint '{$this->sprint->nombre}' ha finalizado.")
            ->line("Proyecto: {$this->sprint->proyecto->nombre}")
            ->action('Ver Resultados', route('proyectos.scrum.show', $this->sprint->proyecto_id))
            ->line('Es momento de realizar la retrospectiva.');
    }
}
