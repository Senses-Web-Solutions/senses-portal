<?php

namespace App\Actions\Chats;

use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class DeleteChat
{
    use QueueableAction;

    public function execute(int $id)
    {
        $company = Chat::findOrFail($id);

        $company->delete();

        return $company;
    }
}

//Generated 27-10-2023 10:55:45
