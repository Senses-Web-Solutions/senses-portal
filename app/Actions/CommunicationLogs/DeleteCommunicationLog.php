<?php

namespace App\Actions\CommunicationLogs;

use App\Models\CommunicationLog;
use Spatie\QueueableAction\QueueableAction;

class DeleteCommunicationLog
{
    use QueueableAction;

    public function execute(int $id)
    {
        $communicationLog = CommunicationLog::findOrFail($id);

        $communicationLog->delete();

        return $communicationLog;
    }
}

//Generated 04-11-2023 16:09:50
