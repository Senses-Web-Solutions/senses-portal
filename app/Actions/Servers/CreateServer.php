<?php

namespace App\Actions\Servers;

use App\Actions\ApiTokens\CreateApiToken;
use App\Models\Server;
use Spatie\QueueableAction\QueueableAction;

class CreateServer
{
    use QueueableAction;

    public function execute(array $data)
    {
        $server = new Server($data);

		if (isset($data['company_id'])) {
			$server->company()->associate($data['company_id']);
		}

        $server->save();

        $apiToken = app(CreateApiToken::class)->execute([
            'title' => 'Automatically Generated API Token',
            'server_id' => $server->id,
        ]);

        $server['token'] = $apiToken['token'];

        return $server;
    }
}

//Generated 01-11-2023 11:27:41
