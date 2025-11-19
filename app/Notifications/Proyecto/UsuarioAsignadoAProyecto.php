<?php

namespace App\Notifications\Proyecto;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UsuarioAsignadoAProyecto extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto,
        public string $rol
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'proyecto_id' => $this->proyecto->id_proyecto,
            'proyecto_codigo' => $this->proyecto->codigo,
            'proyecto_nombre' => $this->proyecto->nombre,
            'rol' => $this->rol,
            'tipo' => 'proyecto_asignado',
            'icono' => 'user-plus',
            'color' => 'blue',
            'mensaje' => "Has sido asignado al proyecto '{$this->proyecto->nombre}' como {$this->rol}",
            'url' => route('proyectos.show', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Asignado a Nuevo Proyecto')
            ->line("Has sido asignado al proyecto '{$this->proyecto->nombre}' como {$this->rol}.")
            ->action('Ver Proyecto', route('proyectos.show', $this->proyecto->id_proyecto))
            ->line('¡Bienvenido al equipo!');
    }
}
