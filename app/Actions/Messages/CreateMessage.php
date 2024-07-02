<?php

namespace App\Actions\Messages;

use App\Models\Message;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class CreateMessage
{
    use QueueableAction;

    public function execute(array $data)
    {
        $message = new Message($data);

        if (isset($data['chat_id'])) {
            $message->chat()->associate($data['chat_id']);
        }

        $sentStatus = Status::where('slug', 'sent')->first();
        $message->status()->associate($sentStatus);

        $message->save();

        if (isset($data['file_ids'])) {
            $message->files()->sync($data['file_ids']);
        }

        $message->load('files');
        
        return $message;
    }
}

//Generated 27-10-2023 10:55:45
