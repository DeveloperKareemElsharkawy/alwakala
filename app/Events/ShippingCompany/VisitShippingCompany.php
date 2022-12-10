<?php

namespace App\Events\ShippingCompany;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VisitShippingCompany
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $type;
    /**
     * Create a new event instance.
     * @param $request
     * @param $type
     * @return void
     */
    public function __construct($request, $type)
    {
        $this->request = $request;
        $this->type = $type;
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
