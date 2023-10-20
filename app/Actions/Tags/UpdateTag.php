<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use Spatie\QueueableAction\QueueableAction;

class UpdateTag
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $tag = Tag::findOrFail($id);

        $tag->fill($data);

        if(isset($data['tag_group_ids'])) {
            $tag->tagGroups()->sync($data['tag_group_ids']);
        }

        if (!$tag->isDirty()) {
            $tag->emitUpdated();
        }

        $tag->save();

        return $tag;
    }
}

//Generated 13-10-2021 11:49:44
