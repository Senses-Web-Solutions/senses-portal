<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\Status;
use App\Actions\ActionLogs\CreateActionLog;
use Spatie\QueueableAction\QueueableAction;

class ReviewChat
{
    use QueueableAction;

    public function execute(array $data)
    {
        

        $chat->load('messages', 'agents', 'invitedAgents', 'actionLogs.user');

        app(CreateActionLog::class)->onQueue()->execute($chat, 'created', []);

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
