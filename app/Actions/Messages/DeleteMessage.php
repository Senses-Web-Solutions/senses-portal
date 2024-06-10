<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Spatie\QueueableAction\QueueableAction;

class DeleteMessage
{
    use QueueableAction;

    public function execute(int $id)
    {
        $message = Message::findOrFail($id);

        $message->delete();

        return $message;
    }
}

//Generated 27-10-2023 10:55:45
