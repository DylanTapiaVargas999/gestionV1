<?php

namespace App\Notifications\Cambios;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SolicitudRechazada extends Notification
{
    use Queueable;

    public function __construct(
        public SolicitudCambio $solicitud
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'solicitud_id' => $this->solicitud->id_solicitud,
            'titulo' => $this->solicitud->titulo,
            'proyecto_id' => $this->solicitud->proyecto_id,
            'proyecto_nombre' => $this->solicitud->proyecto->nombre,
            'tipo' => 'solicitud_rechazada',
            'icono' => 'x-circle',
            'color' => 'red',
            'mensaje' => "❌ La solicitud de cambio '{$this->solicitud->titulo}' ha sido RECHAZADA",
            'url' => route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de Cambio Rechazada')
            ->line("La solicitud de cambio '{$this->solicitud->titulo}' ha sido RECHAZADA.")
            ->line("Proyecto: {$this->solicitud->proyecto->nombre}")
            ->action('Ver Detalles', route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ]))
            ->line('Puedes revisar los comentarios y votos en la solicitud.');
    }
}
