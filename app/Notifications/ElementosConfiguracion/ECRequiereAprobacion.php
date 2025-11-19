<?php

namespace App\Notifications\ElementosConfiguracion;

use App\Models\ElementoConfiguracion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ECRequiereAprobacion extends Notification
{
    use Queueable;

    public function __construct(
        public ElementoConfiguracion $elemento
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'ec_id' => $this->elemento->id_elemento,
            'ec_titulo' => $this->elemento->titulo,
            'proyecto_id' => $this->elemento->proyecto_id,
            'proyecto_nombre' => $this->elemento->proyecto->nombre,
            'tipo' => 'ec_requiere_aprobacion',
            'icono' => 'document-text',
            'color' => 'yellow',
            'mensaje' => "El elemento '{$this->elemento->titulo}' requiere aprobación",
            'url' => route('proyectos.elementos.show', [
                'proyecto' => $this->elemento->proyecto_id,
                'elemento' => $this->elemento->id_elemento
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Elemento de Configuración Requiere Aprobación')
            ->line("El elemento '{$this->elemento->titulo}' requiere tu aprobación.")
            ->line("Proyecto: {$this->elemento->proyecto->nombre}")
            ->action('Revisar y Aprobar', route('proyectos.elementos.show', [
                'proyecto' => $this->elemento->proyecto_id,
                'elemento' => $this->elemento->id_elemento
            ]))
            ->line('Por favor, revisa el elemento y apruébalo si corresponde.');
    }
}
