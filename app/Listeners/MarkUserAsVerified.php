<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class MarkUserAsVerified
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $user = $event->user;
        $user->is_verified = true;
        $user->save();
    }
}
