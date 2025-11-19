<?php

namespace App\Notifications\Cronograma;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AjusteCronogramaPropuesto extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto,
        public array $analisis
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'proyecto_id' => $this->proyecto->id_proyecto,
            'proyecto_nombre' => $this->proyecto->nombre,
            'dias_atraso' => $this->analisis['dias_atraso'] ?? 0,
            'tipo' => 'ajuste_cronograma_propuesto',
            'icono' => 'exclamation-triangle',
            'color' => 'orange',
            'mensaje' => "⚠️ El sistema ha detectado una desviación en el cronograma del proyecto '{$this->proyecto->nombre}'",
            'url' => route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ajuste de Cronograma Propuesto')
            ->line("El sistema ha detectado una desviación en el cronograma.")
            ->line("Proyecto: {$this->proyecto->nombre}")
            ->line("Días de atraso detectados: " . ($this->analisis['dias_atraso'] ?? 0))
            ->action('Revisar Propuesta', route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto))
            ->line('Por favor, revisa las soluciones propuestas.');
    }
}
