<?php

namespace App\Actions\Revenues;

use App\Models\Revenue;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateRevenueShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('revenue-' . $id, function () use ($id) {
            return $this->respond(Revenue::findOrFail($id));
        });
    }
}

//Generated 04-11-2023 16:09:26
