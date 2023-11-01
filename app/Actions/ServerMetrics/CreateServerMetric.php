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
        $data['server_id'] = Server::where('ip', $data['ip_address'])->first()->id ?? null;
        $data['company_id'] = 1;
        $data['timestamp'] = now()->timestamp;
        $data['uptime'] = now()->timestamp;

        $serverMetric = new ServerMetric($data);

		if(isset($data['server_id'])) {
			$serverMetric->server()->associate($data['server_id']);
		}

		if(isset($data['company_id'])) {
			$serverMetric->company()->associate($data['company_id']);
		}

        $serverMetric->save();

        return $serverMetric;
    }
}

//Generated 27-10-2023 10:55:27
