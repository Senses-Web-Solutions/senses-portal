<?php

namespace App\Actions\Users;

use App\Models\User;
use Spatie\QueueableAction\QueueableAction;

class UpdateUser
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $user = User::findOrFail($id);

        $user->fill($data);

        if (!$user->isDirty()) {
            $user->emitUpdated();
        }

        $user->save();

        return $user;
    }
}

//Generated 10-10-2023 10:05:12
