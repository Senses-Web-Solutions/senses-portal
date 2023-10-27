<?php

namespace App\Actions\Servers;

use App\Models\Server;
use Spatie\QueueableAction\QueueableAction;

class UpdateServer
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $server = Server::findOrFail($id);

        $server->fill($data);

		if(isset($data['company_id'])) {
			$server->company()->associate($data['company_id']);
		}

        if (!$server->isDirty()) {
            $server->emitUpdated();
        }

        $server->save();

        return $server;
    }
}

//Generated 27-10-2023 10:53:42
