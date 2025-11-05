<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RoleRequest;
use Illuminate\Notifications\Messages\MailMessage;



class NewRoleRequestNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $roleRequest;

    public function __construct($user, RoleRequest $roleRequest)
    {
        $this->user = $user;
        $this->roleRequest = $roleRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->roleRequest->type == "Distributeur de voyage") {
            $urlRequest  = 'admin/distributorUpgradeRequest';
        } else {
            $urlRequest = 'admin/agentUpgradeRequest';
        }
        return (new MailMessage)
            ->subject('New Role Request')
            ->line('A new role request has been submitted by ' . $this->user->name)
            ->action('View Request', url($urlRequest))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        // Determine the URL based on the role request type
        if ($this->roleRequest->type == "Distributeur de voyage") {
            $urlRequest  = 'admin/distributorUpgradeRequest';
        } else {
            $urlRequest = 'admin/agentUpgradeRequest';
        }
        $message="{$this->user->name} ' has requested to become a ' {$this->roleRequest->type}";
        return [
            'id' => $this->id,
            'for_admin' => 1, // This indicates that the notification is for admin
            'notification' => [
                'id' => $this->roleRequest->id, // Assuming the role request has an ID
                'name' => $this->user->name, // The name of the user making the request
                'avatar' => $this->user->avatar_url ?? 'default_avatar.png', // Ensure there's a valid avatar
                'link' => url('/' . $urlRequest  ), // The link to the request
                'type' => $this->roleRequest->type, // Custom type for your notification
                'message' => $message,
            ],
        ];
    }
    
    

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
