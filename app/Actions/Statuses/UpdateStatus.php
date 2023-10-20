<?php

namespace App\Actions\Statuses;

use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class UpdateStatus
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $status = Status::findOrFail($id);

        $status->fill($data);

        if (!$status->isDirty()) {
            $status->emitUpdated();
        }

        $status->save();

        return $status;
    }
}

//Generated 09-10-2023 12:35:29
