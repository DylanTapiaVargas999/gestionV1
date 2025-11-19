<?php

namespace App\Notifications\Cambios;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VotoPendienteCCB extends Notification
{
    use Queueable;

    public function __construct(
        public SolicitudCambio $solicitud
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'solicitud_id' => $this->solicitud->id_solicitud,
            'titulo' => $this->solicitud->titulo,
            'proyecto_id' => $this->solicitud->proyecto_id,
            'proyecto_nombre' => $this->solicitud->proyecto->nombre,
            'tipo' => 'voto_pendiente',
            'icono' => 'clock',
            'color' => 'orange',
            'mensaje' => "⚠️ Tienes una solicitud de cambio pendiente de votación: {$this->solicitud->titulo}",
            'url' => route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Voto Pendiente en Solicitud de Cambio')
            ->line("Tienes una solicitud de cambio pendiente de votación.")
            ->line("Solicitud: {$this->solicitud->titulo}")
            ->line("Proyecto: {$this->solicitud->proyecto->nombre}")
            ->action('Votar Ahora', route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ]))
            ->line('Tu voto es importante para el proceso de gestión de cambios.');
    }
}
