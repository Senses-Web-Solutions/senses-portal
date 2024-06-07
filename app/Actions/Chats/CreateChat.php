<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use App\Models\Status;
use App\Events\Chats\ChatCreated;
use App\Actions\Messages\CreateMessage;
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
                'content' => $data['message']['content'],
                'author' => $data['message']['author'],
                'sent_at' => now(),
            ];
            app(CreateMessage::class)->onQueue()->execute($messageData);
        }

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
