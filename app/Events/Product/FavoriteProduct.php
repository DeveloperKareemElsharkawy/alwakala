<?php

namespace App\Events\Product;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FavoriteProduct
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usersId;
    public $itemId;
    public $image;

    /**
     * Create a new event instance.
     * @param $userId
     * @param $itemId
     * @return void
     */
    public function __construct($usersId, $itemId,$image)
    {
        $this->usersId = $usersId;
        $this->itemId = $itemId;
        $this->image = $image;
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
