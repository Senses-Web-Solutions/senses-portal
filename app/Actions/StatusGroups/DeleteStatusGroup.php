<?php

namespace App\Actions\StatusGroups;

use App\Models\StatusGroup;
use Spatie\QueueableAction\QueueableAction;

class DeleteStatusGroup
{
    use QueueableAction;

    public function execute(int $id)
    {
        $statusGroup = StatusGroup::findOrFail($id);

        $statusGroup->delete();

        return $statusGroup;
    }
}

//Generated 09-10-2023 12:05:02
