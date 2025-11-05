<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RoleRequest;
use App\Notifications\NewRoleRequestNotification;

class TestController extends Controller
{
    public function testNotification()
    {
        // Remplacez par l'ID de l'utilisateur admin dans votre base de données
        $user = User::find(1); 
        
        // Remplacez par l'ID d'une demande de rôle existante
        $roleRequest = RoleRequest::find(1); 
        
        if ($user && $roleRequest) {
            $user->notify(new NewRoleRequestNotification($user, $roleRequest));
            return 'Notification sent';
        } else {
            return 'User or Role Request not found';
        }
    }
}
