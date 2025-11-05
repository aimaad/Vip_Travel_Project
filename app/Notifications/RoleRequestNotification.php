<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RoleRequestNotification extends Notification
{
    use Queueable;

    protected $roleRequest;

    public function __construct($roleRequest)
    {
        $this->roleRequest = $roleRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new role request has been submitted.')
                    ->action('View Request', url('/admin/role-requests/' . $this->roleRequest->id))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->roleRequest->id,
            'name' => $this->roleRequest->user->name,
            'type' => $this->roleRequest->type,
            'message' => 'A new role request has been submitted.',
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
