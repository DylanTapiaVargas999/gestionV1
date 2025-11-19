<?php

namespace App\Notifications\Proyecto;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UsuarioAsignadoComoLider extends Notification
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
            'tipo' => 'lider_asignado',
            'icono' => 'star',
            'color' => 'yellow',
            'mensaje' => "Has sido asignado como Líder del proyecto '{$this->proyecto->nombre}'",
            'url' => route('proyectos.show-lider', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Asignado como Líder de Proyecto')
            ->line("Has sido asignado como Líder del proyecto '{$this->proyecto->nombre}'.")
            ->line('Como líder, tendrás acceso a funcionalidades especiales de gestión.')
            ->action('Ver Dashboard', route('proyectos.show-lider', $this->proyecto->id_proyecto))
            ->line('¡Mucho éxito en tu nuevo rol!');
    }
}
