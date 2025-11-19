<?php

namespace App\Notifications\Scrum;

use App\Models\UserStory;
use App\Models\Sprint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserStoryAsignadaASprint extends Notification
{
    use Queueable;

    public function __construct(
        public UserStory $userStory,
        public Sprint $sprint
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'user_story_id' => $this->userStory->id_user_story,
            'user_story_titulo' => $this->userStory->titulo,
            'sprint_id' => $this->sprint->id_sprint,
            'sprint_nombre' => $this->sprint->nombre,
            'proyecto_id' => $this->sprint->proyecto_id,
            'proyecto_nombre' => $this->sprint->proyecto->nombre,
            'tipo' => 'user_story_asignada',
            'icono' => 'book-open',
            'color' => 'indigo',
            'mensaje' => "La user story '{$this->userStory->titulo}' ha sido asignada al {$this->sprint->nombre}",
            'url' => route('proyectos.scrum.show', $this->sprint->proyecto_id)
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('User Story Asignada a Sprint')
            ->line("La user story '{$this->userStory->titulo}' ha sido asignada al {$this->sprint->nombre}.")
            ->line("Proyecto: {$this->sprint->proyecto->nombre}")
            ->action('Ver Tablero Scrum', route('proyectos.scrum.show', $this->sprint->proyecto_id))
            ->line('Revisa el sprint backlog actualizado.');
    }
}
