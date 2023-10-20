<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use Spatie\QueueableAction\QueueableAction;

class DeleteTag
{
    use QueueableAction;

    public function execute(int $id)
    {
        $tag = Tag::findOrFail($id);

        $tag->delete();

        return $tag;
    }
}

//Generated 09-10-2023 10:18:19
