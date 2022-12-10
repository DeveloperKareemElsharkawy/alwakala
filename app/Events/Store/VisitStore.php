<?php

namespace App\Events\Store;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VisitStore
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $type;
    public $userId;
    public $itemId;

    /**
     * Create a new event instance.
     * @param $request
     * @param $type
     * @param $userId
     * @param $itemId
     * @return void
     */
    public function __construct($request, $userId, $itemId)
    {
        $this->request = $request;
        $this->userId = $userId;
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
