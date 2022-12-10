<?php

namespace App\Events\Inventory;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StockMovement
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quantity;
    public $productId;
    public $transactionTypeId;
    public $storeId;

    /**
     * Create a new event instance.
     * @param $storeId
     * @param $quantity
     * @param $productId
     * @param $transactionTypeId
     * @return void
     */
    public function __construct($quantity, $productId, $transactionTypeId, $storeId)
    {
        $this->storeId = $storeId;
        $this->quantity = $quantity;
        $this->productId = $productId;
        $this->transactionTypeId = $transactionTypeId;
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
