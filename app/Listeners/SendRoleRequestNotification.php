<?php

namespace App\Listeners;

use App\Events\NewRoleRequestSubmitted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewRoleRequestNotification;

class SendRoleRequestNotification
{
    public function handle(NewRoleRequestSubmitted $event)
    {
        Log::info('SendRoleRequestNotification listener handling event.');
    
        
        $adminRoleId = 1; // ID du rôle super admin
        $additionalRoleId = 6; // ID du user admin
    
        // Récupérer les utilisateurs ayant le role_id de 1 ou 6
        $admins = \App\Models\User::whereIn('role_id', [$adminRoleId, $additionalRoleId])->get();
    
        // Envoyer les notifications
        Notification::send($admins, new NewRoleRequestNotification($event->user, $event->roleRequest));
    }
    
}
