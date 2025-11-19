<?php

namespace App\Notifications\Proyecto;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MiembroAgregadoACCB extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'proyecto_id' => $this->proyecto->id_proyecto,
            'proyecto_codigo' => $this->proyecto->codigo,
            'proyecto_nombre' => $this->proyecto->nombre,
            'tipo' => 'ccb_asignado',
            'icono' => 'shield-check',
            'color' => 'purple',
            'mensaje' => "Has sido agregado al Comité de Control de Cambios del proyecto '{$this->proyecto->nombre}'",
            'url' => route('proyectos.ccb.dashboard', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Agregado al CCB')
            ->line("Has sido agregado al Comité de Control de Cambios (CCB) del proyecto '{$this->proyecto->nombre}'.")
            ->line('Como miembro del CCB, podrás votar en solicitudes de cambio.')
            ->action('Ver Dashboard CCB', route('proyectos.ccb.dashboard', $this->proyecto->id_proyecto))
            ->line('Gracias por tu participación.');
    }
}
