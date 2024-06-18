<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class DeleteChat
{
    use QueueableAction;

    public function execute(int $id)
    {
        $chat = Chat::findOrFail($id);

        $chat->delete();

        // Delete all messages related to chat
        $chat->messages()->delete();

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
