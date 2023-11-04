<?php

namespace App\Actions\Revenues;

use App\Models\Revenue;
use Spatie\QueueableAction\QueueableAction;

class DeleteRevenue
{
    use QueueableAction;

    public function execute(int $id)
    {
        $revenue = Revenue::findOrFail($id);

        $revenue->delete();

        return $revenue;
    }
}

//Generated 04-11-2023 16:09:26
