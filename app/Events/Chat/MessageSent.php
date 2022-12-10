<?php

namespace App\Events\Chat;

use App\Http\Resources\Seller\Chat\MessageResource;
use App\Http\Resources\Seller\Orders\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * User that sent the message
     *
     * @var User
     */
    public $receiverID;

    /**
     * User that sent the message
     *
     * @var User
     */
    public $senderID;

    /**
     * Message details
     *
     * @var Message
     */
    public $message;

    /**
     * Message details
     *
     * @var Message
     */
    public $userInfo;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiverID,$senderID,UserResource $userInfo, MessageResource $message)
    {
        $this->receiverID = $receiverID;
        $this->senderID = $senderID;
        $this->userInfo = $userInfo;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.' . $this->receiverID .'.receive-message');
    }

    public function broadcastAs(): string
    {
        return 'message-received';
    }
}
