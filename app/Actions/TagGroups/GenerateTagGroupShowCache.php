<?php

namespace App\Actions\TagGroups;

use App\Models\TagGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateTagGroupShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('tag-group-' . $id, function () use ($id) {
            return $this->respond(TagGroup::findOrFail($id));
        });
    }
}

//Generated 09-10-2023 10:26:55
