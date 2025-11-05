<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OfferConfirmedNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $user = $this->data['user'];
        return (new MailMessage)
            ->subject('Nouvelle offre à valider')
            ->line("{$user->name} {$user->prenom} a créé une nouvelle offre d'hôtel à valider.")
            ->action('Valider l\'offre', url('/admin/offres/'.$this->data['offre_id'].'/validation'))
            ->line('Merci.');
    }

    public function toDatabase($notifiable)
    {
        $user = $this->data['user'];
        $hotel = $this->data['input']['name'] ?? 'Hôtel';
        $offre_id = $this->data['offre_id'];
        // Ajoute toutes les données utiles pour la vue admin
        return [
            'for_admin' => 1,
            'notification' => [
                'id' => $offre_id,
                'name' => $hotel,
                'user_fullname' => $user->name . ' ' . $user->prenom,
                'avatar' => $user->avatar_url ?? '',
                'link' => url('/admin/offres/'.$offre_id.'/validation'),
                'type' => 'offer_pending',
                'message' => "{$user->name} {$user->prenom} a créé une offre pour $hotel, à valider.",
                'all_data' => $this->data, // Pour affichage détaillé si besoin
            ],
        ];
    }
}