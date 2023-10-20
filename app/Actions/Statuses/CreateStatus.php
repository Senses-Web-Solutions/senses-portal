<?php

namespace App\Actions\Statuses;

use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class CreateStatus
{
    use QueueableAction;

    public function execute(array $data)
    {
        $status = new Status($data);

        $status->save();

        return $status;
    }
}

//Generated 09-10-2023 12:35:29
