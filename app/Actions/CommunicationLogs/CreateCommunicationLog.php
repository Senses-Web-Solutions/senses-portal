<?php

namespace App\Actions\CommunicationLogs;

use App\Models\CommunicationLog;
use Spatie\QueueableAction\QueueableAction;

class CreateCommunicationLog
{
    use QueueableAction;

    public function execute(array $data)
    {
        $communicationLog = new CommunicationLog($data);

		if(isset($data['company_id'])) {
			$communicationLog->company()->associate($data['company_id']);
		}

        $communicationLog->save();

        return $communicationLog;
    }
}

//Generated 04-11-2023 16:09:50
