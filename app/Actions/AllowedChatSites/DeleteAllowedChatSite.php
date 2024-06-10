<?php

namespace App\Actions\AllowedChatSites;

use App\Models\AllowedChatSite;
use Spatie\QueueableAction\QueueableAction;

class DeleteAllowedChatSite
{
    use QueueableAction;

    public function execute(int $id)
    {
        $allowedChatSite = AllowedChatSite::findOrFail($id);

        $allowedChatSite->delete();

        return $allowedChatSite;
    }
}

//Generated 27-10-2023 10:55:45
