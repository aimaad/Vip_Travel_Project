<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\HotelScraping;

class HotelRequiresValidation extends Notification
{
    use Queueable;

    protected $hotel;

    public function __construct(HotelScraping $hotel)
    {
        $this->hotel = $hotel;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle demande de validation d’hôtel')
            ->line("Un nouvel hôtel nécessite une validation : {$this->hotel->hotel_name}")
            ->action('Voir l’hôtel', url('/admin/hotels/' . $this->hotel->id))
            ->line('Merci de vérifier les informations et de les valider.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->hotel->id,
            'hotel_name' => $this->hotel->hotel_name,
            'message' => "L'hôtel \"{$this->hotel->hotel_name}\" requiert une validation manuelle.",
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
