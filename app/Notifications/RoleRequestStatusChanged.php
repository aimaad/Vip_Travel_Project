<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RoleRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $roleRequest;
    protected $status;

    public function __construct($roleRequest, $status)
    {
        $this->roleRequest = $roleRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Send to both database and email
    }

    public function toDatabase($notifiable)
    {
        $user = $this->roleRequest->user;

        // Determine the message and event based on the status
        $event = $this->status == 'approved' ? 'AgentDistributorApproved' : 'AgentDistributorDeclined';
        $message = ($this->status == 'approved') ? 
            "Your request to become a {$this->roleRequest->type} has been approved." : 
            "Your request to become a {$this->roleRequest->type} has been declined.";

        // Format the notification array to match the desired structure
        return [
            'id' => $this->id,
            'for_admin' => 0, // Indicates this notification is for the user
            'notification' => [
                'id' => $this->roleRequest->id,
                'event' => $event,
                'to' => 'user',
                'name' => $user->name,
                'avatar' => $user->avatar_url ?? 'default_avatar.png',
                'link' => url('/user/dashboard'), // Link to user dashboard or relevant page
                'type' => 'user_upgrade_request',
                'message' => $message,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        $user = $this->roleRequest->user;

        // Determine the message based on the status
        $message = ($this->status == 'approved') ? 
            "Your request to become a {$this->roleRequest->type} has been approved." : 
            "Your request to become a {$this->roleRequest->type} has been declined.";

        return (new MailMessage)
                    ->subject('Role Upgrade Request ' . ucfirst($this->status))
                    ->greeting('Hello ' . $user->name . ',')
                    ->line($message)
                    ->action('View Details', url('/user/dashboard'))
                    ->line('Thank you for using our application!');
    }
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
    
}
