<?php

namespace App\Notifications\Cambios;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NuevaSolicitudCambio extends Notification
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
            'creador_nombre' => $this->solicitud->creadoPor->nombre ?? 'Usuario',
            'tipo' => 'nueva_solicitud_cambio',
            'icono' => 'document-plus',
            'color' => 'blue',
            'mensaje' => "{$this->solicitud->creadoPor->nombre} ha creado una solicitud de cambio: {$this->solicitud->titulo}",
            'url' => route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva Solicitud de Cambio')
            ->line("Se ha creado una nueva solicitud de cambio: {$this->solicitud->titulo}")
            ->line("Proyecto: {$this->solicitud->proyecto->nombre}")
            ->action('Revisar y Votar', route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ]))
            ->line('Por favor, revisa la solicitud y emite tu voto.');
    }
}
