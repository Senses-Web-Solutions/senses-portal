<?php

namespace App\Actions\TagGroups;

use App\Models\TagGroup;
use Spatie\QueueableAction\QueueableAction;

class UpdateTagGroup
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $tagGroup = TagGroup::findOrFail($id);

        $tagGroup->fill($data);

        if (!$tagGroup->isDirty()) {
            $tagGroup->emitUpdated();
        }

        $tagGroup->save();

        return $tagGroup;
    }
}

//Generated 09-10-2023 10:26:55
