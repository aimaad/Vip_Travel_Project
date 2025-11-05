<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferConfirmed
{
    use Dispatchable, SerializesModels;

    public $data;

    public function __construct(array $data) // data = ['input' => ..., 'services' => ..., 'booking' => ..., 'user' => ..., 'offre_id'=>...]
    {
        $this->data = $data;
    }
}