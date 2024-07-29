<?php

namespace app\Actions\CannedMessages;

use App\Models\CannedMessage;
use Spatie\QueueableAction\QueueableAction;

class DeleteCannedMessage
{
    use QueueableAction;

    public function execute(int $id)
    {
        $cannedMessage = CannedMessage::findOrFail($id);

        $cannedMessage->delete();

        return $cannedMessage;
    }
}

//Generated 27-10-2023 10:55:45
