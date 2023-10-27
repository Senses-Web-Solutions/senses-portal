<?php

namespace App\Actions\ServerMetrics;

use App\Models\ServerMetric;
use Spatie\QueueableAction\QueueableAction;

class UpdateServerMetric
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $serverMetric = ServerMetric::findOrFail($id);

        $serverMetric->fill($data);

		if(isset($data['server_id'])) {
			$serverMetric->server()->associate($data['server_id']);
		}

		if(isset($data['company_id'])) {
			$serverMetric->company()->associate($data['company_id']);
		}

        if (!$serverMetric->isDirty()) {
            $serverMetric->emitUpdated();
        }

        $serverMetric->save();

        return $serverMetric;
    }
}

//Generated 27-10-2023 10:55:27
