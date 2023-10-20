<?php

namespace App\Actions\Users;

use App\Models\User;
use Spatie\QueueableAction\QueueableAction;

class CreateUser
{
    use QueueableAction;

    public function execute(array $data)
    {
        $user = new User($data);

        $user->save();

        return $user;
    }
}

//Generated 10-10-2023 10:05:12
