<?php

namespace App\Events\Product;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReviewProduct
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $image;
    public $usersId;
    public $itemId;

    /**
     * Create a new event instance.
     * @param $iamge
     * @param $userId
     * @param $itemId
     * @return void
     */
    public function __construct($userId, $itemId,$image)
    {

        $this->usersId = $userId;
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
