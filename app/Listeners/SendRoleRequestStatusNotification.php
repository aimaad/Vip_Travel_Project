<?php

namespace App\Listeners;

use App\Events\RoleRequestUpdated;
use App\Notifications\RoleRequestStatusNotification;
use Illuminate\Support\Facades\Log;
use App\Notifications\RoleRequestNotification;


class SendRoleRequestStatusNotification
{
    public function handle(RoleRequestUpdated $event)
{
    // Log that the listener has been triggered
    Log::info('Handling RoleRequestUpdated event for request ID: ' . $event->roleRequest->id);

    // Ensure the user and notification are set correctly
    $user = $event->roleRequest->user;

    // Log the user ID to verify it's correct
    Log::info('User ID: ' . $user->id);

    // Send notification
    $user->notify(new RoleRequestStatusNotification($event->roleRequest, $event->status));

    // Log after the notification has been sent
    Log::info('Notification sent for request ID: ' . $event->roleRequest->id);
}

}
