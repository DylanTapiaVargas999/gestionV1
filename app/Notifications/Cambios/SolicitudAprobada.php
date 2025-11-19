<?php

namespace App\Notifications\Cambios;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SolicitudAprobada extends Notification
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
            'tipo' => 'solicitud_aprobada',
            'icono' => 'check-circle',
            'color' => 'green',
            'mensaje' => "✅ La solicitud de cambio '{$this->solicitud->titulo}' ha sido APROBADA",
            'url' => route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de Cambio Aprobada')
            ->line("La solicitud de cambio '{$this->solicitud->titulo}' ha sido APROBADA.")
            ->line("Proyecto: {$this->solicitud->proyecto->nombre}")
            ->action('Ver Solicitud', route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ]))
            ->line('El proceso de implementación comenzará pronto.');
    }
}
