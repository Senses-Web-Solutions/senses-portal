<?php

namespace App\Actions\Chats;

use App\Actions\Messages\ReadMessage;
use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class AgentReadsChat
{
    use QueueableAction;

    public function execute(Chat $chat)
    {

        // Find all messages not from_agent and mark them as read

        $messages = $chat->messages()->where('from_agent', false)->where('read_at', null)->get();

        foreach ($messages as $message) {
            app(ReadMessage::class)->onQueue()->execute($message);
        }

        return $chat;
    }
}
