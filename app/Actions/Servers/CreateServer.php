<?php

namespace App\Actions\Servers;

use App\Models\Server;
use Spatie\QueueableAction\QueueableAction;

class CreateServer
{
    use QueueableAction;

    public function execute(array $data)
    {
        $server = new Server($data);

		if(isset($data['company_id'])) {
			$server->company()->associate($data['company_id']);
		}

        $server->save();

        return $server;
    }
}

//Generated 01-11-2023 11:27:41
