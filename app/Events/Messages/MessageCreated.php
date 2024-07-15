<?php

namespace App\Events\Messages;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use App\Jobs\Messages\SendMessageNotificationIfUnread;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageCreated implements ShouldBroadcastNow
{
    public Message $message;
    private int $company_id;

    public function __construct(Message $message)
    {
        SendMessageNotificationIfUnread::dispatch($message)->delay(now()->addSeconds(10));

        $message->load([
            'status' => function ($query) {
                $query->select('statuses.id', 'statuses.title', 'statuses.slug', 'statuses.colour', 'statuses.text_colour');
            },
            'author:id,full_name',
            'files'
        ]);
        $this->message = $message;
        $this->company_id = $message->chat->company_id;

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
