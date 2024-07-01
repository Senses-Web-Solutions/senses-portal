<?php

namespace App\Actions\Chats;

use App\Events\Chats\ChatResolved;
use App\Models\Chat;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class ResolveChat
{
    use QueueableAction;

    public function execute(int $id)
    {
        $chat = Chat::findOrFail($id);

        $status = Status::where('slug', 'resolved')->first();

        $chat->status()->associate($status);

        $chat->completed_at = now();

        $chat->save();

        event(new ChatResolved($chat));

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
