<?php

namespace App\Notifications\Cronograma;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AjusteAprobado extends Notification
{
    use Queueable;

    public function __construct(
        public Proyecto $proyecto,
        public array $ajuste
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
            'estrategia' => $this->ajuste['estrategia'] ?? 'mixta',
            'tipo' => 'ajuste_aprobado',
            'icono' => 'check-circle',
            'color' => 'green',
            'mensaje' => "📅 Se ha aprobado un ajuste al cronograma del proyecto '{$this->proyecto->nombre}'",
            'url' => route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ajuste de Cronograma Aprobado')
            ->line("Se ha aprobado un ajuste al cronograma del proyecto.")
            ->line("Proyecto: {$this->proyecto->nombre}")
            ->line("Estrategia: " . ($this->ajuste['nombre'] ?? 'Ajuste personalizado'))
            ->action('Ver Cronograma Actualizado', route('proyectos.cronograma.inteligente', $this->proyecto->id_proyecto))
            ->line('Revisa las nuevas fechas de tus tareas.');
    }
}
