<?php

namespace App\Actions\Chats;

use App\Actions\ActionLogs\CreateActionLog;
use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class CobrowseChat
{
    use QueueableAction;

    public function execute(Chat|int $chat)
    {

        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        app(CreateActionLog::class)->onQueue()->execute($chat, 'request-cobrowse', []);

        event(new \App\Events\Chats\RequestCobrowse($chat));

        return $chat;
    }
}
