<?php

namespace App\Events\Messages;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageUpdated implements ShouldBroadcastNow
{
    public Message $message;

    public function __construct(Message $message)
    {
        $message->load(['company', 'status']);
        $this->message = $message;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('companies.' . $this->message->chat->company_id . '.message'),
        ];
    }
}
