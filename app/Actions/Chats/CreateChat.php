<?php

namespace App\Actions\Chats;

use App\Actions\Messages\CreateMessage;
use App\Models\Chat;
use App\Models\Status;
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

        $chat->save();

        if (isset($data['message'])) {
            $messageData = [
                'chat_id' => $chat->id,
                'content' => $data['message'],
                'sent_at' => now(),
            ];
            app(CreateMessage::class)->execute($messageData);
        }

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
