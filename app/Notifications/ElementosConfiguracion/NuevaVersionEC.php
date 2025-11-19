<?php

namespace App\Notifications\ElementosConfiguracion;

use App\Models\ElementoConfiguracion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NuevaVersionEC extends Notification
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
            'version' => $this->elemento->version,
            'proyecto_id' => $this->elemento->proyecto_id,
            'proyecto_nombre' => $this->elemento->proyecto->nombre,
            'tipo' => 'nueva_version_ec',
            'icono' => 'cube',
            'color' => 'purple',
            'mensaje' => "📦 Nueva versión del elemento '{$this->elemento->titulo}': v{$this->elemento->version}",
            'url' => route('proyectos.elementos.show', [
                'proyecto' => $this->elemento->proyecto_id,
                'elemento' => $this->elemento->id_elemento
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva Versión de Elemento de Configuración')
            ->line("Se ha creado una nueva versión del elemento '{$this->elemento->titulo}'.")
            ->line("Proyecto: {$this->elemento->proyecto->nombre}")
            ->line("Versión: {$this->elemento->version}")
            ->action('Ver Elemento', route('proyectos.elementos.show', [
                'proyecto' => $this->elemento->proyecto_id,
                'elemento' => $this->elemento->id_elemento
            ]))
            ->line('Revisa los cambios realizados.');
    }
}
