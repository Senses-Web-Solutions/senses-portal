<?php

namespace App\Actions\ChatUsers;

use App\Models\ChatUser;
use Spatie\QueueableAction\QueueableAction;

class DeleteChatUser
{
    use QueueableAction;

    public function execute(int $id)
    {
        $chatUser = ChatUser::findOrFail($id);

        $chatUser->delete();

        return $chatUser;
    }
}

//Generated 27-10-2023 10:55:45
