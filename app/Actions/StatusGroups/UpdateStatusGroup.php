<?php

namespace App\Actions\StatusGroups;

use App\Models\StatusGroup;
use Spatie\QueueableAction\QueueableAction;

class UpdateStatusGroup
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $statusGroup = StatusGroup::findOrFail($id);

        $statusGroup->fill($data);

        if (!$statusGroup->isDirty()) {
            $statusGroup->emitUpdated();
        }

        $statusGroup->save();

        return $statusGroup;
    }
}

//Generated 09-10-2023 12:05:02
