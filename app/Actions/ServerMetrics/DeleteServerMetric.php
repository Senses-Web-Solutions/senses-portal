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

//Generated 27-10-2023 10:55:27
