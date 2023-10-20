<?php

namespace App\Actions\Statuses;

use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class DeleteStatus
{
    use QueueableAction;

    public function execute(int $id)
    {
        $status = Status::findOrFail($id);

        $status->delete();

        return $status;
    }
}

//Generated 09-10-2023 12:35:29
