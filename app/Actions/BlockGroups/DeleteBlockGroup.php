<?php

namespace App\Actions\BlockGroups;

use App\Models\BlockGroup;
use Spatie\QueueableAction\QueueableAction;

class DeleteBlockGroup
{
    use QueueableAction;

    public function execute(int $id)
    {
        $blockGroup = BlockGroup::findOrFail($id);

        $blockGroup->delete();

        return $blockGroup;
    }
}

//Generated 16-10-2023 10:39:10
