<?php

namespace App\Actions\CommunicationLogs;

use App\Models\CommunicationLog;
use Spatie\QueueableAction\QueueableAction;

class UpdateCommunicationLog
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $communicationLog = CommunicationLog::findOrFail($id);

        $communicationLog->fill($data);

		if(isset($data['company_id'])) {
			$communicationLog->company()->associate($data['company_id']);
		}

        if (!$communicationLog->isDirty()) {
            $communicationLog->emitUpdated();
        }

        $communicationLog->save();

        return $communicationLog;
    }
}

//Generated 04-11-2023 16:09:50
