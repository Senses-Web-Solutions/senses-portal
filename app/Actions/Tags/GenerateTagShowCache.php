<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateTagShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('tag-' . $id, function () use ($id) {
            return $this->respond(Tag::with('tagGroups')->findOrFail($id));
        });
    }
}

//Generated 09-10-2023 10:18:19
