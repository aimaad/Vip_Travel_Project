<?php

namespace App\Events;

use App\Models\RoleRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleRequestUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roleRequest;
    public $status;

    public function __construct(RoleRequest $roleRequest, $status)
    {
        $this->roleRequest = $roleRequest;
        $this->status = $status;
    }
}
