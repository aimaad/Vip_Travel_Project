<?php

namespace App\Listeners;

use App\Events\OfferConfirmed;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\OfferConfirmedNotification;

class SendOfferConfirmedNotification
{
    public function handle(OfferConfirmed $event)
    {
        // Récupère les admins (adapte selon ta logique)
        $admins = User::whereIn('role_id', [1, 6])->get();
        Notification::send($admins, new OfferConfirmedNotification($event->data));
    }
}