<?php

namespace App\Actions\ServerMetrics;

use App\Models\Server;
use App\Models\ServerMetric;
use Spatie\QueueableAction\QueueableAction;

class CreateServerMetric
{
    use QueueableAction;

    public function execute(array $data)
    {
        $serverMetric = new ServerMetric($data);

		if (isset($data['server_id'])) {
			$serverMetric->server()->associate($data['server_id']);
			$serverMetric->company()->associate($serverMetric->server->company_id);
		}

        $serverMetric->save();

        return $serverMetric;
    }
}

//Generated 01-11-2023 11:22:36
