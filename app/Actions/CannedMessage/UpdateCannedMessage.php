<?php

namespace App\Actions\CannedMessages;

use App\Models\CannedMessage;
use Spatie\QueueableAction\QueueableAction;

class UpdateCannedMessage
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $cannedMessage = CannedMessage::findOrFail($id);

        $cannedMessage->fill($data);

        $cannedMessage->user()->associate($data['user_id']);

        $cannedMessage->save();

        return $cannedMessage;
    }
}

//Generated 27-10-2023 10:55:45
