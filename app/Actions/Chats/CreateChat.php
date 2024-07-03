<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\Status;
use App\Actions\Messages\CreateMessage;
use App\Actions\ActionLogs\CreateActionLog;
use App\Models\ChatUser;
use Spatie\QueueableAction\QueueableAction;

class CreateChat
{
    use QueueableAction;

    public function execute(array $data)
    {
        $chat = new Chat($data);

        if (isset($data['company_id'])) {
            $chat->company()->associate($data['company_id']);
        }

        $newStatus = Status::where('slug', 'new')->first();
        $chat->status()->associate($newStatus);

        $chatUser = ChatUser::where('uuid', $data['chat_user_uuid'])->first();
        $chat->chatUser()->associate($chatUser);

        $chat->save();

        if (isset($data['message'])) {
            $messageData = [
                'chat_id' => $chat->id,
                'chat_user_uuid' => $data['message']['chat_user_uuid'] ?? null,
                'content' => $data['message']['content'],
                'sent_at' => now(),
                'from_agent' => $data['message']['from_agent'] ?? false,
            ];
            app(CreateMessage::class)->execute($messageData);
        }

        app(CreateActionLog::class)->onQueue()->execute($chat, 'created', []);

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
