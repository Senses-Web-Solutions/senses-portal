<?php

namespace App\Actions\TagGroups;

use App\Models\TagGroup;
use Spatie\QueueableAction\QueueableAction;

class DeleteTagGroup
{
    use QueueableAction;

    public function execute(int $id)
    {
        $tagGroup = TagGroup::findOrFail($id);

        $tagGroup->delete();

        return $tagGroup;
    }
}

//Generated 09-10-2023 10:26:55
