<?php

namespace App\Actions\TagGroups;

use App\Models\TagGroup;
use Spatie\QueueableAction\QueueableAction;

class CreateTagGroup
{
    use QueueableAction;

    public function execute(array $data)
    {
        $tagGroup = new TagGroup($data);

        $tagGroup->save();

        return $tagGroup;
    }
}

//Generated 09-10-2023 10:26:55
