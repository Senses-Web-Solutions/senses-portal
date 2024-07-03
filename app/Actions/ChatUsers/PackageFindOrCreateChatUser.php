<?php

namespace App\Actions\ChatUsers;

use App\Models\ChatUser;
use App\Actions\ChatUsers\CreateChatUser;
use Spatie\QueueableAction\QueueableAction;

class PackageFindOrCreateChatUser
{
    use QueueableAction;

    public function execute(array $data)
    {
        if (isset($data['uuid'])) {
            $chatUser = ChatUser::where('uuid', $data['uuid'])->first();
            if ($chatUser) {
                return $chatUser;
            }
        }

        $chatUser = app(CreateChatUser::class)->execute($data);

        return $chatUser;
    }
}

//Generated 27-10-2023 10:55:45
