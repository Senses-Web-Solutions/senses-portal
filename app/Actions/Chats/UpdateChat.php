<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class UpdateChat
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $chat = Chat::findOrFail($id);

        $chat->fill($data);

        if (isset($data['company_id'])) {
            $chat->company()->associate($data['company_id']);
        }

        if (!$chat->isDirty()) {
            $chat->emitUpdated();
        }

        $chat->save();

        return $chat;
    }
}

//Generated 27-10-2023 10:55:45
