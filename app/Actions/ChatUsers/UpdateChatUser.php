<?php

namespace App\Actions\ChatUsers;

use App\Models\ChatUser;
use Spatie\QueueableAction\QueueableAction;

class UpdateChatUser
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $chatUser = ChatUser::findOrFail($id);

        $chatUser->fill($data);

        $chatUser->company()->associate($data['company_id']);

        if (!$chatUser->isDirty()) {
            $chatUser->emitUpdated();
        }

        $chatUser->save();

        return $chatUser;
    }
}

//Generated 27-10-2023 10:55:45
