<?php

namespace App\Actions\BlockGroups;

use App\Models\BlockGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateBlockGroupShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('block-group-' . $id, function () use ($id) {
            return $this->respond(BlockGroup::findOrFail($id));
        });
    }
}

//Generated 16-10-2023 10:39:10
