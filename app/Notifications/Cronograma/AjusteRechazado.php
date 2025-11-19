<?php

namespace App\Notifications\Cronograma;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AjusteRechazado extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto,
        public string $razon
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'proyecto_id' => $this->proyecto->id_proyecto,
            'proyecto_nombre' => $this->proyecto->nombre,
            'razon' => $this->razon,
            'tipo' => 'ajuste_rechazado',
            'icono' => 'x-circle',
            'color' => 'gray',
            'mensaje' => "El ajuste propuesto para el cronograma del proyecto '{$this->proyecto->nombre}' fue rechazado",
            'url' => route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ajuste de Cronograma Rechazado')
            ->line("El ajuste propuesto para el cronograma fue rechazado.")
            ->line("Proyecto: {$this->proyecto->nombre}")
            ->line("Razón: {$this->razon}")
            ->action('Ver Análisis', route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto))
            ->line('Puedes revisar otras soluciones disponibles.');
    }
}
