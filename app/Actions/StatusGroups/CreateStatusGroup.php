<?php

namespace App\Actions\StatusGroups;

use App\Models\StatusGroup;
use Spatie\QueueableAction\QueueableAction;

class CreateStatusGroup
{
    use QueueableAction;

    public function execute(array $data)
    {
        $statusGroup = new StatusGroup($data);

        $statusGroup->save();

        return $statusGroup;
    }
}

//Generated 09-10-2023 12:05:02
