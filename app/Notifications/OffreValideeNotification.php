<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OffreValideeNotification extends Notification
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
            ->subject('Votre offre a été validée')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre offre "' . ($this->offre->hotel_scraping->hotel_name ?? '') . '" a été validée par l\'administrateur.')
            ->line('Détails de l\'offre :')
            ->line('Nombre de chambres : ' . $this->offre->total_rooms)
            // Ajoute d'autres détails si tu veux
            ->action('Voir mon offre', url('/hotels/offres/' . $this->offre->id . '/details')) // adapte l'URL selon ton app
            ->line('Merci pour votre confiance.');
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
                'statut'      => $this->offre->statut,
                'total_rooms' => $this->offre->total_rooms,
                'message'     => 'Votre offre "' . ($this->offre->hotel_scraping->hotel_name ?? '') . '" a été validée.',
                'date'        => now(),
                'link'        => route('offre.detail', $this->offre->id),
            ],
        ];
    }


    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}