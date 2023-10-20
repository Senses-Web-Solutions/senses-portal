<?php

namespace App\Actions\Users;

use App\Models\User;
use Spatie\QueueableAction\QueueableAction;

class DeleteUser
{
    use QueueableAction;

    public function execute(int $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $user;
    }
}

//Generated 10-10-2023 10:05:12
