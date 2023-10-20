<?php

namespace App\Actions\Statuses;

use App\Models\Status;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateStatusShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('status-' . $id, function () use ($id) {
            return $this->respond(Status::findOrFail($id));
        });
    }
}

//Generated 09-10-2023 12:35:29
