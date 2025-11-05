<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OffreRefuseeNotification extends Notification
{
    use Queueable;

    public $offre;

    public function __construct($offre)
    {
        $this->offre = $offre;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre offre a été refusée')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre offre "' . ($this->offre->hotel_scraping->hotel_name ?? '') . '" a été refusée.')
            ->line('Motif du refus :')
            ->line($this->offre->refus_commentaire)
            ->line('Merci de votre compréhension.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->id,
            'notification' => [
                'id'          => $this->offre->id,
                'to'          => 'user',
                'name'        => $notifiable->name,
                'avatar'      => $notifiable->avatar_url ?? 'default_avatar.png',
                'hotel_name'  => $this->offre->hotel_scraping->hotel_name ?? '',
                'refus_commentaire' => $this->offre->refus_commentaire,
                'statut'      => $this->offre->statut,
                'message'     => 'Votre offre "' . ($this->offre->hotel_scraping->hotel_name ?? '') . '" a été refusée.',
                'date'        => now(),
                // Ajoute ce lien pour la redirection :
                'link'        => route('offre.refus.detail', $this->offre->id),
            ],
        ];
    }


    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}