<?php

namespace App\Actions\ServerMetrics;

use App\Models\ServerMetric;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class GenerateServerMetricShowCache
{
    use QueueableAction, ApiResponse;

    public $queue = 'low';

    public function execute(int $id)
    {
        return TaggedCache::responseForever('server-metric-' . $id, function () use ($id) {
            return $this->respond(ServerMetric::findOrFail($id));
        });
    }
}

//Generated 01-11-2023 11:22:36
