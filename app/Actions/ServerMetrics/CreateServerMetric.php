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
        if (!isset($data['timestamp'])) {
            $data['timestamp'] = now()->timestamp;
        }

        $serverData = [
            "hostname" => $data['hostname'],
            "ip_address" => $data['ip_address'],
            "os" => $data['os'],
            "distro" => $data['distro'],
            "distro_version" => $data['distro_version'],
            "architecture" => $data['architecture'],
            "kernel" => $data['kernel'],
            "kernel_version" => $data['kernel_version'],

            "cpu_cores" => $data['cpu_cores'],
            "cpu_threads" => $data['cpu_threads'],
        ];

        $server = Server::find($data['server_id']);

        foreach ($serverData as $key => $value) {
            if ($server->{$key} != $serverData[$key]) {
                $server->update($serverData);
                break;
            }
        }

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
