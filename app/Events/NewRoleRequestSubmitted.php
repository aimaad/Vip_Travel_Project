<?php

namespace App\Events;

use App\Models\RoleRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewRoleRequestNotification;
use Illuminate\Support\Facades\Log;
class NewRoleRequestSubmitted
{
    use SerializesModels;

    public $user;
    public $roleRequest;

    public function __construct($user, RoleRequest $roleRequest)
    {
        Log::info('NewRoleRequestSubmitted event triggered.');

        $this->user = $user;
        $this->roleRequest = $roleRequest;
    }
}
