<?php

namespace App\Actions\StatusGroups;

use App\Models\StatusGroup;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateStatusGroupShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('status-group-' . $id, function () use ($id) {
            return $this->respond(StatusGroup::findOrFail($id));
        });
    }
}

//Generated 09-10-2023 12:05:02
