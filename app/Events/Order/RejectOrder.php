<?php

namespace App\Events\Order;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RejectOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usersId;
    public $itemId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($storesUsersIds,$item_id)
    {
        $this->usersId = $storesUsersIds;
        $this->itemId = $item_id;
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
