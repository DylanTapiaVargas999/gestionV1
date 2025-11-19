<?php

namespace App\Notifications\Scrum;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DailyScrumPendiente extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'proyecto_id' => $this->proyecto->id_proyecto,
            'proyecto_nombre' => $this->proyecto->nombre,
            'tipo' => 'daily_scrum_pendiente',
            'icono' => 'calendar',
            'color' => 'yellow',
            'mensaje' => "📅 Recuerda registrar el Daily Scrum de hoy para el proyecto '{$this->proyecto->nombre}'",
            'url' => route('proyectos.scrum.show', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recordatorio: Daily Scrum')
            ->line("Recuerda registrar el Daily Scrum de hoy.")
            ->line("Proyecto: {$this->proyecto->nombre}")
            ->action('Registrar Daily Scrum', route('proyectos.scrum.show', $this->proyecto->id_proyecto))
            ->line('Mantén al equipo informado sobre el progreso diario.');
    }
}
