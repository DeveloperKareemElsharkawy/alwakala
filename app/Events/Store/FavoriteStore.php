<?php

namespace App\Events\Store;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FavoriteStore
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usersId;
    public $itemId;

    /**
     * Create a new event instance.
     * @param $userId
     * @param $itemId
     * @return void
     */
    public function __construct($usersId, $itemId)
    {
        $this->usersId = $usersId;
        $this->itemId = $itemId;
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
