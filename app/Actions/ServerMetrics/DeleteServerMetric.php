<?php

namespace App\Actions\ServerMetrics;

use App\Models\ServerMetric;
use Spatie\QueueableAction\QueueableAction;

class DeleteServerMetric
{
    use QueueableAction;

    public function execute(int $id)
    {
        $serverMetric = ServerMetric::findOrFail($id);

        $serverMetric->delete();

        return $serverMetric;
    }
}

//Generated 01-11-2023 11:22:36
