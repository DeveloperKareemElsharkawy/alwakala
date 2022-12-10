<?php

namespace App\Events\Users;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ApprovePendingStore
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usersId;
    public $itemId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($usersId)
    {
        $this->usersId = $usersId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
