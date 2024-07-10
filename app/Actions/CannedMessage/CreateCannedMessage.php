<?php

namespace App\Actions\CannedMessages;

use App\Models\CannedMessage;
use Spatie\QueueableAction\QueueableAction;

class CreateCannedMessage
{
    use QueueableAction;

    public function execute(array $data)
    {
        $cannedMessage = new CannedMessage($data);

        $cannedMessage->user()->associate($data['user_id']);

        $cannedMessage->save();

        return $cannedMessage;
    }
}

//Generated 27-10-2023 10:55:45
