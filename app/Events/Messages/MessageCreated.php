<?php

namespace App\Events\Messages;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageCreated implements ShouldBroadcastNow
{
    public Message $message;
    public int $company_id;

    public function __construct(Message $message)
    {
        $message->load(['status']);
        $this->message = $message;
        $this->company_id = $message->chat->company_id;

        // Take chat off the message to avoid circular reference
        unset($this->message->chat);
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('companies.' . $this->company_id . '.message'),
            new Channel('chats.' . $this->message->chat_id . '.message'),
        ];
    }
}
