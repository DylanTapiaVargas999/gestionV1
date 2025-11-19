<?php

namespace App\Notifications\Liberaciones;

use App\Models\Liberacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NuevaLiberacion extends Notification
{
    use Queueable;

    public function __construct(
        public Liberacion $liberacion
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'liberacion_id' => $this->liberacion->id_liberacion,
            'nombre' => $this->liberacion->nombre,
            'version' => $this->liberacion->version,
            'proyecto_id' => $this->liberacion->proyecto_id,
            'proyecto_nombre' => $this->liberacion->proyecto->nombre,
            'tipo' => 'nueva_liberacion',
            'icono' => 'rocket-launch',
            'color' => 'green',
            'mensaje' => "🚀 Nueva liberación '{$this->liberacion->nombre}' v{$this->liberacion->version} creada",
            'url' => route('proyectos.liberaciones.show', [
                'proyecto' => $this->liberacion->proyecto_id,
                'liberacion' => $this->liberacion->id_liberacion
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva Liberación Publicada')
            ->line("Se ha creado una nueva liberación: '{$this->liberacion->nombre}'.")
            ->line("Proyecto: {$this->liberacion->proyecto->nombre}")
            ->line("Versión: {$this->liberacion->version}")
            ->action('Ver Liberación', route('proyectos.liberaciones.show', [
                'proyecto' => $this->liberacion->proyecto_id,
                'liberacion' => $this->liberacion->id_liberacion
            ]))
            ->line('Revisa las notas de la versión y los cambios incluidos.');
    }
}
