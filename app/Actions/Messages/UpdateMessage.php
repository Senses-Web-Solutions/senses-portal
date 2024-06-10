<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Spatie\QueueableAction\QueueableAction;

class UpdateMessage
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $message = Message::findOrFail($id);

        $message->fill($data);

        if (isset($data['chat_id'])) {
            $message->company()->associate($data['chat_id']);
        }

        if (!$message->isDirty()) {
            $message->emitUpdated();
        }

        $message->save();

        return $message;
    }
}

//Generated 27-10-2023 10:55:45
